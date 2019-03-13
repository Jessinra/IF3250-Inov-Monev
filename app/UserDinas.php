<?php
/**
 * Created by PhpStorm.
 * User: jessinra
 * Date: 3/11/19
 * Time: 11:05 PM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDinas extends Model
{
    //
    protected $fillable = ['id', 'dinas_id', 'role'];

    public function user()
    {
        return User::find($this->id);
    }

}
