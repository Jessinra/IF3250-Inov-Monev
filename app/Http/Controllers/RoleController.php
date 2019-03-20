<?php

namespace App\Http\Controllers;

use App\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    public function roleManagementHandler(Request $request)
    {

//         TODO: enable this after able to create user
//        $auth = Auth::user();
//        $this->redirectIfNotLoggedIn($auth);

        $data = $request->all();
        $action = $data['action'];

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
            $role = Role::findOrNew($data['id']);
            return $role;

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

            $role = Role::find($data['id']);
            $role['name'] = $data['name'];
            $role['description'] = $data['description'];
            $role->save();

        } catch (\Exception $e) {
            $role = null;
        }

        if (!$role) {
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

            $role = Role::find($data['id']);
            $role->delete();

        } catch (\Exception $e) {
            $role = null;
        }

        if (!$role) {
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


}
