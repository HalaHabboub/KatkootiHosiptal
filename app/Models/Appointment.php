<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $primaryKey = 'appointment_id';
    public $incrementing = false; // UUID is not auto-incrementing
    protected $keyType = 'string';

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'doctor_id',
        'admin_id',
        'date_time',
        'status',
        'message',
    ];

    // Add date casting
    protected $casts = [
        'date_time' => 'datetime',
    ];

    // Add date mutator
    public function setDateTimeAttribute($value)
    {
        $this->attributes['date_time'] = Carbon::parse($value);
    }

    // Define relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
