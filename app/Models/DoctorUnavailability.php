<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorUnavailability extends Model
{
    protected $fillable = ['doctor_id', 'date'];

    protected $casts = [
        'date' => 'date'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}