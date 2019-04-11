<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    protected $fillable = [
        'name', 'description', 'editable', 'deletable',
    ];

    public function nextStages()
    {
        return $this->belongsToMany('App\Stage', 'workflow_next', 'current_id', 'next_id');
    }

    public function prevStages()
    {
        return $this->belongsToMany('App\Stage', 'workflow_prev', 'current_id', 'prev_id');
    }

    public function activeProjects()
    {
        return $this->belongsToMany('App\Project', "project_stage")->withPivot('id')->withTimestamps();
    }
}
