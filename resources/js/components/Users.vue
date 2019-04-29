<template>
    <div class="content-wrapper">
        <div class="content-header">
            <h1>
                Users
                    <!-- <small>this is the permissions page</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
                <li class="active">Users</li>
            </ol>
        </div>
        
        <div class="content container-fluid">
            <div class="col-xs-12">
                <!-- Your Page Content -->
                <div class="box box-solid box-primary">
                    <div class="box-header with-border">
                        <div class="box-title">List of Users</div>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <th class="col-sm-1">ID</th>
                                    <th class="col-sm-2">NIP</th>
                                    <th class="col-sm-2">Name</th>
                                    <th class="col-sm-2">Email</th>
                                    <th class="col-sm-2">Group</th>
                                    <th class="col-sm-1">Role</th>
                                    <th class="col-sm-2 text-center">Actions</th>
                                </tr>
                                <tr v-for="user in users" v-bind:key="user.id">
                                    <th class="col-sm-1">{{ user.id }}</th>
                                    <th class="col-sm-2">{{ user.username }}</th>
                                    <th class="col-sm-2">{{ user.name }}</th>
                                    <th class="col-sm-2">{{ user.email }}</th>
                                    <th class="col-sm-2">
                                        <div v-for="group in user.groups" v:bind:key="group.id">
                                            {{group.name}}
                                        </div>
                                    </th>
                                    <th class="col-sm-1">
                                        <div v-for="role in user.roles" v:bind:key="role.id">
                                            {{role.name}}
                                        </div>
                                    </th>
                                    <th class="row col-sm-2 text-right">
                                        <button class="btn btn-warning" @click="openUpdate(user.id)"
                                        data-toggle="modal" data-target="#modal-user">Update</button>
                                        <button class="btn btn-danger" @click="deleteUser(user.id)">Delete</button>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-right">
                    <button class="btn btn-primary" data-toggle="modal"
                    data-target="#modal-create">Create</button>
                </div>
                    
                <div class="text-right">
                    <ul class="pagination">
                        <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="paginate_button previous">
                            <a @click="fetchAllUsers(pagination.prev_page_url)">Previous</a>
                        </li>
                        <li class="page-item disabled">
                            <a class="page-link text-dark">Page {{ pagination.current_page }} of {{pagination.last_page}}</a>
                        </li>
                        <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="paginate_button next">
                            <a @click="fetchAllUsers(pagination.next_page_url)">Next</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div> <!-- /.content -->

        <!-- create new modal -->
        <div class="modal" id="modal-create" transition="modal">
            <div class="modal-wrapper">
                <div class="modal-dialog box box-default">
                    <div class="box-header with-border">
                        <slot name="header">Create new user</slot>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                    </div>
                    <form @submit.prevent="createNewUser" role="form">
                        <div class="box-body">
                            <div class="form-group">
                                <label>Username
                                    <input type="text" class="form-control"
                                    placeholder="New Username" v-model="user.username">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Name
                                    <input type="text" class="form-control"
                                    placeholder="New Name" v-model="user.name">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Email
                                    <input type="text" class="form-control"
                                    placeholder="New Email" v-model="user.email">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Password
                                    <input type="password" class="form-control"
                                    placeholder="New Password" v-model="user.password">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Confirm Password
                                    <input type="password" class="form-control"
                                    placeholder="Confirm Password" v-model="user.password_confirmation">
                                </label>
                            </div>
                            <div class="box-header with-border">
                                <slot name="header">List of Groups</slot>
                            </div>
                            <div class="modal-body">
                                <div v-for="group in groups" v-bind:key="group.id">
                                    <input type="checkbox" v-model="users_with_groups_after" :value="group.id"/> {{ group.name }}
                                </div>
                            </div>
                            <div class="box-header with-border">
                                <slot name="header">List of Roles</slot>
                            </div>
                            <div class="modal-body">
                                <div v-for="role in roles" v-bind:key="role.id">
                                    <input type="checkbox" v-model="users_with_roles_after" :value="role.id"/> {{ role.name }}
                                </div>
                            </div>
                        </div>
                    </form>    
                    <div class="modal-footer text-right">
                        <slot name="footer">
                            <button type="submit" class="btn btn-primary"
                            @click="createNewUser()" data-dismiss="modal">Create</button>
                        </slot>
                    </div>
                </div>
            </div>
        </div>
        <!-- / create new modal -->

        <!-- update modal -->
        <div class="modal" id="modal-user" transition="modal">
            <div class="modal-wrapper">
                <div class="modal-dialog box box-default">
                    <div class="modal-header">
                        <slot name="header">Update user</slot>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                    </div>
                    <form @submit.prevent="updateUser(user.id)" role="form">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Username
                                    <input type="text" class="form-control"
                                    placeholder="Update Username" v-model="user.username">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Name
                                    <input type="text" class="form-control"
                                    placeholder="Update name" v-model="user.name">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Email
                                    <input type="text" class="form-control"
                                    placeholder="Update email" v-model="user.email">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Password
                                    <input type="password" class="form-control"
                                    placeholder="Update password" v-model="user.password">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Confirm Password
                                    <input type="password" class="form-control"
                                    placeholder="Confirm password" v-model="user.password_confirmation">
                                </label>
                            </div>
                            <div class="box-header with-border">
                                <slot name="header">List of Groups</slot>
                            </div>
                            <div class="modal-body">
                                <div v-for="group in groups" v-bind:key="group.id">
                                    <input type="checkbox" v-model="users_with_groups_after" :value="group.id"/> {{ group.name }}
                                </div>
                            </div>
                            <div class="box-header with-border">
                                <slot name="header">List of Roles</slot>
                            </div>
                            <div class="modal-body">
                                <div v-for="role in roles" v-bind:key="role.id">
                                    <input type="checkbox" v-model="users_with_roles_after" :value="role.id"/> {{ role.name }}
                                </div>
                            </div>
                        </div>
                    </form>    
                    <div class="modal-footer text-right">
                        <slot name="footer">
                            <button type="submit" class="btn btn-warning"
                            @click="updateUser(user.id)" data-dismiss="modal">Update</button>
                        </slot>
                    </div>
                </div>
            </div>
        </div>
        <!-- / update modal -->
    </div> <!-- /.content-wrapper -->
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            users: [],
            roles: [],
            groups: [],
            user: {
                id: '',
                username: '',
                email: '',
                name: '',
                password: '',
                password_confirmation: '',
                groups: [],
                roles: [],
                groups_added: [],
                groups_removed: [],
                roles_added: [],
                roles_removed: [],
            },
            users_with_groups_before: [],
            users_with_groups_after: [],
            users_with_roles_after: [],
            users_with_roles_before: [],
            pagination: {},
        }
    },

    methods: {
        fetchAllUsers: function(page_url) {
            let url = page_url || 'http://localhost:8000/api/user';
            let data = {
                action: 'fetchAll' 
            };
            // let vm = this;
            let options = {
                method: 'post',
                data,
                url
            }

            axios(options)
                .then(res => {
                    this.users = res.data;
                    // vm.makePagination(res.data.meta, res.data.links);
                });
        },
        fetchAllRoles: function(page_url) {
            let url = page_url || 'http://localhost:8000/api/role';
            let data = {
                action: 'fetchAll' 
            };
            let vm = this;
            let options = {
                method: 'post',
                data,
                url
            }

            axios(options)
                .then(res => {
                    this.roles = res.data;
                });
        },
        fetchAllGroups: function(page_url) {
            let url = page_url || 'http://localhost:8000/api/group';
            let data = {
                action: 'fetchAll' 
            };
            let vm = this;
            let options = {
                method: 'post',
                data,
                url
            }

            axios(options)
                .then(res => {
                    this.groups = res.data.data;
                });
        },
        makePagination: function(meta, links) {
            let pagination = {
                current_page: meta.current_page,
                last_page: meta.last_page,
                next_page_url: links.next,
                prev_page_url: links.prev
            }
            
            this.pagination = pagination;
        },
        clearUser: function() {
            this.user = {
                id: '',
                username: '',
                email: '',
                name: '',
                password: '',
                password_confirmation: '',
                groups: [],
                roles: [],
                groups_added: [],
                groups_removed: [],
                roles_added: [],
                roles_removed: [],
            };
        },

        createNewUser: function() {
            this.user.groups_added = this.users_with_groups_after;
            this.user.roles_added = this.users_with_roles_after;

            let url = 'http://localhost:8000/api/user';
            let data = {
                action: 'create',
                name: this.user.name,
                username: this.user.username,
                email: this.user.email,
                password: this.user.password,
                password_confirmation: this.user.password_confirmation,
                groups_added: this.user.groups_added,
                roles_added: this.user.roles_added,
            }
            
            let options = {
                method: 'post',
                data,
                url
            }

            axios(options)
                .then(res => {
                    this.fetchAllUsers();
                })
            this.users_with_roles_after = []
            this.users_with_groups_after = []
            this.clearUser();
        },
        deleteUser: function(id) {
            let url = 'http://localhost:8000/api/user';
            let data = {
                action: 'delete',
                id: id
            }
            let options = {
                method: 'post',
                data,
                url
            }

            axios(options)
                .then(res => {
                    this.fetchAllUsers();
                })
        },
        openUpdate: function(id) {
            this.user.id = id
            let arr_temp = []
            var user_idx = ''
            
            this.users.forEach(function (value,key) {
                if (value.id==id){
                    user_idx = key;
                }
            })
            
            this.users[user_idx].groups.slice().forEach(function(group){
                arr_temp.push(group.id)
            })
            this.users_with_groups_after = arr_temp
            this.users_with_groups_before = arr_temp

            arr_temp = []
            this.users[user_idx].roles.slice().forEach(function(role){
                arr_temp.push(role.id)
            })
            this.users_with_roles_after = arr_temp
            this.users_with_roles_before = arr_temp

            this.user.name = this.users[user_idx].name;
            this.user.username = this.users[user_idx].username;
            this.user.email = this.users[user_idx].email;
            this.user.password = ''
            this.user.password_confirmation = ''
            
        },
        updateUser: function(id) {

            // calculate groups' removed and added
            this.user.groups_removed = this.users_with_groups_before
                                    .filter(x => !this.users_with_groups_after.includes(x))

            this.user.groups_added = this.users_with_groups_after
                                    .filter(x => !this.users_with_groups_before.includes(x))
            this.users_with_groups_before = this.users_with_groups_after

            // calculate roles' removed and added
            this.user.roles_removed = this.users_with_roles_before
                                    .filter(x => !this.users_with_roles_after.includes(x))

            this.user.roles_added = this.users_with_roles_after
                                    .filter(x => !this.users_with_roles_before.includes(x))
            this.users_with_roles_before = this.users_with_roles_after

            let url = 'http://localhost:8000/api/user';
            let data = {
                action: 'update',
                id: id,
                name: this.user.name,
                username: this.user.username,
                email: this.user.email,
                password: this.user.password,
                password_confirmation: this.user.password_confirmation,
                groups_added: this.user.groups_added,
                groups_removed: this.user.groups_removed,
                roles_added: this.user.roles_added,
                roles_removed: this.user.roles_removed,
            }
            console.log(data)
            let options = {
                method: 'post',
                data,
                url
            }

            axios(options)
                .then(res => {
                    console.log(res)
                    this.fetchAllUsers();
                })
            this.clearUser();
        },
    },
    created: function() {
        this.fetchAllUsers();
        this.fetchAllGroups();
        this.fetchAllRoles();
    }
}
</script>