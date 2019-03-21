<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    public function roleDashboard()
    {
        return "this is Role management page";
    }

    public function roleManagementHandler(Request $request)
    {

//         TODO: enable this after able to create user
//        $auth = Auth::user();
//        $this->redirectIfNotLoggedIn($auth);

        $data = $request->all();
        $data = array_map('trim', $data);

        $action = isset($data['action']) ? $data['action'] : null;
        if ($action == "create") {
            $this->createNewRole($data);
        } else if ($action == "read") {
            $this->readRole($data);
        } else if ($action == "update") {
            $this->updateRole($data);
        } else if ($action == "delete") {
            $this->deleteRole($data);
        } else if ($action == "fetchAll") {
            $this->fetchAllRole();
        } else if ($action == null) {
            return abort(404);
        }

//         TODO: change this to proper page
//         return view('roleManagement');
        return "this is role management page";
    }

    private function createNewRole($data)
    {

        try {
            $newRole = Role::create([
                'name' => $data['name'],
                'description' => $data['description']
            ]);
        } catch (\Exception $e) {
            $newRole = null;
        }

        if (!$newRole) {
            $this->displayCreateRoleFailed();
        } else {
            $this->displayCreateRoleSucceeded();
        }

        return $newRole;
    }

    public function addPermission($data)
    {
        try {
            $role = Role::find($data['role_id']);
            $permission = Permission::find($data['permission_id']);

            if (!$role || !$permission) {
                $this->displayAddPermissionFailed();
            }

            $role->permissions()->attach($permission);
            $this->displayAddPermissionSucceeded();

        } catch (\Exception $e) {
            $this->displayAddPermissionFailed();
        }
    }

    private function displayCreateRoleFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> Role cannot be created!
        </div>';
    }

    private function displayCreateRoleSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> New role has been created!
        </div>';
    }

    private function readRole($data)
    {
        try {
            $permission = Role::findOrNew($data['id']);
            return $permission;

        } catch (\Exception $e) {
            $this->displayReadRoleFailed();
            return new Role();
        }
    }

    private function displayReadRoleFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> Role not found!
        </div>';
    }

    private function updateRole($data)
    {
        try {

            $permission = Role::find($data['id']);
            $permission['name'] = $data['name'];
            $permission['description'] = $data['description'];
            $permission->save();

        } catch (\Exception $e) {
            $permission = null;
        }

        if (!$permission) {
            $this->displayUpdateRoleFailed();
        } else {
            $this->displayUpdateRoleSucceeded();
        }

    }

    private function displayUpdateRoleFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> Role cannot be updated!
        </div>';
    }

    private function displayUpdateRoleSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> Role has been edited!
        </div>';
    }

    private function deleteRole(array $data)
    {
        try {

            $permission = Role::find($data['id']);
            $permission->delete();

        } catch (\Exception $e) {
            $permission = null;
        }

        if (!$permission) {
            $this->displayDeleteRoleFailed();
        } else {
            $this->displayDeleteRoleSucceeded();
        }
    }

    private function displayDeleteRoleFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> Role cannot be deleted!
        </div>';
    }

    private function displayDeleteRoleSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> Role has been deleted!
        </div>';
    }

    private function fetchAllRole()
    {

        try {
            return Role::all();
        } catch (\Exception $e) {
            $this->displayReadRoleFailed();
            return [];
        }
    }

    private function displayAddPermissionFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> Permission cannot be granted!
        </div>';
    }

    private function displayAddPermissionSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> New permission has been granted!
        </div>';
    }


}
