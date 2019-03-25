import PermissionsComponent from './components/Permissions.vue'
import DashboardComponent from './components/Dashboard.vue'

const routes= [
    {
        path: '/permissions',
        component: PermissionsComponent
    },
    {
        path: '/dashboard',
        component: DashboardComponent
    }
]

export default routes