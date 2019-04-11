<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name', 'description', 'file'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', "user_project")->withPivot('id')->withTimestamps();
    }

    public function group()
    {
        return $this->belongsTo('App\Group', "group_project")->withPivot('id')->withTimestamps();
    }

    public function visitedStages()
    {
        return $this->belongsToMany('App\Stage', "project_stage")->withPivot('id')->withTimestamps();
    }

    public function notes()
    {
        return $this->hasMany('App\Note', "project_note")->withPivot('id')->withTimestamps();
    }
}
