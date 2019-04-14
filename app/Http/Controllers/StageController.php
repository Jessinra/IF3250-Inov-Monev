<?php

namespace App\Http\Controllers;

use App\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StageController extends Controller
{
    public function stageDashboard()
    {
        return "this is Stage management page";
    }

    public function stageManagementHandler(Request $request)
    {

//         TODO: enable this after able to create user
//        $auth = Auth::user();
//        $this->redirectIfNotLoggedIn($auth);

        $data = $request->all();
        $data = array_map('trim', $data);

        $action = isset($data['action']) ? $data['action'] : null;
        if ($action == "create") {
            $this->createNewStageHandler($data);

        } else if ($action == "retrieve") {

//          TODO: change this to display correct view
            return $this->retrieveStage($data);

        } else if ($action == "update") {
            $this->updateStageHandler($data);

        } else if ($action == "delete") {
            $this->deleteStage($data);

        } else if ($action == "fetchAll") {

//          TODO: change this to display correct view
            return $this->fetchAllStage();

        } else if ($action == null) {

//          TODO : return using abort(404);
            return "abort 404";
        }


//         TODO: change this to proper page
//         return view('stageManagement');
        return "this is Stage management page";
    }

    private function parseCreateData($data)
    {
        return [
            'name' => isset($data['name']) ? $data['name'] : null,
            'description' => isset($data['description']) ? $data['description'] : null,
            'editable' => (isset($data['editable']) ? $data['editable'] : 'null') === 'true',
            'deletable' => (isset($data['deletable']) ? $data['deletable'] : 'null') === 'true',
        ];
    }

    private function isCreateDataValid($data)
    {

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255', 'unique:stages'],
            'description' => ['string', 'max:255'],
            'editable' => ['boolean'],
            'deletable' => ['boolean'],
        ]);

        return !($validator->fails());
    }

    private function createNewStageHandler($data)
    {
        $newStage = $this->createNewStage($data);

        if (!$newStage) {
            $this->displayCreateStageFailed();
        } else {
            $this->displayCreateStageSucceeded();
        }
    }

    private function createNewStage($data)
    {
        $data = $this->parseCreateData($data);

        if (!($this->isCreateDataValid($data))) {
            return null;
        }

        return Stage::create($data);
    }


    private function displayCreateStageFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> Stage cannot be created!
        </div>';
    }

    private function displayCreateStageSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> New stage has been created!
        </div>';
    }

    private function retrieveStage($data)
    {
        return Stage::findOrNew(
            isset($data['id']) ? $data['id'] : null
        );
    }

    private function parseUpdateData($data)
    {
        return [
            'id' => isset($data['id']) ? $data['id'] : null,
            'name' => isset($data['name']) ? $data['name'] : null,
            'description' => isset($data['description']) ? $data['description'] : null,
            'editable' => (isset($data['editable']) ? $data['editable'] : 'null') === 'true',
            'deletable' => (isset($data['deletable']) ? $data['deletable'] : 'null') === 'true',
        ];
    }

    private function isUpdateDataValid($data)
    {

        $validator = Validator::make($data, [
            'id' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255', 'unique:stages'],
            'description' => ['string', 'max:255'],
            'editable' => ['boolean'],
            'deletable' => ['boolean'],
        ]);

        return !($validator->fails());
    }

    private function updateStageHandler($data)
    {
        $updatedStage = $this->updateStage($data);

        if (!$updatedStage) {
            $this->displayUpdateStageFailed();
        } else {
            $this->displayUpdateStageSucceeded();
        }
    }

    private function updateStage($data)
    {
        $data = $this->parseUpdateData($data);

        if (!($this->isUpdateDataValid($data))) {
            return null;
        }

        $stage = Stage::find($data['id']);
        $stage['name'] = $data['name'];
        $stage['description'] = $data['description'];
        $stage['editable'] = $data['editable'];
        $stage['deletable'] = $data['deletable'];
        $stage->save();

        return $stage;
    }

    private function displayUpdateStageFailed()
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> Stage cannot be updated!
        </div>';
    }

    private function displayUpdateStageSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> Stage has been edited!
        </div>';
    }

    private function deleteStage(array $data)
    {
        $stage = Stage::find(
            isset($data['id']) ? $data['id'] : null
        );

        if ($stage->activeProjects()) {
            $this->displayDeleteStageFailed("Remove project(s) in this stage first");
        }

        if ($stage->prevStages()) {
            $this->displayDeleteStageFailed("Remove stage(s) referencing this stage first");
        }

        $stage->delete();
        $this->displayDeleteStageSucceeded();
    }

    private function displayDeleteStageFailed($solution)
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Failed !</strong> Stage cannot be deleted! ' . $solution .
            '</div>';
    }

    private function displayDeleteStageSucceeded()
    {
        echo '<div class="alert alert-success alert-dismissible fade show text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success !</strong> Stage has been deleted!
        </div>';
    }

    private function fetchAllStage()
    {

        try {
            return Stage::all();
        } catch (\Exception $e) {
            return [];
        }
    }
}
