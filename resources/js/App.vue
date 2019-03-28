<template>
    <div class="wrapper">
        <div v-if="showComponents">
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
        </div>
    
        <!-- Content -->
        <router-view/>

        <!-- Main Footer -->
        <div v-if="showComponents">
            <Footer/>
        </div>
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
                isLoggedIn: localStorage.getItem('inovmonev.jwt') != null,
                showComponents: this.isLoggedIn
            }
        },
        mounted() {
            this.setDefaults()
        },
        methods : {
            setDefaults() {
                this.showComponents = this.isLoggedIn
                if (this.isLoggedIn) {
                    let user = JSON.parse(localStorage.getItem('inovmonev.user'))
                    this.name = user.name
                }
            },
            change() {
                this.isLoggedIn = localStorage.getItem('inovmonev.jwt') != null
                this.setDefaults()
            }
        },
        watch: {
            $route: function() {
                if (this.$route.name != 'login') {
                    console.log('watched true');
                    this.showComponents = true;
                } else {
                    console.log('watched false');
                    this.showComponents = false;
                }
            }
        }
    }
</script>