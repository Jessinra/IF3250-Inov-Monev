<template>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <div class="sidebar">

            <!-- Sidebar user panel (optional) -->
            <div class="user-panel">
            <div class="pull-left image">
                <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{name}}</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
            </div>

            <!-- search form (Optional) -->
            <!-- <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
            </form> -->
            <!-- /.search form -->

            <!-- Sidebar Menu -->
            <ul class="sidebar-menu" data-widget="tree">
                <li class="header">Categories</li>
                <!-- Optionally, you can add icons to the links -->
                <li class="active"><router-link to="/dashboard">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span></router-link></li>
                <li><router-link to="/projects">
                    <i class="fa fa-link"></i>
                    <span>Project</span></router-link></li>
                <li><router-link to="/permissions">
                    <i class="fa fa-link"></i>
                    <span>Permission</span></router-link></li>
                <li>
                    <router-link to="/roles">
                        <i class="fa fa-link"></i>
                        <span>Roles</span>
                    </router-link>
                </li>
                <li><router-link to="/users">
                    <i class="fa fa-link"></i>
                    <span>User</span></router-link></li>
                    <li><router-link to="/groups">
                    <i class="fa fa-link"></i>
                    <span>Group</span></router-link></li>
            </ul>
        <!-- /.sidebar-menu -->
        </div>
    <!-- /.sidebar -->
    </aside>
</template>

<script>
    export default{
        name: 'Sidebar',
        data() {
            return {
                name: '',
                isLoggedIn: localStorage.getItem('inovmonev.jwt') != null,
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
        }
    }
</script>