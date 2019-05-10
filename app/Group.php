<?php

    namespace App;

    use Illuminate\Database\Eloquent\Model;

    class Group extends Model
    {
        protected $fillable = [
            'name', 'description'
        ];

        /* Eloquent many to many function */
        public function users() {
            return $this->belongsToMany('App\User', "user_group")->withPivot('id')->withTimestamps();
        }

        public function projects() {
            return $this->belongsToMany('App\Project', "group_project")->withPivot('id')->withTimestamps();
        }

        public function addUser($userId){
            $this->users()->attach($userId);
        }

        public function removeUser($userId){
            $this->users()->detach($userId);
        }

        public function addProject($projectId){
            $this->projects()->attach($projectId);
        }

        public function removeProject($projectId){
            $this->projects()->detach($projectId);
        }
    }
