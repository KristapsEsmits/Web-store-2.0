import {createApp} from 'vue';
import {createPinia} from 'pinia';

import axios from 'axios'
import '../axios';
import './assets/main.css';
import App from './App.vue';
import router from './router';
import 'bootstrap';

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);
app.mount('#app');


axios.interceptors.request.use((config) => {
    config.headers.Authorization = `Bearer ${localStorage.getItem('access_token')}`
    return config;
});


axios.defaults.baseURL = 'http://127.0.0.1:8000/api'