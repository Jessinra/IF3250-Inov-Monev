<?php

namespace App\Http\Controllers;

use App\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{

    public function groupDashboard()
    {
        return "this is Group management page";
    }

    public function groupManagementHandler(Request $request)
    {

//         TODO: enable this after able to create user
//        $auth = Auth::user();
//        $this->redirectIfNotLoggedIn($auth);

        $data = $request->all();
        $data = array_map('trim', $data);

        $action = isset($data['action']) ? $data['action'] : null;
        if ($action == "create") {
            $this->createNewGroup($data);

        } else if ($action == "read") {

//          TODO: change this to display correct view
            return $this->readGroup($data);

        } else if ($action == "update") {
            $this->updateGroup($data);

        } else if ($action == "delete") {
            $this->deleteGroup($data);

        } else if ($action == "fetchAll") {

//          TODO: change this to display correct view
            return $this->fetchAllGroup();

        } else if ($action == null) {

//          TODO : return using abort(404);
            return "abort 404";
        }

//      TODO: change this to proper page
//      return view('GroupManagement');
        return "this is Group management page";

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
            'name' => ['required', 'string', 'max:255', 'unique:groups'],
            'description' => ['string', 'max:255']
        ]);

        return !($validator->fails());
    }

    private function createNewGroup($data)
    {

        $data = $this->parseCreateData($data);

        if ($this->isCreateDataValid($data)) {
            $newGroup = Group::create($data);
            $this->displayCreateGroupSucceeded();
        } else {
            $newGroup = null;
            $this->displayCreateGroupFailed();
        }

        return $newGroup;
    }

    private function displayCreateGroupFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> Group cannot be created!
        </div>';
    }

    private function displayCreateGroupSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> New Group has been created!
        </div>';
    }

    private function readGroup($data)
    {
        return Group::findOrNew(
            isset($data['id']) ? $data['id'] : null
        );
    }

    private function displayReadGroupFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> Group not found!
        </div>';
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
            'name' => ['required', 'string', 'max:255', 'unique:groups'],
            'description' => ['string', 'max:255']
        ]);

        return !($validator->fails());
    }

    private function updateGroup($data)
    {
        $data = $this->parseUpdateData($data);

        if ($this->isUpdateDataValid($data)) {

            $group = Group::find($data['id']);
            $group['name'] = $data['name'];
            $group['description'] = $data['description'];
            $group->save();

            $this->displayUpdateGroupSucceeded();

        } else {
            $this->displayUpdateGroupFailed();
        }
    }

    private function displayUpdateGroupFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> Group cannot be updated!
        </div>';
    }

    private function displayUpdateGroupSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> Group has been edited!
        </div>';
    }

    private function deleteGroup(array $data)
    {
        $group = Group::find(
            isset($data['id']) ? $data['id'] : null
        );

        if ($group) {
            $group->delete();
            $this->displayDeleteGroupSucceeded();

        } else {
            $this->displayDeleteGroupFailed();
        }
    }

    private function displayDeleteGroupFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> Group cannot be deleted!
        </div>';
    }

    private function displayDeleteGroupSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> Group has been deleted!
        </div>';
    }

    private function fetchAllGroup()
    {

        try {
            return Group::all();
        } catch (\Exception $e) {
            $this->displayReadGroupFailed();
            return [];
        }
    }
}
