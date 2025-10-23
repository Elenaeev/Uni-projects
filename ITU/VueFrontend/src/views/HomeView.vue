<!-- author - xdomra00 -->

<template>
    <div class="flex one">
        <div style="display: grid; grid-template-columns: 1fr auto;">
            <h1 class="full greenColor" style="margin-left: 50px;">Welcome to <span class="redColor">Gamia</span></h1>
            <button class="greenBack" @click="go_to_user" style="margin-right: 50px; margin-top: 50px; height: 70px;">
                <h3>User</h3>
            </button>
        </div>
        
        <div class="flex three textCenter">
            <!-- left block -->
            <div class="flex center full half-1000 third-1500">
                <div class="full">
                    <h2 class="redColor">Games</h2>
                    <ul class="menuUl">
                        <li v-for="game in allGames" :key="game.id" >
                            <button type="submit" for="createGameForm" @click="createNewGame()" @mouseover="setGameInfo(game.id)" class="fourth redColor menuButton" style="font-size: 24px;">{{ game.name }}</button>
                        </li>
                    </ul>
                    <h3 v-if="errorWarning != ''" style="color:red;">{{ errorWarning }}</h3>
                    <form id="createGameForm" @submit.prevent style="margin-top: 5%;">
                        <label>
                            <input type="checkbox" v-model="privateFlag">
                            <span class="checkable">Private game</span>
                        </label>
                    </form>
                </div>
                <form  @submit.prevent="this.handleJoin()" class="flex one center">
                    <div class="center third">
                        <h2 class="redColor">Connect</h2>
                        <div class="spanPad">
                            <input class="two-third" type="text" v-model="partyCode">
                            <button class="third" @click="" style="color: #F2FF00; background-color: #52D6B5;">Join</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- right block  -->
            <div class="flex center full half-1000 two-third-1500 ">
                <img class="flex" :src="gameDescriptionSrc" style='max-height: 100vh; max-width: 100%; width: auto; height: auto; object-fit: contain' :style="{ opacity: gameDescriptionSrc==='' ? '0' : '1'}">
            </div>
        </div>
    </div>
          <!-- <nav>
        <RouterLink to="/">Home</RouterLink>
        <RouterLink to="/about">About</RouterLink>
      </nav> -->


  <!-- <RouterView /> -->

</template>


<script>

    export default {

        beforeDestroy() 
        {

        },


        mounted() 
        {
            this.mounted = true;
            this.setFantomUser();
            this.getAllGames();

            
        },

        unmounted()
        {
            this.mounted = false;
        },

        data() 
        {
            return {
                allGames: "",
                gameDescriptionSrc: "",
                gameRouteName: "",
                chosenGameId: "",
                user: "",
                privateFlag: false,
                partyCode: "",
                errorWarning: "",
                mounted: null,
            }   
        },

        methods: {
            handleJoin()
            {
                if(!this.mounted)
                    return;
                this.$api.post("join_game", {
                        userId: this.user.id,
                        code: this.partyCode
                }, {
                        withCredentials: true
                })
                .then(response => {
                    this.$router.push({ name: response.data.routeName });
                })
                .catch(error => {
                    this.errorWarning = "Error, can't join game";
                    setTimeout(()=>{this.errorWarning=""}, 3000);
                })
            },

            setFantomUser()
            {
                if(!this.mounted)
                    return;
                this.$api.get("fantom_user")
                .then(response => {
                    this.user = response.data;
                    console.log("Current user = "+this.user['name']);
                })
                .catch(error => {
                    setTimeout(this.setFantomUser, 2000);
                    // console.log(error);
                })
            },

            getAllGames()
            {
                if(!this.mounted)
                    return;
                this.$api.get("all_games")
                .then(response => {
                    this.allGames = response.data;
                    console.log("got all games");
                })
                .catch(error => {
                    setTimeout(this.getAllGames, 2000);
                    // console.log(error);
                })
            },

            setGameInfo(gameId)
            {   
                if(!this.mounted)
                    return;
                this.chosenGameId = gameId;

                this.$api.get("game_info", {
                    params: {
                        id: gameId
                    }
                })
                .then(response => {
                    this.gameDescriptionSrc = response.data.src;
                    this.gameRouteName = response.data.routeName;
                })
                .catch(error => {
                    setTimeout(() => this.setGameInfo(gameId), 2000);
                    // console.log(error)
                })
            },

            emptyGameDescription()
            {
                this.chosenGameId = "";
                this.gameDescriptionSrc = ""
            },

            createNewGame()
            {
                if(!this.mounted)
                    return;
                this.$api.post("create_game_for", {
                        userId: this.user.id,
                        gameId: this.chosenGameId,
                        private: this.privateFlag
                }, {
                        withCredentials: true
                })
                .then(response => {
                    this.$router.push({ name: this.gameRouteName });
                })
                .catch(error => {
                    this.errorWarning = "Error, try again";
                    setTimeout(()=>{this.errorWarning=""}, 3000);
                })
            },

            go_to_user()
            {
                this.$router.push({ name: "User" });
            }
        }

    }
</script>



<style scoped>

.menuUl
{ 
    max-height: 200px; 
    overflow-y:auto;
    scrollbar-width: thin;
    scrollbar-color: #52D6B5 #FCEEDF;
}

.spanPad
{
    border-radius: 15px;
    padding: 5px 10px 5px 10px;
    background-color: #52D6B5;
}


.menuButton
{

    background-color: rgba(251, 190, 91, 0.14);
    padding: 2% 0 2% 0
}

.menuButton:hover
{
    transition: 0s;
    outline: solid;
    outline-color:#52D6B5;
    
}

</style>

<style>
h1 
{
    font-family: "Jua", cursive;
    font-size: 64px;

}

h2 
{
    font-family: "Jua", cursive;
    font-size: 48px;

}

h3 
{
    font-family: "Jua", cursive;
    font-size: 24px;
}

h4
{
    font-family: "Jua", cursive;
    font-size: 12px;
}


.textCenter
{
    text-align: center;
} 

.greenColor
{
    color:#52D6B5;
}

.redColor
{
    color:#F97D7B;
}

.blackColor
{
    color: black;
}

.redBack
{
    background-color: #F97D7B;
}
 
.greenBack
{
    background-color: #52D6B5;
}

.zeroPad
{
    padding: 0;
}

</style>