<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description'
    ];

    /* Eloquent many to many function */
    public function users()
    {
        return $this->belongsToMany('App\User')->withPivot('id')->withTimestamps();
    }

    /* Eloquent many to many function */
    public function permissions()
    {
        return $this->belongsToMany('App\Permission')->withPivot('id')->withTimestamps();
    }

}
