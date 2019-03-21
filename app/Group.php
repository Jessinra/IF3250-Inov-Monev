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
        return $this->belongsToMany('App\User')->withPivot('id')->withTimestamps();
    }
}
