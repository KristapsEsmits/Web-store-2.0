import { createApp } from 'vue';
import { createPinia } from 'pinia';

import axios from 'axios'
import '../axios';
import './assets/main.css';
import App from './App.vue';
import router from './router';
import 'bootstrap' ;

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);
app.mount('#app');


axios.interceptors.request.use((config) => {
    config.headers.Authorization = `Bearer ${localStorage.getItem('access_token')}`
    return config;
});

axios.interceptors.response.use(function (response) {
    return response;
}, async function (error) {
    if (error.response.status === 401) await router.push({name: 'login'})
    return Promise.reject(error);
}
)

axios.defaults.baseURL = 'http://127.0.0.1:8000/api'