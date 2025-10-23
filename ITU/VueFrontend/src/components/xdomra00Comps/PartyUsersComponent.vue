<!-- author - xdomra00 -->
<template>
    <div class="flex one two-500 three-800 four-1000 six-1500 userPanel redBack">
        <div class="flex five card userCard" v-for="user in users">
            <h3 class="" style="text-align: left;">{{ user.host ? "👑" : ""}}</h3>
            <h3 class="two-fifth" style="text-align: left; overflow-x: hidden; text-overflow: ellipsis; max-width: 160px;">{{user.name}}</h3>
            <h3 class="" style="text-align: right;">
                <span class="label":class="readyStates[user.ready_for_game]">
                    {{actionStates[user.ready_for_game]}}
                </span>
            </h3>
            <!-- <img src="" style="width: 50px; height: 50px;" class="dangerous"> -->
        </div>
        
    </div>
</template>


<script>

    export default {
        props: ["users"], 
    
        data()
        {
            return {
                actionStates: {true: "ready", false: "preparing"},
                readyStates: {true: "success", false: "warning"},
            }
        }
}

</script>


<style scoped>

.userPanel
{
    border-radius: 15px;
    overflow-x: auto;
    flex-wrap: nowrap;
    scrollbar-width: thin;
    scrollbar-color: #DA6D6B #F97D7B;
}

.userCard
{
    min-width: 20%;
    margin: 1% 1% 0.5% 1%;
    padding: 0% 0% 0% 0%;
}



</style>