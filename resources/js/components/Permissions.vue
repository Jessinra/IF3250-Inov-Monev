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
        
        <div class="content"> 
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
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                    </tr>
                                    <tr v-for="permission in permissions" v-bind:key="permission.id">
                                        <th>{{ permission.id }}</th>
                                        <th>{{ permission.name }}</th>
                                        <th>{{ permission.description }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="dataTables_paginate paging_simple_numbers">
                        <ul class="pagination">
                            <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="paginate_button previous">
                                <a @click="fetchAllPermissions(pagination.prev_page_url)">Previous</a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link text-dark">Page {{ pagination.current_page }} of {{pagination.last_page}}</a>
                            <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="paginate_button next">
                                <a @click="fetchAllPermissions(pagination.next_page_url)">Next</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div> <!-- /.content -->
        </div>
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
            pagination: {}
        }
    },

    methods: {
        fetchAllPermissions: function(page_url) {
            let url = page_url || 'http://localhost:8000/permissions';
            let send = {
                action: "fetchAll" 
            };
            let vm = this;

            axios.post(url, send)
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
        }
    },
    
    created: function() {
        this.fetchAllPermissions();
    }
}
</script>