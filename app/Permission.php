<?php

    namespace App;

    use Illuminate\Database\Eloquent\Model;

    class Permission extends Model
    {
        protected $fillable = [
            'name', 'description'
        ];

        /* Eloquent many to many function */
        public function roles() {
            return $this->belongsToMany('App\Role', "role_permission")->withPivot('id')->withTimestamps();
        }
    }
