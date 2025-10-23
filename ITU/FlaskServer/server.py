#author - xdomra00, xivano08 

from flask import Flask, request, jsonify, url_for
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
import json, random, string
import uuid
from fluxGenerator import spit_url
from sqlalchemy import text
from fuzzywuzzy import fuzz
from datetime import datetime


app = Flask(__name__)
CORS(app, supports_credentials=True)

app.config['SQLALCHEMY_DATABASE_URI'] = 'sqlite:///test.db'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db = SQLAlchemy(app)

class Users(db.Model):
    __tablename__ = 'users'
    id = db.Column(db.Integer, primary_key=True)
    email = db.Column(db.String(120), unique=True, nullable=True)
    password = db.Column(db.String(40), nullable=True)
    name = db.Column(db.String(40), nullable=False)
    current_game_id = db.Column(db.BigInteger, db.ForeignKey('playedgame.id'), nullable=True)
    fantom = db.Column(db.Boolean, default=False)
    ready_for_game = db.Column(db.Boolean, default=False)
    host = db.Column(db.Boolean, default=False)

    current_played_game = db.relationship('PlayedGame', backref='users')

    def __repr__(self):
        return f'User {self.username}'


class AllGames(db.Model):
    __tablename__ = 'allgames'
    id = db.Column(db.Integer, primary_key=True)
    type = db.Column(db.Enum('Riddle', 'Recycle', name='game_types'))
    json_data = db.Column(db.String(5000), nullable=True)



class PlayedGame(db.Model):
    __tablename__ = 'playedgame'
    id = db.Column(db.Integer, primary_key=True)
    game_id = db.Column(db.BigInteger, db.ForeignKey('allgames.id'))
    state = db.Column(db.Enum('Preparing', 'Playing', 'Finished', name='game_states'))  # Enum for game state
    party_code = db.Column(db.String(5), nullable=False)
    private = db.Column(db.Boolean, default=False)
    stage = db.Column(db.Integer, nullable=False, default=0)
    temp_json_data = db.Column(db.String(5000), nullable=True)
    chat_json_data = db.Column(db.String(5000), nullable=True)

    game_ref = db.relationship('AllGames', backref='played_games')
    # players = db.relationship('Users', backref='played_game')

    def __repr__(self):
        return f'PlayedGame {self.name}'


class GameStatistics(db.Model):
    __tablename__ = 'gamestatistics'
    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.BigInteger, db.ForeignKey('users.id'))
    played_game_id = db.Column(db.BigInteger, db.ForeignKey('playedgame.id'))
    points_earned = db.Column(db.BigInteger, nullable=False)

    user = db.relationship('Users', backref='statistics')
    played_game = db.relationship('PlayedGame', backref='statistics')

    def __repr__(self):
        return f'GameStatistics {self.points_earned}'



def insert_default_games():
    if AllGames.query.count() == 0:
        game1 = AllGames(type='Riddle', json_data=json.dumps({'src': 'RiddleGameDemonstration.png'}))
        game2 = AllGames(type='Recycle', json_data=json.dumps({'src': 'RecycleGameDemonstration.png'}))

        db.session.add(game1)
        db.session.add(game2)

        db.session.commit()

def putGameLogic(type, logic):
    if type == 'Riddle':
        logic['secTimer'] = 120
        logic['history'] = {}
        logic['users'] = []
        logic['rounds'] = {}
        logic['stages'] = 2

    elif type == 'Recycle':
        logic['secTimer'] = 30
        logic['history'] = []


def generate_game_code():
    return ''.join(random.choices(string.ascii_uppercase, k=5))

with app.app_context():
    db.drop_all()
     # games = AllGames.query.filter(AllGames.json_data == None).all()
    db.create_all()
    insert_default_games()







