<?php

    namespace App;

    use Illuminate\Database\Eloquent\Model;

    class Project extends Model
    {
        protected $fillable = [
            'name', 'description', 'file'
        ];

        public function users() {
            return $this->belongsToMany('App\User', "user_project")->withPivot('id')->withTimestamps();
        }

        public function group() {
            return $this->belongsTo('App\Group', "group_project")->withPivot('id')->withTimestamps();
        }

        public function activeStage() {
            return $this->belongsTo('App\Stage', "project_active_stage")->withPivot('id')->withTimestamps();
        }

        public function visitedStages() {
            return $this->belongsToMany('App\Stage', "project_stage")->withPivot('id')->withTimestamps();
        }

        public function notes() {
            return $this->hasMany('App\Note', "project_note")->withPivot('id')->withTimestamps();
        }

        public function addUser($userIds) {
            $this->users()->attach($userIds);
        }

        public function removeUser($userIds) {
            $this->users()->detach($userIds);
        }

        public function setGroup($groupId) {
            $this->group()->associate($groupId);
        }

        public function setActiveStage($stageId) {
            $this->activeStage()->associate($stageId);
        }

        public function moveToStage($stageId) {
            $this->addVisitedStage($this->activeStage()->id);
            $this->setActiveStage($stageId);
        }

        public function addVisitedStage($stageId) {
            $this->visitedStages()->attach($stageId);
        }

        public function addNote($noteId) {
            $this->notes()->attach($noteId);
        }

        // Might not needed actually
        public function removeNote($noteId) {
            $this->notes()->detach($noteId);
        }
    }
