<!-- author - xivano08 -->
<template>
    <div class="card textCenter" style="height:100%; margin: 0% 10% 0 0;">
        <header  style="height:7%">Chat</header>
        <div style="height:83%">
             <div v-for="(msg, index) in this.messages" :key="msg.timestamp">
            <div>
                <h4 style="margin-left: 5%; margin-right: 5%; word-wrap: break-word; overflow-wrap: break-word; text-align: left;">
                    <span class= "redColor">{{ msg.name }}:</span>
                    <span class="">{{ msg.text }}</span>
                </h4>
            </div>
        </div>
        </div>
       
        <div class="flex " style="margin: 0%; height: 10%; background-color: white;">
            <input v-model="this.messageText" class="full" style="height: 50%; align-items: center">
            <button class="full flex greenBack" @click="sendMessage()" style="margin: 0%; height: 50%; justify-content: center; align-items: center;">
                <h4 style="padding: 0;">Send</h4>
            </button>
        </div>    
    </div>
</template>


<script>

export default {
    data() {
        return {
            messageText: ""
        }
    },

    props:
    {
       userId:
       {
            required: true
       },
       messages: 
       {
            required: true
       }

        
    },
    methods: {
        sendMessage()
        {
            return this.$api.post("send_message", {
                    userId: this.userId,
                    msg: this.messageText,
                }, {
                    withCredentials: true,
                    timeout: 10000
                })
                .then(response => {
                    this.messageText = ""
                })
                .catch(error => {
                    this.errorWarning = "Error";
                    setTimeout(()=>{this.errorWarning=""}, 3000);
                })
        }
    },
}

</script>
