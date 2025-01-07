<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Patient extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'date_of_birth',
        'gender',
        'blood_group',
        'existing_conditions',
        'current_medications',
        'allergies'
    ];

    protected $primaryKey = 'patient_id';  // Specify the custom primary key
    public $incrementing = true;  // If auto-incrementing
    protected $keyType = 'int';   // Type of primary key
}
