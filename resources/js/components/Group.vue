<template>
    <div class="content-wrapper">
        <div class="content-header">
            <h1>
                Group
                <small>this is the group page</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
                <li class="active">Group</li>
            </ol>
        </div>

        <div class="content container-fluid">
            <div class="col-xs-12">
                <!-- Your Page Content -->
                <div class="box box-solid box-primary">
                    <div class="box-header with-border">
                        <div class="box-title">List of Group</div>
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
                            <tr v-bind:key="group.id" v-for="group in groups">
                                <th class="col-sm-1">{{ group.id }}</th>
                                <th class="col-sm-2">{{ group.name }}</th>
                                <th class="col-sm-7">{{ group.description }}</th>
                                <th class="row col-sm-2 text-right">
                                    <button @click="openUpdate(group.id)" class="btn btn-warning"
                                            data-target="#modal-permission" data-toggle="modal">Update
                                    </button>
                                    <button @click="deleteGroup(group.id)" class="btn btn-danger">Delete</button>
                                </th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-right">
                    <button class="btn btn-primary" data-target="#modal-create"
                            data-toggle="modal">Create
                    </button>
                </div>

                <div class="text-right">
                    <ul class="pagination">
                        <li class="paginate_button previous" v-bind:class="[{disabled: !pagination.prev_page_url}]">
                            <a @click="fetchAllGroup(pagination.prev_page_url)">Previous</a>
                        </li>
                        <li class="page-item disabled">
                            <a class="page-link text-dark">Page {{ pagination.current_page }} of
                                {{pagination.last_page}}</a>
                        </li>
                        <li class="paginate_button next" v-bind:class="[{disabled: !pagination.next_page_url}]">
                            <a @click="fetchAllGroup(pagination.next_page_url)">Next</a>
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
                        <slot name="header">Create new group</slot>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                            <span aria-hidden="true">&times;</span></button>
                    </div>
                    <form @submit.prevent="createNewGroup" role="form">
                        <div class="box-body">
                            <div class="form-group">
                                <label>Name
                                    <input class="form-control" placeholder="New name"
                                           type="text" v-model="group.name">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Description
                                    <input class="form-control" placeholder="New description"
                                           type="text" v-model="group.description">
                                </label>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer text-right">
                        <slot name="footer">
                            <button @click="createNewGroup()" class="btn btn-primary"
                                    data-dismiss="modal" type="submit">Create
                            </button>
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
                        <slot name="header">Update group</slot>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                            <span aria-hidden="true">&times;</span></button>
                    </div>
                    <form @submit.prevent="updateGroup(group.id)" role="form">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Name
                                    <input class="form-control" placeholder="Update name"
                                           type="text" v-model="group.name">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Description
                                    <input class="form-control" placeholder="Update description"
                                           type="text" v-model="group.description">
                                </label>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer text-right">
                        <slot name="footer">
                            <button @click="updateGroup(group.id)" class="btn btn-warning"
                                    data-dismiss="modal" type="submit">Update
                            </button>
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
                groups: [],
                group: {
                    id: '',
                    name: '',
                    description: ''
                },
                group_id: '',
                pagination: {}
            }
        },
        methods: {
            fetchAllGroup: function (page_url) {
                let url = page_url || 'http://localhost:8000/api/group';
                let data = {
                    action: 'fetchAll'
                };
                let vm = this;
                let options = {
                    method: 'post',
                    data,
                    url
                };

                axios(options)
                    .then(res => {
                        console.log(res);
                        this.groups = res.data.data;
                        vm.makePagination(res.data.meta, res.data.links);
                    });
            },
            makePagination: function (meta, links) {
                let pagination = {
                    current_page: meta.current_page,
                    last_page: meta.last_page,
                    next_page_url: links.next,
                    prev_page_url: links.prev
                };

                this.pagination = pagination;
            },
            createNewGroup: function () {
                let url = 'http://localhost:8000/api/group';
                let data = {
                    action: 'create',
                    name: this.group.name,
                    description: this.group.description
                };
                let options = {
                    method: 'post',
                    data,
                    url
                };

                axios(options)
                    .then(res => {
                        console.log(res);
                        this.fetchAllGroup();
                    })
            },
            deleteGroup: function (id) {
                let url = 'http://localhost:8000/api/group';
                let data = {
                    action: 'delete',
                    id: id
                };
                let options = {
                    method: 'post',
                    data,
                    url
                };

                axios(options)
                    .then(res => {
                        console.log(res);
                        this.fetchAllGroup();
                    })
            },
            openUpdate: function(id) {
                this.group.id = id;
            },
            updateGroup: function (id) {
                let url = 'http://localhost:8000/api/group';
                let data = {
                    action: 'update',
                    id: id,
                    name: this.group.name,
                    description: this.group.description
                };
                let options = {
                    method: 'post',
                    data,
                    url
                };

                axios(options)
                    .then(res => {
                        console.log(res);
                        this.fetchAllGroup();
                    })
            }
        },
        created: function () {
            this.fetchAllGroup();
        }
    }
</script>