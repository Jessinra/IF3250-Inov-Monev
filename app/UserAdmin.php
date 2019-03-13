<?php
/**
 * Created by PhpStorm.
 * User: jessinra
 * Date: 3/11/19
 * Time: 11:09 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class UserAdmin extends Model
{
    //
    protected $fillable = ['id', 'dinas_id'];

    public function user()
    {
        return User::find($this->id);
    }

}