<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
    
        'name', 
        'email', 
        'mobile_number', 
        'gender', 
        'address',
        'password',
        'old password',
        'password',
        'confirm password'

    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];

    // If you want to add any custom attributes or methods to the Admin model, you can do so here.
}