#Routes#################################################################################
@app.route('/api/fantom_user') 
def get_fantom_user():
    db.session.execute(text('BEGIN EXCLUSIVE'))
    id = request.cookies.get('userId')
    fantom_user = None
    if id:
        fantom_user = Users.query.get(int(id))
    
    if not fantom_user:
        fantom_user = Users(name='#user', fantom=True)
        db.session.add(fantom_user)
        db.session.commit()
        fantom_user.name = f"{fantom_user.id:04d}" + fantom_user.name
        db.session.commit()

    user_data = {
        'id': fantom_user.id,
        'name': fantom_user.name,
        'current_game_id': fantom_user.current_game_id,
    }

    response = jsonify(user_data)
    response.set_cookie('userId', value=str(user_data['id']), secure=False)


    return response

@app.route('/api/user')
def get_user():
    db.session.execute(text('BEGIN EXCLUSIVE'))
    id = request.cookies.get('userId')
    user = None
    if id:
        user = Users.query.get(int(id))
    if not user:
        user = Users(name='#user', fantom=True)
        db.session.add(user)
        db.session.commit()
        user.name = f"{user.id:04d}" + user.name
        db.session.commit()

    user_data = {
        'id': user.id,
        'name': user.name,
        'fantom' : user.fantom
    }

    response = jsonify(user_data)
    response.set_cookie('userId', value=str(user_data['id']), secure=False)

    return response

@app.route('/api/register', methods=["POST"])
def register_user():
    db.session.execute(text('BEGIN EXCLUSIVE'))
    data = request.get_json()
    if not data or 'name' not in data or 'email' not in data or 'password' not in data:
        return jsonify({}), 400
    name = data['name']
    email = data['email']
    password = data['password']

    user = Users.query.filter_by(email=email).first()
    if user:
        return jsonify({}), 400

    new_user = Users(name=name, email=email, password=password, fantom=False)
    db.session.add(new_user)
    db.session.commit()

    data = {
        'id':new_user.id,
        'name':new_user.name,
        'fantom':new_user.fantom
    }

    response = jsonify(data)
    response.set_cookie('userId', value=str(new_user.id), secure=False)

    return response


@app.route('/api/login', methods=["POST"])
def login_user():
    db.session.execute(text('BEGIN EXCLUSIVE'))
    data = request.get_json()
    if not data or 'email' not in data or 'password' not in data:
        return jsonify({"error": "Invalid data. Missing name, email, or password."}), 400

    email = data['email']
    password = data['password']
    user = Users.query.filter_by(email=email).first()
    if not user:
        return jsonify({}), 400 
    if user.password != password:
        return jsonify({}), 400
    
    db.session.add(user)
    db.session.commit()

    data = {
        'id':user.id,
        'name':user.name,
        'fantom':user.fantom
    }

    response = jsonify(data)
    response.set_cookie('userId', value=str(user.id), secure=False)

    return response


@app.route('/api/all_games') 
def get_all_games():
    games = AllGames.query.all()

    games_data = [
        {
            'id': str(game.id),
            'name': game.type,
        }   
        for game in games
    ]

    return jsonify(games_data)

@app.route('/api/game_info') 
def get_game_info():
    game = AllGames.query.get(request.args['id'])
    if game == None:
        return jsonify({}), 404
    
    src = url_for('static', filename=f'{json.loads(game.json_data)["src"]}', _external=True)
    data = {
        'name': game.type,
        'routeName': game.type, 
        'src': src,
    }
    return jsonify(data)

    

@app.route('/api/played_game') 
def get_played_game(): 
    game = PlayedGame.query.get(request.args['id'])
    if game == None:
        return jsonify({}), 404
    data = {
        "id": game.id,
        "game_id": game.game_id,
        "state": game.state,
        "party_code": game.party_code,
        "private": game.private,
        "name": game.game_ref.type,
        "logic": game.temp_json_data,
        "stage": game.stage,
        "chat": game.chat_json_data
    }
    return jsonify(data)

#maybe not doing it
# @app.route('/api/set_played_game_logic', methods=["POST"]) 
# def post_set_played_game_logic(): 
#     data = request.get_json()
#     game = PlayedGame.query.get(data['gameId'])
#     if game == None:
#         return jsonify({}), 404

