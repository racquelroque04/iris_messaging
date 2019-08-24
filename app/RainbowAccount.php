<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RainbowAccount extends Model
{
    protected $fillable = [
        'user_id',
        'contact_id',
        'email',
        'password'
    ];

    public function rainbowAccount()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
