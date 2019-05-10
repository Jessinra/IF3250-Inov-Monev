<template>
    <div class="content-wrapper">
        <div class="content-header">
            <h1>
                Projects
                    <small>this is the projects page</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
                <li class="active">Projects</li>
            </ol>
        </div>
        
        <div class="content container-fluid">
            <div class="col-xs-12">
                <!-- Your Page Content -->
                <div class="box box-solid box-primary">
                    <div class="box-header with-border">
                        <div class="box-title">List of Projects</div>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <th class="col-sm-1">ID</th>
                                    <th class="col-sm-2">Name</th>
                                    <th class="col-sm-4">Description</th>
                                    <th class="col-sm-2">File</th>
                                    <th class="col-sm-3 text-right">Actions</th>
                                </tr>
                                <tr v-for="project in projects" v-bind:key="project.id">
                                    <th class="col-sm-1">{{ project.id }}</th>
                                    <th class="col-sm-2">{{ project.name }}</th>
                                    <th class="col-sm-4">{{ project.description }}</th>
                                    <th class="col-sm-2">{{ project.file }}</th>
                                    <th class="row col-sm-3 text-right">
                                        <button class="btn btn-warning" @click="openUpdate(project.id)"
                                        data-toggle="modal" data-target="#modal-update">Update</button>
                                        <button class="btn btn-danger" @click="deleteProject(project.id)">Delete</button>
                                        <button class="btn btn-info" @click="openNote(project.id)">Notes</button>
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
                            <a @click="fetchAllProjects(pagination.prev_page_url)">Previous</a>
                        </li>
                        <li class="page-item disabled">
                            <a class="page-link text-dark">Page {{ pagination.current_page }} of {{pagination.last_page}}</a>
                        </li>
                        <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="paginate_button next">
                            <a @click="fetchAllProjects(pagination.next_page_url)">Next</a>
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
                        <slot name="header">Submit new project</slot>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                    </div>
                    <form @submit.prevent="createNewProject" role="form">
                        <div class="box-body">
                            <div class="form-group">
                                <label>Name
                                    <input type="text" class="form-control"
                                    placeholder="New name" v-model="project.name">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Description
                                    <input type="text" class="form-control"
                                    placeholder="New description" v-model="project.description">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>File
                                    <input type="file" @change="handleFile">
                                </label>
                        </div>
                    </form>    
                    <div class="modal-footer text-right">
                        <slot name="footer">
                            <button type="submit" class="btn btn-primary"
                            @click="createNewProject()" data-dismiss="modal">Create</button>
                        </slot>
                    </div>
                </div>
            </div>
        </div>
        <!-- / create new modal -->

        <!-- update modal -->
        <div class="modal" id="modal-update" transition="modal">
            <div class="modal-wrapper">
                <div class="modal-dialog box box-default">
                    <div class="box-header with-border">
                        <slot name="header">Update project</slot>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                    </div>
                    <form @submit.prevent="updateProject(project.id)" role="form">
                        <div class="box-body">
                            <div class="form-group">
                                <label>Name
                                    <input type="text" class="form-control"
                                    placeholder="New name" v-model="project.name">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Description
                                    <input type="text" class="form-control"
                                    placeholder="New description" v-model="project.description">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>File
                                    <input type="file" @change="handleFile">
                                </label>
                        </div>
                    </form>    
                    <div class="modal-footer text-right">
                        <slot name="footer">
                            <button type="submit" class="btn btn-primary"
                            @click="updateProject(project.id)" data-dismiss="modal">Update</button>
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
            projects: [],
            project: {
                id: '',
                name: '',
                description: '',
                file: '',
                uploader: ''
            },
            project_id: '',
            pagination: {}
        }
    },
    methods: {
        fetchAllProjects: function(page_url) {
            let url = page_url || 'http://localhost:8000/api/projects';
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
                    this.projects = res.data.data;
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
        handleFile: function(e) {
            this.project.file = e.target.files || e.dataTransfer.files;
            if (!this.project.file.length)
                return
            this.project.file = this.project.file[0];
            console.log(this.project.file);
        },
        createNewProject: function() {
            let user = JSON.parse(localStorage.getItem('inovmonev.user'));
            let url = 'http://localhost:8000/api/projects';
            let formData = new FormData();

            formData.append('action', 'create');
            formData.append('name', this.project.name);
            formData.append('description', this.project.description);
            formData.append('file', this.project.file);
            formData.append('uploader', user.id);
            
            let options = {
                header: {
                    'Content-Type': 'multipart/form-data'
                },
                method: 'post',
                data: formData,
                url
            }

            axios(options)
                .then(res => {
                    console.log(res);
                    this.fetchAllProjects();
                })
        },
        deleteProject: function(id) {
            let url = 'http://localhost:8000/api/projects';
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
                    this.fetchAllProjects();
                })
        },
        openUpdate: function(id) {
            this.project.id = id;
        },
        updateProject: function(id) {
            let user = JSON.parse(localStorage.getItem('inovmonev.user'));
            let url = 'http://localhost:8000/api/projects';
            let formData = new FormData();

            formData.append('action', 'update');
            formData.append('id', this.project.id);
            formData.append('name', this.project.name);
            formData.append('description', this.project.description);
            formData.append('file', this.project.file);
            formData.append('uploader', user.id);

            let options = {
                header: {
                    'Content-Type': 'multipart/form-data'
                },
                method: 'post',
                data: formData,
                url
            }

            axios(options)
                .then(res => {
                    console.log(res);
                    this.fetchAllProjects();
                })
        },
        openNote: function(id) {
            this.$router.push({
                name: 'notes',
                params: {
                    id: id
                }
            })
        }
    },
    created: function() {
        this.fetchAllProjects();
    }
}
</script>