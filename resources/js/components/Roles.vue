<template>
    <div class="content-wrapper">
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
                                    <th class="col-sm-1">ID</th>
                                    <th class="col-sm-2">Name</th>
                                    <th class="col-sm-3">Description</th>
                                    <th class="col-sm-4">Permissions Granted</th>
                                </tr>
                                <tr v-for="role in roles" v-bind:key="role.id">
                                    <th class="col-sm-1">{{ role.id }}</th>
                                    <th class="col-sm-2">{{ role.name }}</th>
                                    <th class="col-sm-3">{{ role.description }}</th>
                                    <th class="col-sm-4">
                                        <div>
                                            <tr>
                                                <th class="col-sm-2">
                                                    <input type="checkbox" v-model="boolean">Testing1
                                                </th>
                                                <th class="col-sm-2">
                                                    <input type="checkbox" v-model="boolean">Testing2
                                                </th>
                                                <th class="col-sm-2">
                                                    <input type="checkbox" v-model="boolean">Testing3
                                                </th>
                                            <tr>
                                        </div>
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

        <!-- create new modal -->
        <div class="modal" id="modal-create" transition="modal">
            <div class="modal-wrapper">
                <div class="modal-dialog box box-default">
                    <div class="box-header with-border">
                        <slot name="header">Create new permission</slot>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
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

export default {
    data() {
        return {
            permissions = [],
            roles: [],
            role: {
                id: '',
                name: '',
                description: ''
            },
            permission_id: '',
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
                    console.log(res);
                    this.roles = res.data;
                    console.log(this.roles)
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
                    console.log(res);
                    this.permissions = res.data.data;
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
    }
}
</script>