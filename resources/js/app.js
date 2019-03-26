require('./bootstrap');

import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter);

import App from './App.vue'
import Home from './components/ExampleComponent.vue'
import Login from './components/Login.vue'
import PermissionsComponent from './components/Permissions.vue'
import DashboardComponent from './components/Dashboard.vue'

let routes= [
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/login',
        name: 'login',
        component: Login
    },
    {
        path: '/permissions',
        component: PermissionsComponent,
        meta: {
            requiresAuth: true,
        }
    },
    {
        path: '/dashboard',
        component: DashboardComponent,
        meta: {
            requiresAuth: true,
        }
    }
]

const router = new VueRouter({
    mode: 'history',
    routes: routes
})

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (localStorage.getItem('inovmonev.jwt') == null) {
            next({
                path: '/login',
                params: { nextUrl: to.fullPath }
            })
        } else {
            let user = JSON.parse(localStorage.getItem('inovmonev.user'))
            next()
        }
    } else {
        next()
    }
})

const app = new Vue({
    render: h => h(App),
    router
}).$mount('#app')