#     return jsonify(data)


@app.route('/api/create_game_for', methods=["POST"]) 
def post_create_game_for():
    db.session.execute(text('BEGIN EXCLUSIVE'))
    data = request.get_json() 
    host_id = data['userId']
    new_game_id = data['gameId']
    is_private = data['private']
    host = Users.query.get(host_id)

    if not host:
        db.session.rollback()
        return jsonify({}), 404
    
    #if user already has game - not allow to create game
    if(host.current_game_id != None):
        db.session.rollback()
        return jsonify({}), 404
    #if user has game with status: preparing - give host to someone else or delete game
    game = PlayedGame.query.get(host.current_game_id)
    if(game):
        db.session.rollback()
        return jsonify({}), 200

    #some day - check if party code is not unique
    new_game = PlayedGame(
        game_id=new_game_id, 
        state="Preparing",
        party_code=generate_game_code(),
        private=is_private,
        stage=0)

    db.session.add(new_game)
    db.session.commit()

    logic = {}
    putGameLogic(new_game.game_ref.type, logic)
    new_game.temp_json_data = json.dumps(logic)
    chat = {}
    chat['messages'] = []
    new_game.chat_json_data = json.dumps(chat)
    db.session.commit()

    host.current_game_id = new_game.id
    host.ready_for_game = False
    host.host = True

    db.session.add(host)
    db.session.commit()

    return jsonify({}), 200


@app.route('/api/game_players') 
def get_game_players():
    game = PlayedGame.query.get(request.args['id'])
    if game == None:
        return jsonify({}), 404
    users = game.users
    data = [{
        'id': user.id,
        'name': user.name,
        'current_game_id': user.current_game_id,
        'ready_for_game': user.ready_for_game,
        'host': user.host,
    } for user in users]

    return jsonify(data)


@app.route('/api/disconnect_player', methods=["POST"]) 
def post_disconnect_player():
    data = request.get_json()  
    player = Users.query.get(data['id'])
    if not player:
        return jsonify({}), 404
    
    game = player.current_played_game

    player.current_game_id = None
    player.host = False
    player.ready_for_game = False

    db.session.add(player)
    db.session.commit()


    #delete game if not users left and if game did not finish
    if game.state != 'Finished':
        if not game.users:
            db.session.delete(game)
            db.session.commit()
        else:
            game.users[0].host = True
            db.session.add(game.users[0])
            db.session.commit()
    

    return jsonify({}), 200


@app.route('/api/join_game', methods=["POST"]) 
def post_join_game():
    data = request.get_json()
    player = Users.query.get(data['userId'])
    if not player:
        return jsonify({}), 404
    
    game = PlayedGame.query.filter(
        PlayedGame.party_code == data['code'].upper(),
        PlayedGame.state.in_(['Preparing', 'Finished'])
    ).first()

    if not game:
        return jsonify({}), 404
    
    ##!!no logic about private game!!. it belongs to random join, not join by code

    player.current_game_id = game.id
    if(not player.host):#host != None and host != True
        player.host = False
    player.ready_for_game = False

    db.session.add(player)
    db.session.commit()


    return jsonify({"routeName": game.game_ref.type}), 200




@app.route('/api/toggle_ready') 
def get_toggle_ready():
    player = Users.query.get(request.args['userId'])
    if not player:
        return jsonify({}), 404
    
    game = player.current_played_game
    if not game:
        return jsonify({}), 404


    player.ready_for_game = not player.ready_for_game
    db.session.add(player)
    db.session.commit()

    
    allReady = player.ready_for_game
    for u in game.users:
        allReady = allReady and u.ready_for_game
               
    if(allReady):
        set_next_stage_and_save(game)

    return jsonify({}), 200


def set_next_stage_and_save(game):
    if(game.state != 'Finished'):    
        game.state = 'Playing'
        for u in game.users:
            db.session.add(u)
            u.ready_for_game = False    
        db.session.commit()
        game.stage += 1

    db.session.add(game)
    db.session.commit()


