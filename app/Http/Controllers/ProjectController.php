<?php

namespace App\Http\Controllers;

use App\Project;
use App\Http\Resources\Project as ProjectResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
  public function projectManagementHandler(Request $request) {
    $data = $request->all();

    $action = isset($data['action']) ? $data['action'] : null;
    if ($action == "create") {
      $this->createNewProject($data);
    } else if ($action == "fetchAll") {
      return $this->fetchAllProjects($data);
    }
  }

  private function isRequestValid($data) {
    $validator = Validator::make($data, [
      'name' => ['required', 'string', 'max:255', 'unique:projects'],
      'description' => ['string', 'max:255'],
      'file' => ['required', 'file', 'max:5000']
    ]);

    return !($validator->fails());
  }

  private function parseCreateData($data) {
    $uploadedFile = $data->file('file');
    $path = $uploadedFile->store('public/files');

    return [
      'name' => isset($data['name']) ? $data['name'] : null,
      'description' => isset($data['description']) ? $data['description'] : null,
      'file' => $path
    ];
  }

  private function createNewProject($data) {
    if (!($this->isRequestValid($data))) {
      return null;
    }

    $data = $this->parseCreateData($data);

    $newProject = Project::create($data);

    return $newProject;
  }

  private function fetchAllProjects() {
    $projects_per_page = 15;
    
    // Get projects
    $projects = Project::paginate($projects_per_page);

    // return as a resource
    return ProjectResource::collection($projects);
  }
}
