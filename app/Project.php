<?php

    namespace App;

    use Illuminate\Database\Eloquent\Model;

    class Project extends Model
    {
        protected $fillable = [
            'name', 'description', 'file'
        ];

        public function user() {
            return $this->belongsTo('App\User');
        }

        public function group() {
            return $this->belongsToMany('App\Group', "group_project")->withPivot('id')->withTimestamps();
        }

        public function activeStage() {
            return $this->belongsTo('App\Stage');
        }

        public function visitedStages() {
            return $this->belongsToMany('App\Stage', "project_stage")->withPivot('id')->withTimestamps();
        }

        public function notes() {
            return $this->hasMany('App\Note');
        }

        public function setUser($userId) {
            $this->user()->associate($userId);
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
