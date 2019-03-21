<?php

namespace App\Http\Controllers;

use App\Group;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function userManagementHandler(Request $request)
    {

//         TODO: enable this after able to create user
//        $auth = Auth::user();
//        $this->redirectIfNotLoggedIn($auth);

        $data = $request->all();
        $action = $data['action'];

        if ($action == "create") {
            $this->createNewUser($data);
        } else if ($action == "read") {
            $this->readUser($data);
        } else if ($action == "update") {
            $this->updateUser($data);
        } else if ($action == "delete") {
            $this->deleteUser($data);
        } else if ($action == "fetchAll") {
            $this->fetchAllUser();
        } else if ($action == "addRole") {
            $this->addRole($data);
        } else if ($action == "addGroup") {
            $this->addGroup($data);
        }

//         TODO: change this to proper page
//         return view('UserManagement');
        return "this is User management page";
    }

    private function createNewUser($data)
    {

        try {
            if (!($this->isDataValid($data))) {
                throw new \Exception();
            }

            $newUser = User::create([
                'name' => trim($data['name']),
                'username' => trim($data['username']),
                'email' => trim($data['email']),
                'password' => Hash::make(trim($data['password'])),
            ]);

            $this->addRoleOnCreate($newUser, trim($data['role_id']));
            $this->addGroupOnCreate($newUser, trim($data['group_id']));

        } catch (\Exception $e) {
            $newUser = null;
        }

        if (!$newUser) {
            $this->displayCreateUserFailed();
        } else {
            $this->displayCreateUserSucceeded();
        }
    }

    public function addRole($data)
    {
        try {
            $user = User::find($data['user_id']);
            $role = Role::find($data['role_id']);

            if (!$user || !$role) {
                $this->displayAddRoleFailed();
            }

            $user->roles()->attach($role);
            $this->displayAddRoleSucceeded();

        } catch (\Exception $e) {
            $this->displayAddRoleFailed();
        }
    }

    private function addRoleOnCreate($user, $roleId)
    {
        try {
            $role = Role::find($roleId);

            if (!$user || !$role) {
                $this->displayAddRoleFailed();
            }

            $user->roles()->attach($role);
            $this->displayAddRoleSucceeded();

        } catch (\Exception $e) {
            $this->displayAddRoleFailed();
        }
    }

    public function addGroup($data)
    {
        try {
            $user = User::find($data['user_id']);
            $group = Group::find($data['group_id']);

            if (!$user || !$group) {
                $this->displayAddGroupFailed();
            }

            $user->groups()->attach($group);
            $this->displayAddGroupSucceeded();

        } catch (\Exception $e) {
            $this->displayAddGroupFailed();
        }
    }


    private function addGroupOnCreate($user, string $groupId)
    {
        try {
            $group = Group::find($groupId);

            if (!$user || !$group) {
                $this->displayAddGroupFailed();
            }

            $user->groups()->attach($group);
            $this->displayAddGroupSucceeded();

        } catch (\Exception $e) {
            $this->displayAddGroupFailed();
        }
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
        try {
            $user = User::findOrNew($data['id']);
            return $user;

        } catch (\Exception $e) {
            $this->displayReadUserFailed();
            return new User();
        }
    }

    private function displayReadUserFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> User not found!
        </div>';
    }

    private function updateUser($data)
    {
        try {

            if (!($this->isDataValid($data))) {
                throw new \Exception();
            }

            $user = User::find($data['id']);
            $user['name'] = $data['name'];
            $user['username'] = $data['username'];
            $user['email'] = $data['email'];
            $user['password'] = Hash::make(trim($data['password']));

            $user->save();

        } catch (\Exception $e) {
            $user = null;
        }

        if (!$user) {
            $this->displayUpdateUserFailed();
        } else {
            $this->displayUpdateUserSucceeded();
        }
    }

    private function isDataValid($data)
    {
        $validator = $this->getValidator($data);
        if ($validator->fails()) {
            $this->displayValidationErrors($validator->errors()->all());
            return false;
        }

        return true;
    }

    private function getValidator(array $data)
    {

        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
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
        try {

            $user = User::find($data['id']);
            $user->delete();

        } catch (\Exception $e) {
            $user = null;
        }

        if (!$user) {
            $this->displayDeleteUserFailed();
        } else {
            $this->displayDeleteUserSucceeded();
        }
    }

    private function displayDeleteUserFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> User cannot be deleted!
        </div>';
    }

    private function displayDeleteUserSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> User has been deleted!
        </div>';
    }

    private function fetchAllUser()
    {

        try {
            return User::all();
        } catch (\Exception $e) {
            $this->displayReadUserFailed();
            return [];
        }
    }

    private function displayAddGroupFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> User cannot join the group!
        </div>';
    }

    private function displayAddRoleFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> Role cannot be given!
        </div>';
    }

    private function displayAddRoleSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> Role has been given to user!
        </div>';
    }

    private function displayAddGroupSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> User joined the group!
        </div>';
    }

    private function displayValidationErrors($errors)
    {
        foreach ($errors as $error) {
            echo '<div class="alert alert-warning alert-dismissible fade show text-center">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            ' . $error . '</div>';
        }
    }


}
