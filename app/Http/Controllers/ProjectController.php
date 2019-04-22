<?php

namespace App\Http\Controllers;

class ProjectController extends Controller
{
  public function projects() {
    return view('pages.projects');
  }

  public function projectManagementHandler() {
    $data = $request->all();
    $data = array_map('trim', data);

    $action = isset($data['action']) ? $data['action'] : null;
    if ($action == "create") {
      $this->createNewProject($data);
    }
  }

  private function parseCreateData($data) {
    return [
      'name' => isset($data['name']) ? $data['name'] : null,
      'description' => isset($data['description']) ? $data['description'] : null,
      'file' => isset($data['file']) ? $data['file'] : null
    ];
  }

  private function isCreateDataValid($data) {
    $validator = Validator::make($data, [
      'name' => ['required', 'string', 'max:255', 'unique:projects'],
      'description' => ['string', 'max:255'],
      'file' => ['string', 'max:255']
    ]);

    return !($validator->fails());
  }

  private function createNewProject($data) {
    $data = $this->parseCreateData($data);

    if (!($this->isCreateDataValid($data))) {
      return null;
    }

    $newProject = Project::create($data);

    return $newProject;
  }
}
