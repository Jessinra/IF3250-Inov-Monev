<?php

    namespace App;

    use Illuminate\Database\Eloquent\Model;

    class Note extends Model
    {
        protected $fillable = [
            'note'
        ];

        public function user() {
            return $this->belongsTo('App\User');
        }

        public function project() {
            return $this->belongsTo('App\Project');
        }

        public function setUser($userId) {
            $this->user()->associate($userId);
        }

        public function setProject($projectId) {
            $this->project()->associate($projectId);
        }
    }
