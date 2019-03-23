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
        }
    }
</script>