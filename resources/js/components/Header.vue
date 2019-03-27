<template>
    <!-- Main Header -->
    <header class="main-header">
        <!-- Logo -->
        <router-link to='/dashboard' class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>P</b>LB</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Pusda</b>litbang</span>
        </router-link>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
            <div v-if="isLoggedIn">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
                </a>
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    
                    <!-- User Account Menu -->
                    <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        <img src="dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">{{name}}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                        <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">

                        <p>
                            {{name}} - Web Developer
                            <small>Member since Nov. 2012</small>
                        </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                        <div class="row">
                            <div class="col-xs-4 text-center">
                            <a href="#">Followers</a>
                            </div>
                            <div class="col-xs-4 text-center">
                            <a href="#">Sales</a>
                            </div>
                            <div class="col-xs-4 text-center">
                            <a href="#">Friends</a>
                            </div>
                        </div>
                        <!-- /.row -->
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                        <div class="pull-left">
                            <a href="#" class="btn btn-default btn-flat">Profile</a>
                        </div>
                        <div class="pull-right">
                            <a href="#" class="btn btn-default btn-flat">Sign out</a>
                        </div>
                        </li>
                    </ul>
                    </li>
                    <!-- Control Sidebar Toggle Button -->
                    <li @click="logout"> Logout</li>
                </ul>
                </div>
            </div>
            <div v-if="!isLoggedIn">
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li>
                    <router-link :to="{ name: 'login' }" class="nav-link" v-if="!isLoggedIn">Login</router-link>
                    </li>
                </ul>
                </div>
            </div
        </nav>
    </header>
</template>

<script>
    export default {
        name: 'Header',
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
                    console.log(name)
                }
            },
            change() {
                this.isLoggedIn = localStorage.getItem('inovmonev.jwt') != null
                this.setDefaults()
            },
            logout(){
                console.log('clicked')
                localStorage.removeItem('inovmonev.jwt')
                localStorage.removeItem('inovmonev.user')
                this.change()
                this.$router.push('/')
            }
        }
    }
</script>

