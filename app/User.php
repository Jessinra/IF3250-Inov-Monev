<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    const TYPE_DINAS = "Dinas";
    const TYPE_KOMINFO = "Kominfo";
    const TYPE_ADMIN = "Admin";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullname', 'username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isUserDinas()
    {
        return UserDinas::find($this->id);
    }

    public function isUserKominfo()
    {
        return UserKominfo::find($this->id);
    }

    public function isAdmin()
    {
        return Admin::find($this->id);
    }
}
