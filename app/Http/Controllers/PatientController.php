<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {
        // Add any data you want to pass to the patient view
        return view('patient');
    }

    public function show()
    {
        // Add logic to show patient profile
        return view('patient.profile');
    }

    public function appointments()
    {
        // Add logic to show patient appointments
        return view('patient.appointments');
    }
}
