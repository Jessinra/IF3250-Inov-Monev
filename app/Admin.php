<?php
/**
 * Created by PhpStorm.
 * User: jessinra
 * Date: 3/11/19
 * Time: 11:09 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    //
    protected $fillable = ['id'];

    public function user()
    {
        return User::find($this->id);
    }

}