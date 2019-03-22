<?php

namespace App\Http\Controllers;

use App\Group;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function userDashboard()
    {
        return "this is User management page";
    }

    public function userManagementHandler(Request $request)
    {

//         TODO: enable this after able to create user
//        $auth = Auth::user();
//        $this->redirectIfNotLoggedIn($auth);

        $data = $request->all();
        $data = array_map('trim', $data);

        $action = isset($data['action']) ? $data['action'] : null;
        if ($action == "create") {
            $this->createNewUserHandler($data);

        } else if ($action == "read") {

//          TODO: change this to display correct view
            return $this->readUser($data);

        } else if ($action == "update") {
            $this->updateUserHandler($data);

        } else if ($action == "delete") {
            $this->deleteUser($data);

        } else if ($action == "fetchAll") {

//          TODO: change this to display correct view
            return $this->fetchAllUser();

        } else if ($action == null) {

//          TODO : return using abort(404);
            return "abort 404";
        }

//         TODO: change this to proper page
//         return view('UserManagement');
        return "this is User management page";
    }


    private function createNewUserHandler($data)
    {

        $newUser = $this->createNewUser($data);
        $newUser = $this->addRoleToUser($newUser, $data);
        $newUser = $this->addGroupToUser($newUser, $data);

        if (!$newUser) {
            $this->displayCreateUserFailed();
        } else {
            $this->displayCreateUserSucceeded();
        }
    }

    private function createNewUser($data)
    {
        $data = $this->parseCreateData($data);

        if (!($this->isCreateDataValid($data))) {
            return null;
        }

        return User::create($data);
    }

    private function parseCreateData($data)
    {
        return [
            'name' => isset($data['name']) ? $data['name'] : null,
            'username' => isset($data['username']) ? $data['username'] : null,
            'email' => isset($data['email']) ? $data['email'] : null,
            'password' => isset($data['password']) ? Hash::make($data['password']) : null,

            // Copy of password un-hashed for validation
            'c_pass' => isset($data['password']) ? $data['password'] : null,
            'c_pass_confirmation' => isset($data['password_confirmation']) ? $data['password_confirmation'] : null,

            'roleId' => isset($data['roleId']) ? $data['roleId'] : null,
            'groupId' => isset($data['groupId']) ? $data['groupId'] : null,
        ];
    }

    private function isCreateDataValid($data)
    {

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'max:255'],

            'c_pass' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        return !($validator->fails());
    }

    private function addRoleToUser($user, $data)
    {
        $roleId = $this->parseRoleId($data);
        if ($this->isRoleIdValid($roleId) && $user) {
            $user->addRole($roleId);
        }
        return $user;
    }

    private function parseRoleId($data)
    {
        return isset($data['roleId']) ? $data['roleId'] : null;
    }

    private function isRoleIdValid($roleId)
    {
        return Role::find($roleId);
    }

    private function addGroupToUser($user, $data)
    {
        $groupId = $this->parseGroupId($data);
        if ($this->isGroupIdValid($groupId) && $user) {
            $user->addGroup($groupId);
        }

        return $user;
    }

    private function parseGroupId($data)
    {
        return isset($data['groupId']) ? $data['groupId'] : null;
    }

    private function isGroupIdValid($groupId)
    {
        return Group::find($groupId);
    }

    private function displayCreateUserFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> User cannot be created!
        </div>';
    }

    private function displayCreateUserSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> New User has been created!
        </div>';
    }

    private function readUser($data)
    {
        return User::findOrNew(
            isset($data['id']) ? $data['id'] : null
        );
    }

    private function updateUserHandler($data)
    {
        $data = $this->parseUpdateData($data);
        $updatedUser = $this->updateUser($data);

        if ($data['addRole']) {
            $updatedUser = $this->addRoleToUser($updatedUser, $data);
        } else if ($data['removeRole']) {
            $updatedUser = $this->removeRoleFromUser($updatedUser, $data);
        } else if ($data['addGroup']) {
            $updatedUser = $this->addGroupToUser($updatedUser, $data);
        } else if ($data['removeGroup']) {
            $updatedUser = $this->removeGroupFromUser($updatedUser, $data);
        }

        if (!$updatedUser) {
            $this->displayUpdateUserFailed();
        } else {
            $this->displayUpdateUserSucceeded();
        }
    }

    private function parseUpdateData($data)
    {
        return [
            'id' => isset($data['id']) ? $data['id'] : null,
            'name' => isset($data['name']) ? $data['name'] : null,
            'username' => isset($data['username']) ? $data['username'] : null,
            'email' => isset($data['email']) ? $data['email'] : null,
            'password' => isset($data['password']) ? Hash::make($data['password']) : null,

            'addRole' => isset($data['addRole']) ? ($data['addRole'] === 'true') : false,
            'removeRole' => isset($data['removeRole']) ? ($data['removeRole'] === 'true') : false,
            'addGroup' => isset($data['addGroup']) ? ($data['addGroup'] === 'true') : false,
            'removeGroup' => isset($data['removeGroup']) ? ($data['removeGroup'] === 'true') : false,

            'roleId' => isset($data['roleId']) ? $data['roleId'] : null,
            'groupId' => isset($data['groupId']) ? $data['groupId'] : null,

            // Copy of password un-hashed for validation
            'c_pass' => isset($data['password']) ? $data['password'] : null,
            'c_pass_confirmation' => isset($data['password_confirmation']) ? $data['password_confirmation'] : null,
        ];
    }

    private function updateUser($data)
    {
        if (!($this->isUpdateDataValid($data))) {
            return null;
        }

        $user = User::find($data['id']);
        if (!$user) {
            return null;
        }

        $user['name'] = $data['name'];
        $user['username'] = $data['username'];
        $user['email'] = $data['email'];
        $user['password'] = $data['password'];
        $user->save();

        return $user;
    }

    private function isUpdateDataValid($data)
    {

        if ($data['id']) {

            $user = User::find($data["id"]);
            $validator = Validator::make($data, [
                'id' => ['required', 'string'],
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user)],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
                'password' => ['required', 'string', 'max:255'],

                'c_pass' => ['required', 'string', 'min:6', 'confirmed'],
            ]);
        } else {

            $validator = Validator::make($data, [
                'id' => ['required', 'string'],
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'max:255'],

                'c_pass' => ['required', 'string', 'min:6', 'confirmed'],
            ]);
        }

        return !($validator->fails());
    }

    private function removeRoleFromUser($user, $data)
    {
        if (!$user) {
            return null;
        }

        $roleId = $this->parseRoleId($data);
        $user->removeRole($roleId);

        return $user;
    }

    private function removeGroupFromUser($user, $data)
    {
        if (!$user) {
            return null;
        }

        $groupId = $this->parseGroupId($data);
        $user->removeGroup($groupId);

        return $user;
    }

    private function displayUpdateUserFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> User cannot be updated!
        </div>';
    }

    private function displayUpdateUserSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> User data has been updated!
        </div>';
    }

    private function deleteUser(array $data)
    {
        $user = User::find(
            isset($data['id']) ? $data['id'] : null
        );

        if ($user) {
            $user->delete();
            $this->displayDeleteUserSucceeded();

        } else {
            $this->displayDeleteUserFailed();
        }
    }

    private function displayDeleteUserSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> User has been deleted!
        </div>';
    }

    private function displayDeleteUserFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> User cannot be deleted!
        </div>';
    }

    private function fetchAllUser()
    {

        try {
            return User::all();
        } catch (\Exception $e) {
            return [];
        }
    }
}



