<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            $this->createNewRoleHandler($data);

        } else if ($action == "read") {

//          TODO: change this to display correct view
            return $this->readRole($data);

        } else if ($action == "update") {
            $this->updateRoleHandler($data);

        } else if ($action == "delete") {
            $this->deleteRole($data);

        } else if ($action == "fetchAll") {

//          TODO: change this to display correct view
            return $this->fetchAllRole();

        } else if ($action == null) {

//          TODO : return using abort(404);
            return "abort 404";
        }


//         TODO: change this to proper page
//         return view('roleManagement');
        return "this is Role management page";
    }

    private function parseCreateData($data)
    {
        return [
            'name' => isset($data['name']) ? $data['name'] : null,
            'description' => isset($data['description']) ? $data['description'] : null,
        ];
    }

    private function isCreateDataValid($data)
    {

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'description' => ['string', 'max:255']
        ]);

        return !($validator->fails());
    }

    private function createNewRoleHandler($data)
    {

        $newRole = $this->createNewRole($data);
        $newRole = $this->addPermissionsToRole($newRole, $data);

        if (!$newRole) {
            $this->displayCreateRoleFailed();
        } else {
            $this->displayCreateRoleSucceeded();
        }
    }

    private function createNewRole($data)
    {
        $data = $this->parseCreateData($data);

        if (!($this->isCreateDataValid($data))) {
            return null;
        }

        return Role::create($data);
    }


    private function parsePermissionData($data)
    {

        $count = isset($data['permissionCount']) ? $data['permissionCount'] : 0;

        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $permissionId = isset($data['permission' . $i]) ? $data['permission' . $i] : null;
            array_push($result, $permissionId);
        }

        return array_filter($result);
    }

    private function filterNonExistPermission($permissions)
    {

        $result = [];
        foreach ($permissions as $id) {
            $permission = Permission::find($id);
            if ($permission) {
                array_push($result, $permission['id']);
            }
        }
        return $result;
    }

    private function addPermissionsToRole($role, $data)
    {

        if (!$role) {
            return null;
        }

        $permissions = $this->parsePermissionData($data);
        $permissions = $this->filterNonExistPermission($permissions);

        $role->resetPermission();
        $role->addPermissions($permissions);

        return $role;
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
        return Role::findOrNew(
            isset($data['id']) ? $data['id'] : null
        );
    }

    private function parseUpdateData($data)
    {
        return [
            'id' => isset($data['id']) ? $data['id'] : null,
            'name' => isset($data['name']) ? $data['name'] : null,
            'description' => isset($data['description']) ? $data['description'] : null,
        ];
    }

    private function isUpdateDataValid($data)
    {

        $validator = Validator::make($data, [
            'id' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'description' => ['string', 'max:255']
        ]);

        return !($validator->fails());
    }

    private function updateRoleHandler($data)
    {

        $updatedRole = $this->updateRole($data);
        $updatedRole = $this->addPermissionsToRole($updatedRole, $data);

        if (!$updatedRole) {
            $this->displayUpdateRoleFailed();
        } else {
            $this->displayUpdateRoleSucceeded();
        }
    }

    private function updateRole($data)
    {
        $data = $this->parseUpdateData($data);

        if (!($this->isUpdateDataValid($data))) {
            return null;
        }

        $role = Role::find($data['id']);
        $role['name'] = $data['name'];
        $role['description'] = $data['description'];
        $role->save();

        return $role;
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
        $role = Role::find(
            isset($data['id']) ? $data['id'] : null
        );

        if ($role) {
            $role->delete();
            $this->displayDeleteRoleSucceeded();

        } else {
            $this->displayDeleteRoleFailed();
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
        $role_data = Role::all();

        foreach ($role_data as $role) {
            foreach ($role->permissions as $permission) {
                // Query permissions for role
                // DONT DELETE
            }
        }

        try {
            return json_encode($role_data);
        } catch (\Exception $e) {
            return [];
        }
    }
}
