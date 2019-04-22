import Home from './components/ExampleComponent.vue'
import Login from './components/Login.vue'
import PermissionsComponent from './components/Permissions.vue'
import DashboardComponent from './components/Dashboard.vue'
import ProjectsComponent from './components/Projects.vue'

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
        path: '/dashboard',
        name: 'dashboard',
        component: DashboardComponent,
        meta: {
            requiresAuth: true,
        }
    },
    {
        path: '/projects',
        name: 'projects',
        component: ProjectsComponent,
        meta: {
            requiresAuth: true,
        }
    }
]

export default routes