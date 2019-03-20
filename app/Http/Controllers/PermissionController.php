<?php

namespace App\Http\Controllers;

use App\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function permissionManagementHandler(Request $request)
    {

//         TODO: enable this after able to create user
//        $auth = Auth::user();
//        $this->redirectIfNotLoggedIn($auth);

        $data = $request->all();
        $action = $data['action'];

        if ($action == "create") {
            $this->createNewPermission($data);
        } else if ($action == "read") {
            $this->readPermission($data);
        } else if ($action == "update") {
            $this->updatePermission($data);
        } else if ($action == "delete") {
            $this->deletePermission($data);
        } else if ($action == "fetchAll") {
            $this->fetchAllPermission();
        }

//         TODO: change this to proper page
//         return view('PermissionManagement');
        return "this is Permission management page";
    }

    private function createNewPermission($data)
    {

        try {
            $newPermission = Permission::create([
                'name' => $data['name'],
                'description' => $data['description']
            ]);
        } catch (\Exception $e) {
            $newPermission = null;
        }

        if (!$newPermission) {
            $this->displayCreatePermissionFailed();
        } else {
            $this->displayCreatePermissionSucceeded();
        }
    }

    private function displayCreatePermissionFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> Permission cannot be created!
        </div>';
    }

    private function displayCreatePermissionSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> New Permission has been created!
        </div>';
    }

    private function readPermission($data)
    {
        try {
            $permission = Permission::findOrNew($data['id']);
            return $permission;

        } catch (\Exception $e) {
            $this->displayReadPermissionFailed();
            return new Permission();
        }
    }

    private function displayReadPermissionFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> Permission not found!
        </div>';
    }

    private function updatePermission($data)
    {
        try {

            $permission = Permission::find($data['id']);
            $permission['name'] = $data['name'];
            $permission['description'] = $data['description'];
            $permission->save();

        } catch (\Exception $e) {
            $permission = null;
        }

        if (!$permission) {
            $this->displayUpdatePermissionFailed();
        } else {
            $this->displayUpdatePermissionSucceeded();
        }

    }

    private function displayUpdatePermissionFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> Permission cannot be updated!
        </div>';
    }

    private function displayUpdatePermissionSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> Permission has been edited!
        </div>';
    }

    private function deletePermission(array $data)
    {
        try {

            $permission = Permission::find($data['id']);
            $permission->delete();

        } catch (\Exception $e) {
            $permission = null;
        }

        if (!$permission) {
            $this->displayDeletePermissionFailed();
        } else {
            $this->displayDeletePermissionSucceeded();
        }
    }

    private function displayDeletePermissionFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> Permission cannot be deleted!
        </div>';
    }

    private function displayDeletePermissionSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> Permission has been deleted!
        </div>';
    }

    private function fetchAllPermission()
    {

        try {
            return Permission::all();
        } catch (\Exception $e) {
            $this->displayReadPermissionFailed();
            return [];
        }
    }
}