@app.route('/api/finish_game', methods=["POST"]) 
def post_finish_game():
    data = request.get_json()
    game = PlayedGame.query.get(data['id'])
    if not game:
        return jsonify({}), 404

    if(game.state != 'Finished'):
        game.state = 'Finished'
        db.session.add(game)
        db.session.commit()

    return jsonify({}), 200

#general game logic

#returns path to generated image

async def generate_image(prompt, uniqueGameId, gameType) -> str:
    # path = './static'
    # if gameType == 'Recycle':
    #     path += '/recycleGamesContent'
    # elif gameType == 'Riddle':
    #     path += '/riddleGamesContent'
    
    # path += f'/game-{uniqueGameId}-{uuid.uuid4()}.jpg'

    # path =  "generatedExample.jpg"
    # src = url_for('static', filename=path, _external=True)
    url = await spit_url(prompt)
    return url

def get_fake_src():
    path =  "generatedExample.jpg"
    src = url_for('static', filename=path, _external=True)
    return src

def get_fake_real_src():
    path =  "realGenerated.png"
    src = url_for('static', filename=path, _external=True)
    return src

#specific game logic
#Recycle

#Riddle

#specific game logic requests
#Recycle

submitPromptTrace = 'submit prompt'
getImageTrace = 'get image'
trace = 'user{}Called'

@app.route('/api/recycle_submit_prompt', methods=['POST']) 
def post_recycle_submit_prompt():
    db.session.execute(text('BEGIN EXCLUSIVE'))
    data = request.get_json()
    user = Users.query.get(data['userId'])
    if not user:
        db.session.rollback()
        return jsonify({}), 404
    
    game = PlayedGame.query.filter_by(id=user.current_played_game.id).with_for_update().first()#user.current_played_game
    if not game:
        db.session.rollback()
        return jsonify({}), 404

    logic = json.loads(game.temp_json_data)

    resubmition = False
    #protection: if function called wrong number of times - dont react
    #call rules:  submit_prompt -----> prepare_describing -----> submit_prompt -----> prepare_describing ...
    userTrace = trace.format(data['userId'])
    if(not logic.get(userTrace)):#no trace - good, we are first function as it has to be
        logic[userTrace] = submitPromptTrace
    elif logic[userTrace] == getImageTrace:
        logic[userTrace] = submitPromptTrace#change trace: we called "submit prompt" after getting image
    elif logic[userTrace] == submitPromptTrace:#prompt was already submitted but we can resubmit it
        resubmition = True
    else:
        db.session.rollback()
        return jsonify({}), 404

    

    #generate image by prompt
    generatedSrc = get_fake_src()# generate_image(data['prompt'], game.id, game.game_ref.type) 

    #look for last user in "users" history unit - its user who got image to describe
    #else - create new unit
    #   last user in unit history has to be unique. this statement is satisfied by promt assignments
    #   implenemted as derangements. that means each time users get from each other unique images (bijection)
    #   simplier logic - game is not made for you to describe two images at once. thats why 2 people cant assign you 2 pics

    #set logic
    foundUnit = None
    for unit in logic['history']:
        if int(unit['users'][-1]) == int(user.id):
            foundUnit = unit
            break

    if foundUnit:
        if resubmition:
            foundUnit['prompts'][-1] = (str(data['prompt']))
            foundUnit['generatedSrc'][-1] = generatedSrc
            foundUnit['names'][-1] = str(user.name)
            foundUnit['taken'] = False
        else:
            foundUnit['prompts'].append(str(data['prompt']))
            foundUnit['generatedSrc'].append(generatedSrc)
            foundUnit['names'].append(str(user.name))
            foundUnit['taken'] = False
    else:
        logic['history'].append({
            'prompts': [data['prompt']], 
            'generatedSrc': [generatedSrc], 
            'users': [int(user.id)],
            'names': [str(user.name)],
            'taken': False})
        

    game.temp_json_data = json.dumps(logic)
    db.session.add(game)
    db.session.commit()

    return jsonify({}), 200


