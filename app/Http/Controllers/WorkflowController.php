<?php

namespace App\Http\Controllers;

use App\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkflowController extends Controller
{

    public function workflowDashboard()
    {
        return "this is Workflow management page";
    }

    public function workflowManagementHandler(Request $request)
    {

//         TODO: enable this after able to create user
//        $auth = Auth::user();
//        $this->redirectIfNotLoggedIn($auth);

        $data = $request->all();
        $data = array_map('trim', $data);

        $action = isset($data['action']) ? $data['action'] : null;

        if ($action == "retrieve") {

//          TODO: change this to display correct view
            return $this->retrieveWorkflow();

        } else if ($action == "connect_stage") {
            $this->connectStage($data);

        } else if ($action == "disconnect_stage") {
            $this->disconnectStage($data);

        } else if ($action == null) {

//          TODO : return using abort(404);
            return "abort 404";
        }


//         TODO: change this to proper page
//         return view('workflowManagement');
        return "this is Workflow management page";
    }

    private function retrieveWorkflow()
    {
        $stages = Stage::all();
        $workflow = [];

        foreach ($stages as $stage) {
            $next = $stage->nextStages;
            $prev = $stage->prevStages;

//            print_r($next);
//            print_r($prev);

            $connection['this'] = $stage;
            $connection['next'] = $next;
            $connection['prev'] = $prev;

            array_push($workflow, $connection);
        }

        // TODO: pass this to correct view
        return $workflow;
    }

    private function connectStage($data)
    {
        $data = $this->parseUpdateData($data);
        if (!($this->isUpdateDataValid($data))) {
            return null;
        }

        $currStage = Stage::find($data['id']);
        $nextStage = Stage::find($data['next_id']);

        if (!($currStage) || !($nextStage)) {
            return null;
        }

        $currStage->addNext($nextStage->id);
        $nextStage->addPrev($currStage->id);
    }

    private function parseUpdateData($data)
    {
        return [
            'id' => isset($data['id']) ? $data['id'] : null,
            'next_id' => isset($data['next_id']) ? $data['next_id'] : null,
        ];
    }

    private function isUpdateDataValid($data)
    {
        $validator = Validator::make($data, [
            'id' => ['required', 'string'],
            'next_id' => ['required', 'string'],
        ]);

        return !($validator->fails());
    }

    private function disconnectStage(array $data)
    {
        $data = $this->parseUpdateData($data);

        if (!($this->isUpdateDataValid($data))) {
            return null;
        }

        $currStage = Stage::find($data['id']);
        $nextStage = Stage::find($data['next_id']);

        if (!($currStage) || !($nextStage)) {
            return null;
        }

        $currStage->removeNext($nextStage->id);
        $nextStage->removePrev($currStage->id);
    }
}
