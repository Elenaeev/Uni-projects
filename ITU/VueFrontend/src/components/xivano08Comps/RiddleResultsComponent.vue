<!-- author - xivano08 -->

<template>
    <!-- results window -->
    <div class="flex textCenter" >

        <div class="flex scrolledHistory greenBack " ref="scroller">
            <div class="full">
                <h2 style="padding: 0%;">Winner</h2>
            </div>
            <div class="flex two">
                <h3 class="half">{{ this.original_image.name }}</h3>
                <h3 v-if="this.winner.id == 0">No winners</h3>
                <h3 v-if="this.winner.id != 0">{{ this.winner.name }}</h3>
                <img class="half" :src="this.original_image.url">
                <img v-if="this.winner.id != 0" :src="this.winner.url">
                <h4 class="half">{{ this.original_image.prompt }}</h4>
                <h4 v-if="this.winner.id != 0">{{ this.winner.prompt }}</h4>
                <h3 v-if="this.winner.id != 0" class="full">{{ 'Similarity: ' + this.winner.score + '%' }}</h3>
            </div>
            <div class="full">
                <h2 style="width: 100%;">All guesses</h2>
            </div>
            
            <div v-for="(copy, index) in this.copies" :key="copy.name">
                <div>
                    <h3>{{ copy.name }}</h3>
                    <img :src="copy.url" style="height: 200px; width: 200px;">
                    <h4>{{ copy.prompt }}</h4>
                    <h3>{{ 'Similarity: ' + copy.score + '%' }}</h3>
                </div>
            </div>
        </div>


       
        
        <div v-if="Object.keys(this.logic).length > 0" class="full three" style="padding: 0 15% 5% 15%;">
            <button class="off-two redBack" :disabled="this.page == 0" @click="this.prevPage">
                Prev
            </button>
            <button class="off-third greenBack"  :disabled="this.page == Object.keys(this.logic.history).length -1 " @click="this.nextPage">
                Next
            </button>
        </div>
    </div>

</template>

<script>

    export default{

        mounted()
        {
            
            this.mounted = true;
            //this.getWinner();
            //this.winner = {"id": 0, "name": "", "url": "", "prompt": "", "score": ""};
            this.getWinner();
            
        },

        unmounted()
        {
            this.mounted = false;
        },

        data() {
            return {
                page : 0,
                pagesNum : 0,
                original_image : {},
                copies : [],
                winner : {},
                logic : {}
            }
        },

        props:
        {
            // logic:
            // {
            //     required: true
            // },

            userId:
            {
                required: true
            }
        },

        methods: {
        getPagesNum()
        {
            this.pagesNum = Object.keys(this.logic.history).length;
        },
        nextPage() {
            if (this.page + 1 < Object.keys(this.logic.history).length) {
                this.page += 1;
                this.getResults();
            } else {
                console.warn("No more pages.");
            }
        },

        prevPage() {
            if (this.page > 0) {
                this.page -= 1;
                this.getResults();
            } else {
                console.warn("Already at the first page.");
            }
        },

        getResults() {
            console.log("PAGE"+this.page);
            console.log("PAGE"+this.page);
            console.log("PAGE"+this.page);
            console.log("PAGE"+this.page);
            console.log("PAGE"+this.page);
            
            let records = [];
            for (let key in this.logic.history) {
                records.push([key, this.logic.history[key]]);
            }
            

            console.log(JSON.stringify(records[this.page]));
            console.log(JSON.stringify(records[this.page+1]));
            if (this.logic.history && records[this.page][1]) {
                this.original_image = records[this.page][1].sourceImg;
            } else {
                console.warn("Page out of bounds or logic.history missing!");
            }

            console.log(JSON.stringify(this.logic));
            console.log(JSON.stringify(this.original_image));

            this.copies = Object.values(records[this.page][1].copies);
            console.log(JSON.stringify(this.copies));

            this.winner = records[this.page][1].winner
            if (this.winner == null)
            {
                this.winner = {"id": 0, "name": "", "url": "", "prompt": "", "score": ""};
            }
        },

        getWinner()
        {
            console.log("GET WINNER");
            return this.$api.post("get_winner", {
                userId: this.userId
            }, {
                    withCredentials: true,
                    timeout: 10000
            })
            .then(response => {
                this.logic = response.data.logic;
                this.addPoints();
                this.getResults();
            })
            .catch(error => {
                this.errorWarning = "Error";
                setTimeout(()=>{this.errorWarning=""}, 3000);
            })

           
        },

        addPoints()
        {
            return this.$api.post("add_riddle_points", {
                userId: this.userId
            }, {
                    withCredentials: true,
                    timeout: 10000
            })
            .then(response => {
                
            })
            .catch(error => {
                this.errorWarning = "Error";
                setTimeout(()=>{this.errorWarning=""}, 3000);
            })
        }
    },
    }
    
</script>

<style scoped>
    .scrolledHistory
    {
    padding: 5% 5% 0 0;
    min-height: 500px;
    max-height: 500px;  
    overflow-y: scroll;
    overflow-x: hidden;
    word-wrap: break-word;
    scrollbar-width: thin;
    scrollbar-color: #1b5845 rgba(251, 190, 91, 0.01);

    }
</style>