@app.route('/api/recycle_prepare_describing') 
async def get_recycle_prepare_describing():
    db.session.execute(text('BEGIN EXCLUSIVE'))
    user = Users.query.get(request.args['userId'])
    if not user:
        print("PREP DESC USER 404")
        db.session.rollback()
        return jsonify({}), 404
    
    game = PlayedGame.query.filter_by(id=user.current_played_game.id).with_for_update().first()#user.current_played_game
    if not game:
        print("PREP DESC GAME 404")
        db.session.rollback()
        return jsonify({}), 404
    
    logic = json.loads(game.temp_json_data)

    #protection
    #call rules:  submit_prompt -----> prepare_describing -----> submit_prompt -----> prepare_describing ...
    userTrace = trace.format(request.args['userId'])

    #1. call order
    if(not logic.get(userTrace)):#no trace - bad, we are not the first function user had to call
        print("PREP DESC NOTRACE 404"+f" {game.temp_json_data}")
        db.session.rollback()
        return jsonify({}), 404
    elif logic[userTrace] != submitPromptTrace:#trace is present but specified wrong
        print("PREP DESC WRONGTRACE 404")
        db.session.rollback()
        return jsonify({}), 404
    else:#if order is ok we modify trace
        logic[userTrace] = getImageTrace


    #2. do not include other bad call orders to search
    foundUnit = None
    lastChanceUnit = None
    for unit in logic['history']:
        if(unit['taken']):
            continue
        elif(int(unit['users'][-1]) == int(user.id)):
            lastChanceUnit = unit
        else:
            foundUnit = unit
            unit['taken'] = True
            break

    src = None
    if foundUnit:
        foundUnit['users'].append(int(user.id))
        #generatedSrc = get_fake_real_src()
        generatedSrc = await generate_image(foundUnit['prompts'][-1], game.id, game.game_ref.type)
        foundUnit['generatedSrc'][-1] = generatedSrc#see - submit prompt
        src = foundUnit['generatedSrc'][-1]
    else:
        lastChanceUnit['users'].append(int(user.id))
        #generatedSrc = get_fake_real_src()
        generatedSrc = await generate_image(lastChanceUnit['prompts'][-1], game.id, game.game_ref.type)
        lastChanceUnit['generatedSrc'][-1] = generatedSrc#see - submit prompt
        src = lastChanceUnit['generatedSrc'][-1]

    game.temp_json_data = json.dumps(logic)
    db.session.add(game)
    db.session.commit()

    return jsonify({"src": src}), 200



@app.route('/api/recycle_game_results') 
def get_recycle_game_results():
    game = PlayedGame.query.get(request.args['gameId'])
    if not game:
        return jsonify({}), 404
    
    results = {'results':[]}
    logic = json.loads(game.temp_json_data)
    #protection
    #do not include bad call orders to results
    for unit in logic['history']:
            l1 = len(unit['prompts'])
            l2 = len(unit['generatedSrc'])
          #  l3 = len(unit['users'])
            if(l1 != l2):#l1 != l3 - do not need
                continue
            results['results'].append(unit)

    return jsonify(results), 200



#Riddle

def mix_players_order(list):
    list = list[1:] + list[:1] 
    return list

