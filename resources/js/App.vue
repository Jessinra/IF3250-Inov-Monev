<template>
    <div class="wrapper">
        <!-- Header -->
        <Header/>
        
        <!-- Sidebar -->
        <Sidebar/>

        <!-- Control Sidebar -->
        <Controlbar/>
        <!-- /.control-sidebar -->
        <!-- Add the sidebar's background. This div must be placed
        immediately after the control sidebar -->
        <div class="control-sidebar-bg"/>

        <!-- Content -->
        <router-view/>

        <!-- Main Footer -->
        <Footer/>
    </div>
</template>
<script>
    import Header from './components/Header.vue'
    import Sidebar from './components/Sidebar.vue'
    import Controlbar from './components/Controlbar.vue'
    import Footer from './components/Footer.vue'

    export default{
        name: 'app',
        components: {
            Header,
            Sidebar,
            Controlbar,
            Footer
        },
        data() {
            return {
                name: null,
                isLoggedIn: localStorage.getItem('inovmonev.jwt') != null
            }
        },
        mounted() {
            this.setDefaults()
        },
        methods : {
            setDefaults() {
                if (this.isLoggedIn) {
                    let user = JSON.parse(localStorage.getItem('inovmonev.user'))
                    this.name = user.name
                }
            },
            change() {
                this.isLoggedIn = localStorage.getItem('inovmonev.jwt') != null
                this.setDefaults()
            },
            logout(){
                localStorage.removeItem('inovmonev.jwt')
                localStorage.removeItem('inovmonev.user')
                this.change()
                this.$router.push('/')
            }
        }
    }
    console.log(localStorage.getItem('jwt'))
</script>