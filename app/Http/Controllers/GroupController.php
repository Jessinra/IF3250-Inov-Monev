<?php

namespace App\Http\Controllers;

use App\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function groupManagementHandler(Request $request)
    {

//         TODO: enable this after able to create user
//        $auth = Auth::user();
//        $this->redirectIfNotLoggedIn($auth);

        $data = $request->all();
        $action = $data['action'];

        if ($action == "create") {
            $this->createNewGroup($data);
        } else if ($action == "read") {
            $this->readGroup($data);
        } else if ($action == "update") {
            $this->updateGroup($data);
        } else if ($action == "delete") {
            $this->deleteGroup($data);
        } else if ($action == "fetchAll") {
            $this->fetchAllGroup();
        }

//         TODO: change this to proper page
//         return view('GroupManagement');
        return "this is Group management page";
    }

    private function createNewGroup($data)
    {

        try {
            $newGroup = Group::create([
                'name' => $data['name'],
                'description' => $data['description']
            ]);
        } catch (\Exception $e) {
            $newGroup = null;
        }

        if (!$newGroup) {
            $this->displayCreateGroupFailed();
        } else {
            $this->displayCreateGroupSucceeded();
        }
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
        try {
            $group = Group::findOrNew($data['id']);
            return $group;

        } catch (\Exception $e) {
            $this->displayReadGroupFailed();
            return new Group();
        }
    }

    private function displayReadGroupFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> Group not found!
        </div>';
    }

    private function updateGroup($data)
    {
        try {

            $group = Group::find($data['id']);
            $group['name'] = $data['name'];
            $group['description'] = $data['description'];
            $group->save();

        } catch (\Exception $e) {
            $group = null;
        }

        if (!$group) {
            $this->displayUpdateGroupFailed();
        } else {
            $this->displayUpdateGroupSucceeded();
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
        try {

            $group = Group::find($data['id']);
            $group->delete();

        } catch (\Exception $e) {
            $group = null;
        }

        if (!$group) {
            $this->displayDeleteGroupFailed();
        } else {
            $this->displayDeleteGroupSucceeded();
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
