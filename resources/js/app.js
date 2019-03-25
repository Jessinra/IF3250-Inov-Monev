require('./bootstrap');

import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter);

import App from './App.vue'
import routes from './routes.js'

const router = new VueRouter({
    mode: 'history',
    routes: routes
})

const app = new Vue({
    render: h => h(App),
    router
}).$mount('#app')