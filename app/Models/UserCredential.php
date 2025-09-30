<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCredential extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users_credentials';

    protected $fillable = [
        'matricula',
        'is_admin',
        'admin_level',
        'password',
    ];

    public $timestamps = false;
}
