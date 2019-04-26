<?php

namespace App\Http\Controllers;

use App\Note;
use App\Http\Resources\Note as NoteResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function projectManagementHandler(Request $request) {
        \Log::info($request->all());
        $data = $request->all();
    
    
        $action = isset($data['action']) ? $data['action'] : null;
        if ($action == "create") {
          $this->createNewNote($data);
        } else if ($action == "delete") {
            $this->deleteNote($data);
        } else if ($action == "update") {
            $this->updateNote($data);
        } else if ($action == "fetchAll") {
          return $this->fetchAllNotes($data);
        }
      }
    
      private function isCreateRequestValid($data) {
        $validator = Validator::make($data, [
          'note' => ['required', 'string', 'max:255'],
          'project' => ['required', 'integer'],
          'uploader' => ['required', 'integer']
        ]);
    
        return !($validator->fails());
      }
    
      private function parseCreateData($data) {
        return [
          'note' => $data['note'],
          'project' => $data['project'],
          'uploader' => $data['uploader']
        ];
      }
    
      private function createNewNote($data) {
        
        if (!($this->isCreateRequestValid($data))) {
          return null;
        }
    
        $data = $this->parseCreateData($data);
        $newNote = Note::create($data);
    
        $newNote->setProject($data['project']);
        $newNote->setUser($data['uploader']);
        $newNote->save();
    
        return $newNote;
      }
    
      private function deleteNote($data) {
        $note = Note::find(
            isset($data['id']) ? $data['id'] : null
        );
    
        if ($note) {
            $note->delete();
        }
      }
    
      private function isUpdateRequestValid($data) {
        $validator = Validator::make($data, [
          'id' => ['required', 'integer'],
          'note' => ['required', 'string', 'max:255'],
          'project' => ['required', 'integer'],
          'uploader' => ['required', 'integer']
        ]);
    
        return !($validator->fails());
      }
    
      private function parseUpdateData($data) {
        return [
          'id' => $data['id'],
          'note' => $data['note'],
          'project' => $data['project'],
          'uploader' => $data['uploader']
        ];
      }
    
      private function updateNote($data) {
        if (!($this->isUpdateRequestValid($data))) {
          return null;
        }
    
        $data = $this->parseUpdateData($data);
        \Log::info($data);
        $note = Note::find($data['id']);
        $note['note'] = $data['note'];
    
        $note->setProject($data['project']);
        $note->setUser($data['uploader']);
        $note->save();
    
        return $note;
      }
    
      private function fetchAllNotes() {
        $notes_per_page = 15;
        
        // Get notes
        $notes = Note::paginate($notes_per_page);
        // return as a resource
        return NoteResource::collection($notes);
      }
}
