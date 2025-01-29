<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
            'qualification' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = public_path('images/doctors/' . $imageName);
                $image->move(public_path('images/doctors'), $imageName);
                $validated['image'] = '/images/doctors/' . $imageName;
            }

            $validated['password'] = Hash::make($validated['password']);
            $validated['status'] = 'active';
            $validated['specialization'] = $request->specialization ?? 'General';
            $validated['qualification'] = $request->qualification ?? 'MBBS';

            Doctor::create($validated);
            return redirect()->route('admin.doctors.manage')->with('success', 'Doctor added successfully');
        } catch (\Exception $e) {
            \Log::error('Error storing doctor: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error storing doctor: ' . $e->getMessage());
        }
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
            'qualification' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            // Start with existing doctor data
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'department_id' => $validated['department_id'],
                'phone' => $validated['phone'],
                'specialization' => $validated['specialization'],
                'qualification' => $validated['qualification']
            ];

            // Handle image upload if present
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($doctor->image && file_exists(public_path($doctor->image))) {
                    unlink(public_path($doctor->image));
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/doctors'), $imageName);
                $updateData['image'] = '/images/doctors/' . $imageName;
            }

            // Update the doctor record
            $doctor->update($updateData);

            return redirect()->route('admin.doctors.manage')
                ->with('success', 'Doctor updated successfully');
        } catch (\Exception $e) {
            \Log::error('Error updating doctor: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating doctor: ' . $e->getMessage());
        }
    }
}