@app.route('/api/riddle_submit_prompt', methods=['POST']) 
async def post_riddle_submit_prompt():
    db.session.execute(text('BEGIN EXCLUSIVE'))
    data = request.get_json()
    user = Users.query.get(data['userId'])
    if not user:
        db.session.rollback()
        return jsonify({}), 404
    
    game = PlayedGame.query.filter_by(id=user.current_played_game.id).with_for_update().first()#user.current_played_game
    if not game:
        db.session.rollback()
        return jsonify({}), 404
    
    logic = json.loads(game.temp_json_data)


    prompt = data['prompt']
    #generatedSrc = get_fake_real_src()
    generatedSrc = await generate_image(data['prompt'], game.id, game.game_ref.type)

    if not (str(user.id) in logic["history"]):
        logic["history"][str(user.id)] = {}
        logic["history"][str(user.id)]['sourceImg'] = {}
        logic['stages'] +=1
        logic['users'].append(user.id)
    

    logic["history"][str(user.id)]['sourceImg']['name'] = user.name
    logic["history"][str(user.id)]['sourceImg']['prompt'] = prompt
    logic["history"][str(user.id)]['sourceImg']['url'] = generatedSrc

    logic["history"][str(user.id)]['copies'] = {}
    logic["history"][str(user.id)]['winner'] = None


    game.temp_json_data = json.dumps(logic)
    db.session.add(game)
    db.session.commit()

    return jsonify({}), 200


@app.route('/api/riddle_get_image') 
async def riddle_get_image():
    db.session.execute(text('BEGIN EXCLUSIVE'))
    user = Users.query.get(request.args['userId'])
    if not user:
        db.session.rollback()
        return jsonify({}), 404
    
    game = PlayedGame.query.filter_by(id=user.current_played_game.id).with_for_update().first()#user.current_played_game
    if not game:
        db.session.rollback()
        return jsonify({}), 404
    
    logic = json.loads(game.temp_json_data)

    entry = logic['history'][str(user.id)]['sourceImg']

    prompt = entry['prompt']
    #generatedSrc = await generate_image(prompt, game.id, game.game_ref.type)
    #entry['url'] = generatedSrc

    game.temp_json_data = json.dumps(logic)
    #print(generatedSrc)
    print(logic)

    logic = json.loads(game.temp_json_data)

    if logic['rounds'] == {}:
        logic = await get_order(logic)
        print("GOT ORDER")
        game.temp_json_data = json.dumps(logic)
        db.session.add(game)
        db.session.commit()
    else:
        print("DIDNT GET ORDER")

    logic = json.loads(game.temp_json_data)

    guess_img_id = logic['rounds'][str(user.id)][game.stage-2]
    entry = logic['history'][str(guess_img_id)]['sourceImg']
    
    guess_img = entry['url']

    game.temp_json_data = json.dumps(logic)
    db.session.add(game)
    db.session.commit()

    return jsonify({"src": guess_img}), 200

async def get_order(logic):
    random.shuffle(logic["users"])

    user_rounds = {}
    for i, userid in enumerate(logic["users"]):
        other_users = logic["users"][:i] + logic["users"][i+1:]
        user_rounds[userid] = other_users
    logic["rounds"] = user_rounds
    return logic

@app.route('/api/riddle_guess_image', methods=['POST'])
async def guess_img():
    db.session.execute(text('BEGIN EXCLUSIVE'))
    data = request.get_json()
    user = Users.query.get(data['userId'])
    if not user:
        db.session.rollback()
        return jsonify({}), 404
    
    game = PlayedGame.query.filter_by(id=user.current_played_game.id).with_for_update().first()#user.current_played_game
    if not game:
        db.session.rollback()
        return jsonify({}), 404
    
    logic = json.loads(game.temp_json_data)

    original_prompt_userid = logic['rounds'][str(user.id)][game.stage-2]
    print(user.id)
    print(original_prompt_userid)

    original_prompt = logic['history'][str(original_prompt_userid)]['sourceImg']['prompt']
    print(original_prompt)
    prompt = data['prompt']
    #generatedSrc = get_fake_real_src()
    generatedSrc = await generate_image(prompt, game.id, game.game_ref.type)
    score = fuzz.ratio(original_prompt, prompt)
    logic['history'][str(original_prompt_userid)]['copies'][str(user.id)] = {"name": user.name, "prompt": prompt, "url" : generatedSrc, "score" : score}
    
    game.temp_json_data = json.dumps(logic)
    db.session.add(game)
    db.session.commit()

    return jsonify({"src": generatedSrc, "score": score}), 200


