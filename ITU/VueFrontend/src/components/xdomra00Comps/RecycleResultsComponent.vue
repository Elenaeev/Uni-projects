
<!-- author - xdomra00 -->
<template>
    <!-- results window -->
    <div v-if="this.results" class="flex textCenter resStyle">

        <div class="flex scrolledHistory " ref="scroller">
            <div v-if="this.nextSetRecord >= 0" 
                v-for="(playerName, idx) in this.results[this.playedSet].names.slice(0, this.nextSetRecord)"
                class="full three">
    
                <h3 class="full"
                    style="padding: 5% 30% 5% 5%; text-align:left; color:black;">
                    {{playerName}}:<br/>{{this.results[this.playedSet].prompts[idx]}}
                </h3>
    
                <img class="full off-third"  
                    :src="this.results[this.playedSet].generatedSrc[idx]" 
                    style="max-height: 100vh; max-width: 100%; width: 400; height: 400px; margin: 0% -55% 0 0 ;
                    object-fit: contain" :style="{ opacity: this.results[this.playedSet].generatedSrc[idx]==='' ? '1' : '1'}">
            </div>
        </div>


       
        
        <div class="full three" style="padding: 0 15% 5% 15%;">
            <button class="off-two redBack" :disabled="this.playedSet == 0" @click="this.prevSet">
                Prev
            </button>
            <button class="off-third greenBack"  :disabled="this.playedSet == this.results.length-1" @click="this.nextSet">
                Next
            </button>
        </div>
    </div>

</template>


<script>

    export default {
         
        props: {
            gameId: {
                required: true
            }
        },
        watch:
        {
        },

        mounted()
        {
            this.mounted = true;
            setTimeout(this.getResults, 2000);//to get more fresh data
        },

        unmounted()
        {
            this.mounted = false;
            clearInterval(this.revealInterval);
        },

        data()
        {
            return {
                results: null,
                mounted: false,
                //nested counters:
                playedSet: 0,//players - switch with buttons (show first player histrory, then next)
                nextSetRecord: undefined,//players history unit (show first generation, then another)        
                maxSubRecords: 2,
                revealInterval: undefined,
            }

        },

        methods: {

            getResults()
            {
                if(!this.mounted)
                    return;

                this.$api.get("recycle_game_results", {
                    params: {
                        gameId: this.gameId
                    }

                })
                .then(response => {
                    this.results = response.data.results;//array
                    console.log("RESULTS: "+JSON.stringify(this.results));
                    this.playedSet = 0;
                    this.startShow();
                })
                .catch(error => {
                    setTimeout(this.getResults, 2000);
                })
            }, 

            startShow()
            {
                this.nextSetRecord = 0;
                this.revealInterval = setInterval(this.revealNext, 500);
            },

            nextSet()
            {
                this.playedSet++;
                if(this.playedSet >= this.results.length)
                    this.playedSet = this.results.length-1;

                this.startShow();
            },

            prevSet()
            {
                this.playedSet--;
                if(this.playedSet < 0)
                    this.playeedSet = 0;

                this.startShow();
            },

            revealNext()
            {
                if(!this.mounted)
                    return;

                this.nextSetRecord++;
                if(this.nextSetRecord > this.results[this.playedSet].users.length)
                {
                    clearInterval(this.revealInterval);
                }
                // const scroller = this.$refs.scroller;
                // scroller.scrollTop = scroller.scrollHeight;
            }

        }
    }

</script>

<style scoped>

.resStyle
{
    max-width: 70%;
    background-color: rgba(251, 190, 91, 0.14);
    border-radius: 15px;
    padding-top: 4%;
}   

.scrolledHistory
{
    padding: 5% 5% 0 0;
    min-height: 500px;
    max-height: 500px;  
    overflow-y: scroll;
    overflow-x: hidden;
    word-wrap: break-word;
    scrollbar-width: thin;
    scrollbar-color: #DA6D6B rgba(251, 190, 91, 0.01);

}



</style>