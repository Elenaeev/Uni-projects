<!-- author - xivano08 -->
<template> 
    <div class="flex one center">
        <div class="flex five">
            
            <div class="full">
                <button class="redBack" @click="this.disconnectUser" style="margin: 2% 0 0 2%;  padding: 1% 2% 1% 2%">
                    <h3 class="blackColor zeroPad">Quit</h3>
                </button>
            </div>
            <template v-if="this.game.stage==0">
                <div class="full two-fifth-1500 textCenter infoWrap">
                    <h1 class="greenColor" style="padding:0;">{{this.game.name}}</h1>
                    <img :src="gameDescription" style='max-height: 100vh; max-width: 100%; width: 800px; height: 500px; object-fit: contain' :style="{ opacity: gameDescription==='' ? '1' : '1'}">
                </div>
                <div class="full half-1000 fifth-1500 textCenter partyCodeWrap" style="margin: 8% 0% 0 0%; padding: 0;">
                    <PartyCodeComponent :code="game.party_code" />
                    <ReadyButtonComponent
                        class="full off-third-500 third-500 off-sixth-1000 two-third-1000"
                        :ready="this.user.ready_for_game"
                        @readyPressed="this.setReady()"/>
                </div>
            </template>

            <template v-if="this.game.stage == 1">
                <div class="full off-none-1000 half-1000 three-fifth-1500 textCenter" style="margin-left: 13%; min-height: 650px;">
                    <h1 class="greenColor" style="padding:0; margin-bottom: 2%;">{{this.game.name}}</h1>
                    <TimeoutWritingComponent
                        :newTimer="this.newTimer"
                        @timerSet="this.newTimer = false"
                        @timeOut="submitAndReady()"
                        @promptReady="(p) => {this.prompt = p;}"
                        :secondsToWait="this.game.logic.secTimer" 
                        class="full off-fourth-500 half-500 off-third-1500 third-1500"/>
                    <ReadyButtonComponent  
                        @readyPressed="submitAndReady()"  
                        :ready="this.playerIsReady"
                        style="margin-top: 5%;"/>
                </div>
            </template>

            <template v-if="this.game.stage <= this.stagesNum-2 
                            && this.game.stage > 1 
                            && this.game.state=='Playing'" :key="this.game.stage" >

                <div class="two-fifth-1500 full textCenter" >
                    <h1 class="greenColor" style="padding:0; margin-bottom: 2%;">{{this.game.name}}</h1>
                    <div class="flex two" style="margin-top: 10%;">
                        <div class="textCenter">
                            <img  :src="this.imgSrc"
                            style='max-height: 100vh; max-width: 100%; width: 300px; height: 300px;
                            object-fit: contain'>
                        </div>
                    
                        <div class="textCenter">
                            <img :src="this.guessImgSrc"
                            style='max-height: 100vh; max-width: 100%; width: 300px; height: 300px;
                            object-fit: contain' :style="{ opacity: guessImgSrc==='' ? '0' : '1'}">
                   
                        </div>
                         
                    </div>  
                    <ProgressBarComponent :progress=this.similarity></ProgressBarComponent> 
                </div>
                
                
                <div class="full half-1000 two-fifth-1500 textCenter"
                    >
                    <TimeoutWritingComponent :key="game.stage"
                        :newTimer="this.newTimer"
                        @timerSet="this.newTimer = false"
                        textVal="Guess the prompt!"
                        @timeOut="setReady()"
                        @promptReady="(p) => {this.prompt = p;}"
                        :secondsToWait="this.game.logic.secTimer" 
                        class="full off-fourth-500 half-500 off-sixth-1500 two-third-1500"/>
                        <div class="flex two" style="width: 100%; padding-bottom: 10%;">
                            <button class="greenBack" @click="submitGuess()" style="margin-top: 5%; margin-left: 5%; width: 45%; max-height: 100px;">
                                <h3 class="zeroPad blackColor">Try out prompt</h3>
                            </button>
                            <ReadyButtonComponent 
                            :key="this.game.stage"
                            @readyPressed="setReady()"  
                            :ready="this.playerIsReady"
                            style="margin-top: 5%; margin-left: 5%; width: 45%;"/>
                            
                        </div>
                    
                </div>
            </template>

            <template v-if="(this.game.state == 'Finished' || this.game.stage+1 == this.stagesNum) && this.game.stage != 1">
                <div class="full off-fourth-1500 two-fifth-1500 textCenter" style="margin-right: 5%;">
                    <h1 class="greenColor" style="padding:0; margin-bottom: 2%;">{{this.game.name}}</h1>
                    <RiddleResultsComponent  :userId = this.user.id></RiddleResultsComponent>
                </div>
            </template>

            <div class="full off-fourth-500 half-500 off-none-1000 off-none-1500 fifth-1500 chatWrap">
                <ChatComponent :messages="this.messages" :userId="this.user.id"/>
            </div>
            <div class="full partyUsersWrap">
                <PartyUsersComponent :users="players"/>
            </div>
        </div>
    </div>
