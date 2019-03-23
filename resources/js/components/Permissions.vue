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
                <!-- Your Page Content -->
                <div class="box">Permissions</div>
            </div> <!-- /.content -->
        </div>
    </div> <!-- /.content-wrapper -->
</template>

<script>
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
        }
    }
</script>