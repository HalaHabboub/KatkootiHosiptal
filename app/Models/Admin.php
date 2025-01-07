<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role'
    ];
    protected $primaryKey = 'admin_id';  // Specify the custom primary key
    public $incrementing = true;
    protected $keyType = 'int';
}
