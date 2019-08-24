<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'email',
        'name',
        'password',
    ];

    protected $hidden = [
        'email_verified',
        'remember_token',
        'created_at',
        'updated_at',
    ];
}
