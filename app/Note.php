<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'note'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', "user_note")->withPivot('id')->withTimestamps();
    }

    public function project()
    {
        return $this->belongsTo('App\Project', "project_note")->withPivot('id')->withTimestamps();
    }
}
