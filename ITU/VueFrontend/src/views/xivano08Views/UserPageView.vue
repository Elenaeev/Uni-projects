<!-- author - xivano08 -->

<template>
    <div class="flex five">
        <div class="full flex two">
            <div style="width: 90%;" >
                <h1 style="margin-left: 5%; margin-right: 5%; text-align: left; padding: 0%;">
                    <span class="redColor">{{ this.user.name }}</span>
                    <span v-if="this.user.fathom" class="redColor second-span" style="font-size: 18px; display: inline-block; white-space: nowrap;">(fathom user)</span>
                </h1>
            </div>
            <button class="redBack " @click="go_home" style="height: 70px; width: 7%; margin-top: 30px;" >
                <h3>Home</h3>
            </button>
            
        </div>
        

        <div v-if="this.user.fantom" class="off-fifth three-fifth off-two-fifth-1000 fifth-1000">
            <input v-if="currentAction =='Register'" class="stack" placeholder="Name" v-model="this.form.name"/>
            <input class="stack" placeholder="Email" v-model="this.form.email" />
            <input class="stack" placeholder="Password" v-model="this.form.password" />
            <button class="stack redBack" type="submit" @click="this.loginOrRegister">{{ this.currentAction }}</button>
            <button class="redBack" type="submit" @click="this.changeAction">...</button>

        </div><br>

        <h3 v-show="error_visible" style="color: red;">Invalid data</h3>
        

        <h1 class="full redColor" style="padding: 10px;">Statistics</h1>
        <h2 class="full redColor" style="padding: 10px;">Points totally earned</h2>
        <h1 class="full greenColor" style="padding: 10px;">{{this.riddle_points+this.recycle_points}}</h1>
        <h2 class="full redColor" style="padding: 10px;">Riddle points earned</h2>
        <h1 class="full greenColor" style="padding: 10px;">{{this.riddle_points}}</h1>
        <h2 class="full redColor" style="padding: 10px;">Recycle points earned</h2>
        <h1 class="full greenColor" style="padding: 10px;">{{this.recycle_points}}</h1>
    </div>
    
</template>

<script>

    export default{

        
        
        mounted() 
        {
            this.mounted = true;
            this.setUser();
            

        },

        beforeDestroy() {
        },

        unmounted()
        {
            this.mounted = false;
        },

        data() 
        {
            return {
                currentAction: "Register",
                user: "",
                mounted: null,
                form: {
                  name: '',
                  email: '',
                  password: ''
                },
                error_visible: false,
                
                riddle_points:0,
                recycle_points:0,
                all_points:0
            }   
        },

        methods: {
            setUser()
            {
                if(!this.mounted)
                    return;
                this.$api.get("user")
                .then(response => {
                    this.user = response.data;
                    console.log("Current user = "+this.user['name']);
                    console.log("is fantom = "+this.user['fantom']);

                    this.getPoints();
            
                })
                .catch(error => {
                    setTimeout(this.setUser, 2000);
                })

                
            },

            changeAction()
            {
                console.log(this.currentAction);
                if(this.currentAction == "Register")
                {
                    this.currentAction = "Log in";
                }
                else{
                    console.log("here");
                    this.currentAction = "Register";
                }
                console.log(this.currentAction);
            },

            loginOrRegister()
            {
                if(this.currentAction=="Log in")
                {
                    if(!this.mounted)
                        return;
                    const formData = {
                        email: this.form.email,
                        password: this.form.password,
                    };
                    this.$api.post("login", formData,{withCredentials: true})
                    .then(response => {
                        this.user = response.data;
                        console.log("Current user = "+this.user['name']);
                        console.log("is fantom = "+this.user['fantom']);
                        this.error_visible = false
                    })
                    .catch(error => {
                        this.error_visible = true;
                    })
                }
                else if(this.currentAction == "Register")
                {
                    if(!this.mounted)
                    return;
                    const formData = {
                        name: this.form.name,
                        email: this.form.email,
                        password: this.form.password,
                    };
                    this.$api.post("register", formData,{withCredentials: true})
                    .then(response => {
                        this.user = response.data;
                        console.log("Current user = "+this.user['name']);
                        console.log("is fantom = "+this.user['fantom']);
                        this.error_visible = false
                    })
                    .catch(error => {
                        this.error_visible = true;
                    })
                }
                
            },

            getPoints()
            {
                return this.$api.post("get_stats", {userId: this.user.id},
                    {
                        withCredentials: true,
                        timeout: 10000
                    }
                )
                .then(response => {
                    this.riddle_points = response.data.riddle_points;
                    this.recycle_points = response.data.recycle_points;
                    this.all_points = this.riddle_points + this.recycle_points;
                }).catch(error => {
                    setTimeout(this.getPoints, 1000);
                })
            },

            go_home()
            {
                this.$router.push({ name: "home" });
            }
        },
    }
</script>