</template>

<script>
    import ChatComponent from '@/components/xivano08Comps/ChatComponent.vue';
    import PartyCodeComponent from '@/components/xdomra00Comps/PartyCodeComponent.vue';
    import ReadyButtonComponent from '@/components/xdomra00Comps/ReadyButtonComponent.vue';
    import PartyUsersComponent from '@/components/xdomra00Comps/PartyUsersComponent.vue';
    import TimeoutWritingComponent from '@/components/xdomra00Comps/TimeoutWritingComponent.vue';
    import ProgressBarComponent from '@/components/xivano08Comps/ProgressBarComponent.vue';
    import RiddleResultsComponent from '@/components/xivano08Comps/RiddleResultsComponent.vue';

    export default
    {
        components: {
            TimeoutWritingComponent,
            ChatComponent,
            PartyCodeComponent,
            ReadyButtonComponent,
            PartyUsersComponent,
            ProgressBarComponent,
            RiddleResultsComponent
        },
        mounted() 
        {
            this.mounted = true;
            this.setUser();
        },

        unmounted()
        {
            this.mounted = false;
        },

        data() {
            return {
                gameDescription: "",
                game: "",
                players: "",
                user: "",
                mounted: undefined,
                playerIsReady: false,
                prompt: "",
                newTimer: false,
                imgSrc: "",
                guessImgSrc: "",
                stagesNum: 2,
                similarity: 0,
                messages: []
            }
        },

        watch: {
            newTimer(newInfo) {
                console.log(`TIMER ${newInfo}`);
            },
        
            'game.stage'(newInfo, oldInfo) {
                if (oldInfo === "") return; 
            
                const stageOrder = newInfo + 1;
                const isLastStage = stageOrder === this.stagesNum && stageOrder > 2;
            
                if (stageOrder > this.stagesNum) {
                    console.log("HEEEEEEREEEEE");
                    return; 
                }
            
                if (isLastStage) {
                    console.log("FINISH GAME!");
                    this.finishGame();
                    this.newTimer = false; 
                    return;
                }

                if (newInfo !== oldInfo) {
                    console.log(`NEW STAGE ${newInfo}, OLD ${oldInfo}, Finish: ${isLastStage}`);

                
                    if (stageOrder > 2) {
                        console.log("PROCESSING PROMPT DESCRIPTION STAGE");
                        this.similarity = 0;
                        if(this.prompt != "")
                        
                        setTimeout(this.getSrcImage, 1000);
                    }

                    this.newTimer = true;
                }
            }
        },


        methods: {

            setReady()
            {
                return this.$api.get("toggle_ready", {
                    params: {
                        userId: this.user.id,
                    }
                })
                .catch(error => {
                    this.errorWarning = "Error";
                    setTimeout(()=>{this.errorWarning=""}, 3000);
                })
            },

            setUser()
            {
                if(!this.mounted)
                    return;

           
                return this.$api.get("fantom_user")
                .then(response => {
                    this.user = response.data;
                    console.log("1. Current user = "+JSON.stringify(this.user));

                    this.setGame(this.user.current_game_id);
                    this.setPlayers(this.user.current_game_id);
                })
                .catch(error => {
                    setTimeout(this.setUser, 2000);
                })

                
            },

            setGame(gameId)
            {
                if(!this.mounted)
                    return;

                return this.$api.get("played_game", {
                    params: {
                        id: gameId
                    }
                })
                .then(response => {
                    this.game = response.data;
                    this.game.chat = JSON.parse(this.game.chat);
                    this.messages = this.game.chat.messages;
                    this.game.logic = JSON.parse(this.game.logic);
                    this.stagesNum = this.game.logic['stages']
                    //console.log(this.stagesNum)
                    
                    console.log("2. Current game = "+JSON.stringify(this.game));
                    
                    

                    if(this.gameDescription=="")
                    {
                        this.setGameInfo(this.game.game_id);
                    }
                    

                })
                .catch(error => {
                    setTimeout(() => this.setGame(gameId), 2000);
                   
                })
                .finally(() => {
                    setTimeout(() => this.setGame(gameId), 500);
                })
            },

            setGameInfo(gameId)
            {
                if(!this.mounted)
                    return;
                return this.$api.get("game_info", {
                    params: {
                        id: gameId
                    }
                })
                .then(response => {
                    this.gameDescription = response.data.src;

                })
                .catch(error => {
                    setTimeout(this.setGameInfo, 2000);
                })
            },

            setPlayers(gameId)
            {
                if(!this.mounted)
                    return;

                return this.$api.get("game_players", {
                    params: {
                        id: gameId
                    }
                })
                .then(response => {
                    this.players = response.data;

                    for(let player of this.players)
                    {
                        if(this.user.id == player.id)
                        {
                            
                            this.playerIsReady = player.ready_for_game;
                            break;
                        }
                    }
                })
                .catch(error => {
                    // console.log(error);
                })
                .finally(() => {
                    setTimeout(() => this.setPlayers(gameId), 500);
                })
            },

            disconnectUser()
            {
                return this.$api.post("disconnect_player", {
                    id: this.user.id
                }, {
                        withCredentials: true
                })
                .then(response => {
                    this.$router.push({ name: "home" });
                })
                .catch(error => {
                    this.errorWarning = "Error, cant't disconnect";
                    setTimeout(()=>{this.errorWarning=""}, 3000);
                })
            },

            submitAndReady()
            {
                console.log("here")
                this.submitPrompt().then((x) => {this.setReady()});
                
            },

            submitPrompt()
            {
                console.log(this.user.name, this.prompt)
                return this.$api.post("riddle_submit_prompt", {
                    userId: this.user.id,
                    prompt: this.prompt,
                }, {
                    withCredentials: true,
                    timeout: 10000
                })
                .then(response => {
                })
                .catch(error => {
                    this.errorWarning = "Submit error";
                    setTimeout(()=>{this.errorWarning=""}, 3000);
                })
            },

            submitGuess()
            {
                this.guessImgSrc = ""
                console.log(this.user.name, this.prompt)
                return this.$api.post("riddle_guess_image", {
                    userId: this.user.id,
                    prompt: this.prompt,
                }, {
                    withCredentials: true,
                    timeout: 10000
                })
                .then(response => {
                    this.guessImgSrc = response.data.src;
                    this.similarity = response.data.score;
                })
                .catch(error => {
                    this.errorWarning = "Submit error";
                    setTimeout(()=>{this.errorWarning=""}, 3000);
                })
            },

            getSrcImage()
            {
                console.log("GET IMAGE");
                return this.$api.get("riddle_get_image", {
                    params: {
                        userId: this.user.id,
                    },
                    timeout: 10000
                })
                .then(response => {
                    this.imgSrc = response.data.src; 
                    this.guessImgSrc = ""
                    console.log(this.imgSrc)
                })
                .catch(error => {
                    this.errorWarning = "Error, could not get image";
                    setTimeout(()=>{this.errorWarning=""}, 3000);
                })
                .finally(()=>{
                    
                })
            },

            finishGame()
            {
                if(!this.mounted)
                    return;

                return this.$api.post("finish_game", {
                    id: this.game.id
                }, {
                    withCredentials: true
                })
                .catch(error => {
                    setTimeout(this.finishGame, 300);
                })
            },

        }
    }
</script>

<style scoped>

.infoWrap
{
    margin-left: 13%;
    min-height: 650px;
}

.promptStageWrap
{
    margin-left: 13%;
    min-height: 650px;
}


@media(max-width:1500px)
{
    .infoWrap, .promptStageWrap
    {
        margin-left: 0;
    }
}


.partyUsersWrap
{
    margin: 2% 0% 0% 1%;
    padding: 0% 2% 0% 2%;
}

.chatWrap
{
    min-height: 650px;
}

@media(max-width:1500px)
{
    .chatWrap
    {
        min-height: 800px;
    }
}

</style>