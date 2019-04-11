<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name', 'description'
    ];

    /* Eloquent many to many function */
    public function users()
    {
        return $this->belongsToMany('App\User', "user_group")->withPivot('id')->withTimestamps();
    }

    public function projects()
    {
        return $this->hasMany('App\Project', "group_project")->withPivot('id')->withTimestamps();
    }
}
