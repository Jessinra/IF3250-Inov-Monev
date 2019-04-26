<template>
    <div class="content-wrapper" id = "app">
        <div class="content-header">
            <h1>
                Roles
                <small>This is the roles page</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
                <li class="active">Roles</li>
            </ol>
        </div>
        
        <div class="content container-fluid">
            <div class="col-xs-12">
                <!-- Your Page Content -->
                <div class="box box-solid box-primary">
                    <div class="box-header with-border">
                        <div class="box-title">List of Roles</div>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <th class="col-sm-1 text-center">ID</th>
                                    <th class="col-sm-2 text-center">Name</th>
                                    <th class="col-sm-3 text-center">Description</th>
                                    <th class="col-sm-4 text-center">Action</th>
                                </tr>
                                <tr v-for="role in roles" v-bind:key="role.id">
                                    <th class="col-sm-1 text-center">{{ role.id }}</th>
                                    <th class="col-sm-2 text-center">{{ role.name }}</th>
                                    <th class="col-sm-3 text-center">{{ role.description }}</th>
                                    <th class="col-sm-4 text-center">
                                        <button class="btn btn-primary" @click="setCurrentRole(role)" data-toggle="modal" data-target="#modal-create-and-edit">Edit This Role</button>
                                        <button class="btn btn-danger" @click="deleteRole(role.id)">Delete This Role</button>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-right">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modal-create-and-edit">Create New Role</button>
                </div>
                    
                <div class="text-right">
                    <ul class="pagination">
                        <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="paginate_button previous">
                            <a @click="fetchAllRoles(pagination.prev_page_url)">Previous</a>
                        </li>
                        <li class="page-item disabled">
                            <a class="page-link text-dark">Page {{ pagination.current_page }} of {{pagination.last_page}}</a>
                        </li>
                        <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="paginate_button next">
                            <a @click="fetchAllRoles(pagination.next_page_url)">Next</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div> <!-- /.content -->

        <div class="modal" id="modal-create-and-edit" transition="modal" @close = "edit_mode = false">
            <div class="modal-wrapper">
                <div class="modal-dialog box box-default">
                    <div class="box-header with-border">
                        <slot v-if="edit_mode" name="header">Edit existing role</slot>
                        <slot v-else name="header">Create new role</slot>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                    </div>
                    <form @submit.prevent="createNewRole" role="form">
                        <div class="box-body">
                            <div class="form-group">
                                <label>Name
                                    <input v-if="edit_mode" type="text" class="form-control" placeholder="New name" v-model="current_selected_role.name">
                                    <input v-else type="text" class="form-control" placeholder="New name" v-model="new_role.name">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Description
                                    <input v-if="edit_mode" type="text" class="form-control" placeholder="New description" v-model="current_selected_role.description">
                                    <input v-else type="text" class="form-control" placeholder="New description" v-model="new_role.description">
                                </label>
                            </div>
                        </div>
                    </form>    

                    <div>
                        <div v-if="edit_mode" v-for="permission in permissions" v-bind:key="current_selected_role.id">
                            <input type="checkbox" v-model="current_selected_role.permissions" :value="permission"/> {{ permission.name }}
                        </div>
                    </div>




                    <div class="modal-footer text-right">
                        <slot name="footer">
                            <button v-if="edit_mode" type="submit" class="btn btn-primary" @click="editExistingRole" data-dismiss="modal">Edit</button>
                            <button v-else type="submit" class="btn btn-primary" @click="createNewRole" data-dismiss="modal">Create</button>
                        </slot>
                    </div>
                </div>
            </div>
        </div>

        <!-- create new modal -->
        <div class="modal" id="modal-roles-show-permissions" transition="modal">
            <div class="modal-wrapper">
                <div class="modal-dialog box box-default">
                    <div class="box-header with-border">
                        <slot name="header">List of Permissions</slot>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-footer text-right">
                        <slot name="footer">
                            <button type="submit" class="btn btn-warning"
                            @click="updateRolesPermission" data-dismiss="modal">Update</button>
                        </slot>
                    </div>
                </div>
            </div>
        </div>
        <!-- / create new modal -->

        <!-- update modal -->

        <!-- / update modal -->
    </div> <!-- /.content-wrapper -->



</template>



<script>
import axios from 'axios';
import Name from './UserName';

export default {
    data() {
        return {
            permissions: [],
            roles: [],
            new_role: {
                name: '',
                description: '',
            },
            roles_with_permissions: [],
            edit_mode: false,
            current_selected_role: '',
            pagination: {}
        }
    },
    methods: {
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
                    console.log(this.roles);
                });         
        },
        fetchPermissionsOfRole: function(page_url) {
            let url = page_url || 'http://localhost:8000/api/permissions';
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
        fetchAllPermissions: function(page_url) {
            let url = page_url || 'http://localhost:8000/api/permissions';
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
                    this.permissions = res.data.data;
                    vm.makePagination(res.data.meta, res.data.links);
                });
        },
        createNewRole: function() {
            let url = 'http://localhost:8000/api/role';
            let data = {
                action: 'create',
                name: this.new_role.name,
                description: this.new_role.description,
            }
            let options = {
                method: 'post',
                data,
                url
            }

            axios(options)
                .then(res => {
                    console.log(res);
                    this.fetchAllRoles();
                })
        },
        deleteRole: function(id) {
            let url = 'http://localhost:8000/api/role';
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
                    this.fetchAllRoles();
                })
        },
        editExistingRole: function() {
            let url = 'http://localhost:8000/api/role';
            let data = {
                action: 'update',
                id: this.current_selected_role.id,
                name: this.current_selected_role.name,
                description: this.current_selected_role.description,
                permissions: this.current_selected_role.permissions
            }
            let options = {
                method: 'post',
                data,
                url
            }

            this.edit_mode = false;
            this.new_role.name = "";
            this.new_role.description = "";

            axios(options)
                .then(res => {
                    console.log(res);
                    this.fetchAllRoles();
                })
        },
        setCurrentRole: function(role) {
            this.current_selected_role = role;
            this.edit_mode = true;
        },
        updateRolesPermission: function() {
            console.log("Update permission pressed");
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
        createNewPermission: function() {
            let url = 'http://localhost:8000/api/permissions';
            let data = {
                action: 'create',
                name: this.permission.name,
                description: this.permission.description
            }
            let options = {
                method: 'post',
                data,
                url
            }

            axios(options)
                .then(res => {
                    console.log(res);
                    this.fetchAllPermissions();
                })
        },
        deletePermission: function(id) {
            let url = 'http://localhost:8000/api/permissions';
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
                    this.fetchAllPermissions();
                })
        },
        openUpdate: function(id) {
            this.permission.id = id;
        },
        updatePermission: function(id) {
            let url = 'http://localhost:8000/api/permissions';
            let data = {
                action: 'update',
                id: id,
                name: this.permission.name,
                description: this.permission.description
            }
            let options = {
                method: 'post',
                data,
                url
            }

            axios(options)
                .then(res => {
                    console.log(res);
                    this.fetchAllPermissions();
                })
        }
    },
    created: function() {
        this.fetchAllRoles();
        this.fetchAllPermissions();
    }
}
</script>