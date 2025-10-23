<!-- author - xdomra00 -->

<template>

    <div class="flex one textCenter" >
        <h3 class="full animated" :class="{'active': doTansition}">{{this.textVal}}</h3>
        <h3 class="full">{{ minutesLeft }}:{{ secondsLeft.toString().padStart(2, '0') }}</h3>
        <textarea :disabled="timeLeft <= 0" v-model="this.prompt" class="full" style="resize: none;" rows=8 placeholder="New flavor of ice cream"></textarea>
    </div>

</template>


<script>

    export default {

        props: {
            secondsToWait : {
                type: Number,
                required: false,
                default: 20
            },
            
            newTimer: {
                type: Boolean,
                required: false,
                default: true
            },

            textVal: {
                required: false,
                default: "Start writing!"
            }
        },

        watch : {
            prompt(prmpt)
            {
                this.$emit('promptReady', prmpt);
            }
        },

        mounted()
        {
            window.addEventListener('beforeunload', this.unloaded);
            // localStorage.setItem("arrivetime", Date());
            setTimeout(()=>{this.doTansition=true; setTimeout(()=>{this.doTansition = false;}, 100)}, 100);
            let tl =  localStorage.getItem("timeLeft");
            let lt = localStorage.getItem("leaveTime");
            
            console.log("TIMER MOUNT: NEW "+this.newTimer+" TIME LEFT "+tl+" LAST LEAVE TIME "+lt);
            if(!this.newTimer && lt && tl)
            {
                let absenseTime = Date.now() - parseInt(lt,10);
                let res = parseInt(tl,10)-parseInt(absenseTime/1000);
                if(res < 0)
                    res = 0;

                this.timeLeft = res-1;//if fast reload 
            }
            else
            {
                this.timeLeft = this.secondsToWait;
            }
            localStorage.setItem("timeLeft", this.timeLeft);
            this.counterInterval = setInterval(this.timeCounter, 1000);
            this.$emit("timerSet")
        },

        unmounted()
        {
            this.unloaded()
            window.removeEventListener('beforeunload', this.unloaded);
        },



        data() 
        {
            return {
                timeLeft: 0,
                prompt: "",
                mounted: false,
                counterInterval: null,
                doTansition: false,
            }
        },

        methods: {

            unloaded()
            {
                clearInterval(this.counterInterval);
                localStorage.setItem("leaveTime", Date.now());
                this.doTansition = false;
            },

            timeCounter()
            {
                if(this.timeLeft <= 0)
                {
                    clearInterval(this.counterInterval);
                    localStorage.removeItem("timeLeft");
                    localStorage.removeItem("leaveTime");
                    this.$emit('timeOut'); 
                    return;
                }

                this.timeLeft--;
                localStorage.setItem("timeLeft", this.timeLeft);
            }
        },

        computed: {

            minutesLeft() 
            {
                let tl = this.timeLeft;
                return Math.floor(tl / 60);
            },

            secondsLeft()
            { 
                let tl = this.timeLeft;
                return tl % 60; 
            }

        }, 

        
    }

</script>


<style scoped>

.animated
{
    transform: scale(1, 1);
    transition: transform 500ms ease;
}


.animated.active
{
    transform: scale(2, 2); 
    transition: transform 100ms ease;
}

</style>