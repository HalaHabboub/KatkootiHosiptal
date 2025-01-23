<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

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

    public function completeRegistration()
    {
        return view('auth.completeRegistration');
    }

    public function storeProfile(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:15',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'blood_group' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'existing_conditions' => 'nullable|string',
            'current_medications' => 'nullable|string',
            'allergies' => 'nullable|string',
        ]);

        $patient = Auth::user();
        $patient->update($request->all());

        return redirect()->route('patient.dashboard')->with('success', 'Profile completed successfully!');
    }
}
