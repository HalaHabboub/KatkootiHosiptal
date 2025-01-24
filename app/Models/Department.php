<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $primaryKey = 'department_id';

    protected $fillable = ['name', 'description'];

    public function doctors()
    {
        return $this->hasMany(Doctor::class, 'department_id');
    }
}
