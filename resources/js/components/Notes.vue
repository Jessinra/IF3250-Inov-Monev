<template>
    <div class="content-wrapper">
        <div class="content-header">
            <h1>
                Notes
                    <small>this is the notes page</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
                <li class="active">Notes</li>
            </ol>
        </div>
        
        <div class="content container-fluid">
            <div class="col-xs-12">
                <!-- Your Page Content -->
                <div class="box box-solid box-primary">
                    <div class="box-header with-border">
                        <div class="box-title">List of Notes</div>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <th class="col-sm-1">ID</th>
                                    <th class="col-sm-7">Note</th>
                                    <th class="col-sm-1">Uploader ID</th>
                                    <th class="col-sm-3 text-right">Actions</th>
                                </tr>
                                <tr v-for="note in notes" v-bind:key="note.id">
                                    <th class="col-sm-1">{{ note.id }}</th>
                                    <th class="col-sm-7">{{ note.note }}</th>
                                    <th class="col-sm-1">{{ note.user_id }}</th>
                                    <th class="row col-sm-3 text-right">
                                        <button class="btn btn-warning" @click="openUpdate(note.id)"
                                        data-toggle="modal" data-target="#modal-update">Update</button>
                                        <button class="btn btn-danger" @click="deleteNote(note.id)">Delete</button>
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
                            <a @click="fetchAllNotes(pagination.prev_page_url)">Previous</a>
                        </li>
                        <li class="page-item disabled">
                            <a class="page-link text-dark">Page {{ pagination.current_page }} of {{pagination.last_page}}</a>
                        </li>
                        <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="paginate_button next">
                            <a @click="fetchAllNotes(pagination.next_page_url)">Next</a>
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
                        <slot name="header">Submit new note</slot>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                    </div>
                    <form @submit.prevent="createNewNote" role="form">
                        <div class="box-body">
                            <div class="form-group">
                                <label>Note
                                    <input type="text" class="form-control"
                                    placeholder="New note" v-model="note.note">
                                </label>
                            </div>
                        </div>
                    </form>    
                    <div class="modal-footer text-right">
                        <slot name="footer">
                            <button type="submit" class="btn btn-primary"
                            @click="createNewNote()" data-dismiss="modal">Create</button>
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
                        <slot name="header">Update note</slot>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                    </div>
                    <form @submit.prevent="updateNote(note.id)" role="form">
                        <div class="box-body">
                            <div class="form-group">
                                <label>Note
                                    <input type="text" class="form-control"
                                    placeholder="New note" v-model="note.note">
                                </label>
                            </div>
                        </div>
                    </form>    
                    <div class="modal-footer text-right">
                        <slot name="footer">
                            <button type="submit" class="btn btn-primary"
                            @click="updateNote(note.id)" data-dismiss="modal">Update</button>
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
            notes: [],
            note: {
                id: '',
                note: '',
                project: '',
                uploader: ''
            },
            note_id: '',
            pagination: {}
        }
    },
    methods: {
        fetchAllNotes: function(page_url) {
            let url = page_url || 'http://localhost:8000/api/notes';
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
                    this.notes = res.data.data;
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
        createNewNote: function() {
            let user = JSON.parse(localStorage.getItem('inovmonev.user'));
            let project = this.$route.params.id;
            let url = 'http://localhost:8000/api/notes';
            let data = {
                action: 'create',
                note: this.note.note,
                project: project,
                uploader: user.id
            }
            
            let options = {
                method: 'post',
                data,
                url
            }

            axios(options)
                .then(res => {
                    console.log(res);
                    this.fetchAllNotes();
                })
        },
        deleteNote: function(id) {
            let url = 'http://localhost:8000/api/notes';
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
                    this.fetchAllNotes();
                })
        },
        openUpdate: function(id) {
            this.note.id = id;
        },
        updateNote: function(id) {
            let user = JSON.parse(localStorage.getItem('inovmonev.user'));
            let project = this.$route.params.id;
            let url = 'http://localhost:8000/api/notes';
            let data = {
                action: 'update',
                note: this.note.note,
                project: project,
                uploader: user.id
            }
            
            let options = {
                method: 'post',
                data,
                url
            }

            axios(options)
                .then(res => {
                    console.log(res);
                    this.fetchAllNotes();
                })
        }
    },
    created: function() {
        this.fetchAllNotes();
    }
}
</script>