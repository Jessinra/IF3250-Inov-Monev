require('./bootstrap');

import Vue from 'vue'
import VueRouter from 'vue-router'
import Meta from 'vue-meta'

Vue.use(VueRouter);
Vue.use(Meta);

import App from './App.vue'
import PermissionsComponent from './components/Permissions.vue'
import DashboardComponent from './components/Dashboard.vue'

let routes= [
    {
        path: '/permissions',
        component: PermissionsComponent
    },
    {
        path: '/dashboard',
        component: DashboardComponent
    }
]

const router = new VueRouter({
    mode: 'history',
    routes: routes
})

const app = new Vue({
    render: h => h(App),
    router
}).$mount('#app')