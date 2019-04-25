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
                                    <th class="col-sm-2">email</th>
                                    <th class="col-sm-2">Group</th>
                                    <th class="col-sm-1">Role</th>
                                    <th class="col-sm-2 text-right">Actions</th>
                                </tr>
                                <tr v-for="user in users" v-bind:key="user.id">
                                    <th class="col-sm-1">{{ user.id }}</th>
                                    <th class="col-sm-2">{{ user.username }}</th>
                                    <th class="col-sm-2">{{ user.name }}</th>
                                    <th class="col-sm-2">{{ user.email }}</th>
                                    <th class="col-sm-2">{{ user.roleId }}</th>
                                    <th class="col-sm-1">{{ user.groupId }}</th>
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
                            <div class="form-group">
                                <label>Role Id
                                    <input type="text" class="form-control"
                                    placeholder="New Role Id" v-model="user.roleId">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Group Id
                                    <input type="text" class="form-control"
                                    placeholder="New Group Id" v-model="user.groupId">
                                </label>
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
                            <div class="form-group">
                                <label>Role Id
                                    <input type="text" class="form-control"
                                    placeholder="Update Role ID" v-model="user.roleId">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Group Id
                                    <input type="text" class="form-control"
                                    placeholder="Update Group ID" v-model="user.groupId">
                                </label>
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
            user: {
                id: '',
                username: '',
                email: '',
                name: '',
                password: '',
                password_confirmation: '',
                roleId: '',
                groupId:''
            },
            user_id: '',
            pagination: {}
        }
    },

    methods: {
        fetchAllUsers: function(page_url) {
            let url = page_url || 'http://localhost:8000/api/user';
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
                    console.log(res);
                    this.users = res.data.data;
                    vm.makePagination(res.data.meta, res.data.links);
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
        createNewUser: function() {
            let url = 'http://localhost:8000/api/user';
            let data = {
                action: 'create',
                name: this.user.name,
                username: this.user.username,
                email: this.user.email,
                password: this.user.password,
                password_confirmation: this.user.password_confirmation,
                roleId: this.user.roleId,
                groupId: this.user.groupId
            }
            let options = {
                method: 'post',
                data,
                url
            }

            axios(options)
                .then(res => {
                    console.log(res);
                    this.fetchAllUsers();
                })
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
                    console.log(res);
                    this.fetchAllUsers();
                })
        },
        openUpdate: function(id) {
            this.user.id = id;
        },
        updateUser: function(id) {
            let url = 'http://localhost:8000/api/user';
            let data = {
                action: 'update',
                id: id,
                name: this.user.name,
                username: this.user.username,
                email: this.user.email,
                password: this.user.password,
                password_confirmation: this.user.password_confirmation,
                roleId: this.user.roleId,
                groupId: this.user.groupId,
            }
            let options = {
                method: 'post',
                data,
                url
            }

            axios(options)
                .then(res => {
                    console.log(res);
                    this.fetchAllUsers();
                })
        }
    },
    created: function() {
        this.fetchAllUsers();
    }
}
</script>