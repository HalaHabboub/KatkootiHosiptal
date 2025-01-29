<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Doctor extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'specialization',
        'qualification',
        'status',
        'department_id',
        'image',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    // Ensure the relationship uses the correct foreign key
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    protected $primaryKey = 'doctor_id';  // or 'admin_id' for Admin model
    public $incrementing = true;  // If the primary key auto-increments
    protected $keyType = 'int';   // Type of primary key (int for auto-incrementing)

}