@app.route("/api/send_message", methods=["POST"])
async def send_message():
    db.session.execute(text('BEGIN EXCLUSIVE'))
    data = request.get_json()
    user = Users.query.get(data['userId'])
    if not user:
        db.session.rollback()
        return jsonify({}), 404
    
    game = PlayedGame.query.filter_by(id=user.current_played_game.id).with_for_update().first()#user.current_played_game
    if not game:
        db.session.rollback()
        return jsonify({}), 404
    
    messageText = data['msg']
    current_time = datetime.now()
    formatted_time = current_time.strftime("%Y-%m-%d %H:%M:%S")

    message = {"name" : user.name, "text": messageText, "timestamp": formatted_time}

    chat = json.loads(game.chat_json_data)

    chat["messages"].append(message)

    game.chat_json_data = json.dumps(chat)
    db.session.add(game)
    db.session.commit()

    return jsonify({}), 200

@app.route("/api/add_riddle_points", methods=["POST"])
async def add_riddle_points():
    db.session.execute(text('BEGIN EXCLUSIVE'))
    data = request.get_json()
    user = Users.query.get(data['userId'])
    if not user:
        db.session.rollback()
        return jsonify({}), 404
    
    game = PlayedGame.query.filter_by(id=user.current_played_game.id).with_for_update().first()#user.current_played_game
    if not game:
        db.session.rollback()
        return jsonify({}), 404
    
    points = 0

    logic = json.loads(game.temp_json_data)
    for key, record in logic["history"].items():
        winner = record.get("winner")
        if winner and winner.get("id") == user.id:
            points += 10

    stats = GameStatistics(user_id = user.id, played_game_id = game.game_id, points_earned = points)
    db.session.add(stats)
    db.session.commit()

    return jsonify({}), 200

@app.route("/api/get_winner", methods=["POST"])
async def get_winner():
    db.session.execute(text('BEGIN EXCLUSIVE'))
    data = request.get_json()
    user = Users.query.get(data['userId'])
    if not user:
        db.session.rollback()
        return jsonify({}), 404
    
    game = PlayedGame.query.filter_by(id=user.current_played_game.id).with_for_update().first()#user.current_played_game
    if not game:
        db.session.rollback()
        return jsonify({}), 404
    
    logic = json.loads(game.temp_json_data)

    history = logic['history']

    for id, entry in history.items():
        copies = entry.get("copies", {})
        winner = None

        for copy_id, copy_data in copies.items():
            if winner is None or copy_data["score"] > winner["score"]:
                winner = {
                    "id": int(copy_id),
                    "name": copy_data["name"],
                    "prompt": copy_data["prompt"],
                    "url": copy_data["url"],
                    "score": copy_data["score"]
                }
        entry["winner"] = winner


    game.temp_json_data = json.dumps(logic)
    db.session.add(game)
    db.session.commit()

    return jsonify({"logic": logic}), 200


@app.route("/api/get_stats", methods=["POST"])
async def get_stats():
    db.session.execute(text('BEGIN EXCLUSIVE'))
    data = request.get_json()
    user = Users.query.get(data['userId'])
    if not user:
        db.session.rollback()
        return jsonify({}), 404
    
    
    riddle_game = AllGames.query.filter_by(type='Riddle').first()
    recycle_game = AllGames.query.filter_by(type='Recycle').first()

    riddle_stats = GameStatistics.query.filter_by(user_id = user.id, played_game_id = riddle_game.id).all()
    riddle_points = sum(s.points_earned for s in riddle_stats)

    recycle_stats = GameStatistics.query.filter_by(user_id = user.id, played_game_id = recycle_game.id).all()
    recycle_points = sum(s.points_earned for s in recycle_stats)

    return jsonify({"riddle_points" : riddle_points, "recycle_points": recycle_points}), 200


    
    

if __name__ == '__main__':
    app.run(host='127.0.0.1', debug=True)