//#author - xdomra00, xivano08 

// import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import api from './api'
import Cookies from 'js-cookie';


const app = createApp(App)

app.config.globalProperties.$api = api;
app.config.globalProperties.$cookie = Cookies;

app.use(router)

app.mount('#app')
