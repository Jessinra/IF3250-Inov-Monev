import Home from './components/ExampleComponent.vue'
import Login from './components/Login.vue'
import PermissionsComponent from './components/Permissions.vue'
import DashboardComponent from './components/Dashboard.vue'
import RolesComponent from './components/Roles.vue'
import UsersComponent from './components/Users.vue'

const routes= [
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
        name: 'permissions',
        component: PermissionsComponent,
        meta: {
            requiresAuth: true,
        }
    },
    {
        path: '/roles',
        name: 'roles',
        component: RolesComponent,
        meta: {
            requiresAuth: true,
        }
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: DashboardComponent,
        meta: {
            requiresAuth: true,
        }
    },
    {
        path: '/users',
        name: 'users',
        component: UsersComponent,
        meta: {
            requiresAuth: true,
        }
    }
]

export default routes