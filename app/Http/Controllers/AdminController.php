<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['patient', 'doctor'])
            ->whereDate('date_time', today())
            ->get();

        return view('admin', compact('appointments'));
    }

    public function showAppointment($appointmentId)
    {
        $appointment = Appointment::with(['patient', 'doctor'])
            ->where('appointment_id', $appointmentId)
            ->firstOrFail();

        $patient = $appointment->patient;

        return view('viewAppointmentDetails', compact('appointment', 'patient'));
    }

    public function manageDoctors()
    {
        $doctors = Doctor::with('department')->get();
        $departments = Department::all();
        return view('manageDoctors', compact('doctors', 'departments'));
    }

    public function storeDoctor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors',
            'department_id' => 'required|exists:departments,department_id',
            'phone' => 'required|string',
            'password' => 'required|min:6',
            'specialization' => 'nullable|string',
            'qualification' => 'nullable|string'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = 'active';
        $validated['specialization'] = $request->specialization ?? 'General';
        $validated['qualification'] = $request->qualification ?? 'MBBS'; // Default qualification

        Doctor::create($validated);

        return redirect()->route('admin.doctors.manage')
            ->with('success', 'Doctor added successfully');
    }

    public function deleteDoctor(Doctor $doctor)
    {
        $doctor->delete();
        return redirect()->route('admin.doctors.manage')
            ->with('success', 'Doctor removed successfully');
    }

    public function updateDoctor(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email,' . $doctor->doctor_id . ',doctor_id',
            'department_id' => 'required|exists:departments,department_id',
            'phone' => 'required|string',
            'specialization' => 'nullable|string',
            'qualification' => 'nullable|string'
        ]);

        $doctor->update($validated);

        return redirect()->route('admin.doctors.manage')
            ->with('success', 'Doctor updated successfully');
    }
}
