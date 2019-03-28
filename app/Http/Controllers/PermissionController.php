<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Http\Resources\Permission as PermissionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class PermissionController extends Controller
{
    public function permissions()
    {
        return view('pages.permissions');
    }

    public function permissionManagementHandler(Request $request)
    {

//         TODO: enable this after able to create user
//        $auth = Auth::user();
//        $this->redirectIfNotLoggedIn($auth);

        $data = $request->all();
        $data = array_map('trim', $data);

        $action = isset($data['action']) ? $data['action'] : null;
        if ($action == "create") {
            $this->createNewPermission($data);

        } else if ($action == "read") {

//          TODO: change this to display correct view
            return $this->readPermission($data);

        } else if ($action == "update") {
            $this->updatePermission($data);

        } else if ($action == "delete") {
            $this->deletePermission($data);

        } else if ($action == "fetchAll") {

//          TODO: change this to display correct view
            return $this->fetchAllPermissions();
            
        } else if ($action == null) {

//          TODO : return using abort(404);
            return "abort 404";
        }

//      TODO: change this to proper page
//      return view('PermissionManagement');
        return "this is Permission management page";

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
            'name' => ['required', 'string', 'max:255', 'unique:permissions'],
            'description' => ['string', 'max:255']
        ]);

        return !($validator->fails());
    }

    private function createNewPermission($data)
    {
        $data = $this->parseCreateData($data);

        if (!($this->isCreateDataValid($data))) {
            $this->displayCreatePermissionFailed();
            return null;
        }

        $newPermission = Permission::create($data);
        $this->displayCreatePermissionSucceeded();

        return $newPermission;
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
        return Permission::findOrNew(
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
            'name' => ['required', 'string', 'max:255', 'unique:permissions'],
            'description' => ['string', 'max:255']
        ]);

        return !($validator->fails());
    }
    
    private function updatePermission($data)
    {
        $data = $this->parseUpdateData($data);

        if (!($this->isUpdateDataValid($data))) {
            $this->displayUpdatePermissionFailed();
            return null;
        }

        $permission = Permission::find($data['id']);
        $permission['name'] = $data['name'];
        $permission['description'] = $data['description'];
        $permission->save();

        $this->displayUpdatePermissionSucceeded();
        return $permission;
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
        $permission = Permission::find(
            isset($data['id']) ? $data['id'] : null
        );

        if ($permission) {
            $permission->delete();
            $this->displayDeletePermissionSucceeded();

        } else {
            $this->displayDeletePermissionFailed();
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

    private function fetchAllPermissions()
    {
        $permissions_per_page = 15;
        
        // Get permissions
        $permissions = Permission::paginate($permissions_per_page);

        // return as a resource
        return PermissionResource::collection($permissions);
    }
}
