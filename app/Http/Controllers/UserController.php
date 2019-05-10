<?php

namespace App\Http\Controllers;

use App\Group;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Resources\User as UserResource;

class UserController extends Controller
{

    public function login(Request $request)
    {
        $status = 401;
        $response = ['error' => 'Unauthorised'];

        if (Auth::attempt($request->only(['email', 'password']))) {
            $status = 200;
            $response = [
                'user' => Auth::user(),
                'token' => Auth::user()->createToken('inovmonev')->accessToken,
            ];
        }

        return response()->json($response, $status);
    }

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
        // $data = array_map($data);

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

    //LAGI DISINI
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

            'groups_added' => isset($data['groups_added']) ? $data['groups_added'] : null,

            'roles_added' => isset($data['roles_added']) ? $data['roles_added'] : null,
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

        foreach((array)$data['roles_added'] as $role_added){
            $user->addRole($role_added);
        }
        // $user->update($data)

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
        foreach((array)$data['groups_added'] as $group_added){
            $user->addGroup($group_added);
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

        if (!($this->isUpdateDataValid($data))) {
            return null;
        }

        $id = $data['id'];
        $updatedUser = User::find($id);
        if (!$updatedUser) {
            return User::create($data);
        } else {
            if ($data['groups_added']){
                foreach((array)$data['groups_added'] as $group_added){
                    $updatedUser->addGroup($group_added);
                }
            }
            if ($data['groups_removed']){
                foreach((array) $data['groups_removed'] as $group_removed){
                    $updatedUser->removeGroup($group_removed);
                }
            }
            if ($data['roles_added']){
                foreach((array) $data['roles_added'] as $role_added) {
                    $updatedUser->addRole($role_added);
                }
            }
            if ($data['roles_removed']){
                foreach((array) $data['roles_removed'] as $role_removed){
                    $updatedUser->removeRole($role_removed);
                }
            }
            $updatedUser->update($data);
        }

        return $updatedUser;
    }

    private function parseUpdateData($data)
    {
        return [
            'id' => isset($data['id']) ? $data['id'] : null,
            'name' => isset($data['name']) ? $data['name'] : null,
            'username' => isset($data['username']) ? $data['username'] : null,
            'email' => isset($data['email']) ? $data['email'] : null,
            'password' => isset($data['password']) ? Hash::make($data['password']) : null,

            // Copy of password un-hashed for validation
            'c_pass' => isset($data['password']) ? $data['password'] : null,
            'c_pass_confirmation' => isset($data['password_confirmation']) ? $data['password_confirmation'] : null,

            'groups_added' => isset($data['groups_added']) ? $data['groups_added'] : null,
            'groups_removed' => isset($data['groups_removed']) ? $data['groups_removed'] : null,
            'roles_added' => isset($data['roles_added']) ? $data['roles_added'] : null,
            'roles_removed' => isset($data['roles_removed']) ? $data['roles_removed'] : null,

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
                'id' => ['required', 'int'],
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user)],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
                'password' => ['required', 'string', 'max:255'],

                'c_pass' => ['required', 'string', 'min:6', 'confirmed'],
            ]);
        } else {

            $validator = Validator::make($data, [
                'id' => ['required', 'int'],
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
        $user_data = User::all();

        foreach ($user_data as $user) {
            foreach ($user->roles as $role) {
                // Query permissions for role
                // DONT DELETE
            }
            foreach ($user->groups as $group){

            }
        }

        try {
            return json_encode($user_data);
        } catch (\Exception $e) {
            return [];
        }
        // $users_per_page = 15;
        
        // // Get permissions
        // $users = User::paginate($users_per_page);

        // // return as a resource
        // return UserResource::collection($users);

        // try {
        //     return User::all();
        // } catch (\Exception $e) {
        //     return [];
        // }
    }
}