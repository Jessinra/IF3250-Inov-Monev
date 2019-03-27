<template>
    <div class="content-wrapper">
        <div class="content-header">
            <h1>
                Permissions
                    <small>this is the permissions page</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
                <li class="active">Permissions</li>
            </ol>
        </div>
        
        <div class="content container-fluid">
            <div class="col-xs-12">
                <!-- Your Page Content -->
                <div class="box box-solid box-primary">
                    <div class="box-header with-border">
                        <div class="box-title">List of Permissions</div>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <th class="col-sm-1">ID</th>
                                    <th class="col-sm-2">Name</th>
                                    <th class="col-sm-7">Description</th>
                                    <th class="col-sm-2 text-right">Actions</th>
                                </tr>
                                <tr v-for="permission in permissions" v-bind:key="permission.id">
                                    <th class="col-sm-1">{{ permission.id }}</th>
                                    <th class="col-sm-2">{{ permission.name }}</th>
                                    <th class="col-sm-7">{{ permission.description }}</th>
                                    <th class="row col-sm-2 text-right">
                                        <button class="btn btn-warning" @click="openUpdate(permission.id)"
                                        data-toggle="modal" data-target="#modal-permission">Update</button>
                                        <button class="btn btn-danger" @click="deletePermission(permission.id)">Delete</button>
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
                            <a @click="fetchAllPermissions(pagination.prev_page_url)">Previous</a>
                        </li>
                        <li class="page-item disabled">
                            <a class="page-link text-dark">Page {{ pagination.current_page }} of {{pagination.last_page}}</a>
                        </li>
                        <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="paginate_button next">
                            <a @click="fetchAllPermissions(pagination.next_page_url)">Next</a>
                        </li>
                    </ul>
                </div>
                
                <!-- alternative create new permission box -->
                <!-- <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="box-title">Create new permission</div>
                    </div>
                    <form @submit.prevent="createNewPermission" role="form">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputName">Name</label>
                                <input type="text" class="form-control" id="inputName" 
                                placeholder="Enter permission's name" v-model="permission.name">
                            </div>
                            <div class="form-group">
                                <label for="inputDesc">Description</label>
                                <input type="text" class="form-control" id="inputDesc" 
                                placeholder="Enter permission's description" v-model="permission.description">
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div> -->
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
                    <form @submit.prevent="createNewPermission" role="form">
                        <div class="box-body">
                            <div class="form-group">
                                <label>Name
                                    <input type="text" class="form-control"
                                    placeholder="New name" v-model="permission.name">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Description
                                    <input type="text" class="form-control"
                                    placeholder="New description" v-model="permission.description">
                                </label>
                            </div>
                        </div>
                    </form>    
                    <div class="modal-footer text-right">
                        <slot name="footer">
                            <button type="submit" class="btn btn-primary"
                            @click="createNewPermission()" data-dismiss="modal">Create</button>
                        </slot>
                    </div>
                </div>
            </div>
        </div>
        <!-- / create new modal -->

        <!-- update modal -->
        <div class="modal" id="modal-permission" transition="modal">
            <div class="modal-wrapper">
                <div class="modal-dialog box box-default">
                    <div class="modal-header">
                        <slot name="header">Update permission</slot>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                    </div>
                    <form @submit.prevent="updatePermission(permission.id)" role="form">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Name
                                    <input type="text" class="form-control"
                                    placeholder="Update name" v-model="permission.name">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Description
                                    <input type="text" class="form-control"
                                    placeholder="Update description" v-model="permission.description">
                                </label>
                            </div>
                        </div>
                    </form>    
                    <div class="modal-footer text-right">
                        <slot name="footer">
                            <button type="submit" class="btn btn-warning"
                            @click="updatePermission(permission.id)" data-dismiss="modal">Update</button>
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
            permissions: [],
            permission: {
                id: '',
                name: '',
                description: ''
            },
            permission_id: '',
            pagination: {},
            showUpdate: false,
            showCreate: false
        }
    },
    methods: {
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
        this.fetchAllPermissions();
    }
}
</script>