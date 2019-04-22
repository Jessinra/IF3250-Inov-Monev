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

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        console.log(localStorage.getItem('inovmonev.jwt'));
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