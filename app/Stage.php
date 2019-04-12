<?php

    namespace App;

    use Illuminate\Database\Eloquent\Model;

    class Stage extends Model
    {
        protected $fillable = [
            'name', 'description', 'editable', 'deletable',
        ];

        public function nextStages() {
            return $this->belongsToMany('App\Stage', 'workflow_next', 'current_id', 'next_id');
        }

        public function prevStages() {
            return $this->belongsToMany('App\Stage', 'workflow_prev', 'current_id', 'prev_id');
        }

        public function activeProjects() {
            return $this->hasMany('App\Project', "project_active_stage")->withPivot('id')->withTimestamps();
        }

        public function visitedProjects() {
            return $this->belongsToMany('App\Stage', "project_stage")->withPivot('id')->withTimestamps();
        }

        public function addNext($stageId) {
            $this->nextStages()->attach($stageId);
        }

        public function removeNext($stageId) {
            $this->nextStages()->detach($stageId);
        }

        public function addPrev($stageId) {
            $this->prevStages()->attach($stageId);
        }

        public function removePrev($stageId) {
            $this->prevStages()->detach($stageId);
        }

        public function addActiveProjects($projectId) {
            $this->activeProjects()->attach($projectId);
        }

        public function removeActiveProjects($projectId) {
            $this->activeProjects()->detach($projectId);
        }
    }
