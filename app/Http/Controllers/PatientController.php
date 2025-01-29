<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Department;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\DoctorUnavailability;

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
        $patient = auth()->user();
        $appointments = Appointment::where('patient_id', $patient->patient_id)
            ->with('doctor')
            ->orderBy('date_time', 'desc')
            ->get()
            ->map(function ($appointment) {
                // Ensure date_time is a Carbon instance
                $appointment->date_time = Carbon::parse($appointment->date_time);
                return $appointment;
            });

        $stats = [
            'total' => $appointments->count(),
            'upcoming' => $appointments->where('status', 'confirmed')->where('date_time', '>', now())->count(),
            'completed' => $appointments->where('date_time', '<', now())->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
        ];

        $departments = Department::with([
            'doctors' => function ($query) {
                $query->where('status', 'active');
            }
        ])->get();

        return view('patientAppointments', compact('appointments', 'departments', 'stats'));
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

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:patients,email,' . auth()->id() . ',patient_id',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'existing_conditions' => 'nullable|string',
            'current_medications' => 'nullable|string',
            'allergies' => 'nullable|string',
        ]);

        $patient = auth()->user();
        $patient->update($request->all());

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function storeAppointment(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,doctor_id',
            'date_time' => 'required|date|after:now',
            'message' => 'nullable|string',
        ]);

        $appointmentDateTime = Carbon::parse($request->date_time);
        $maxDate = now()->addMonths(3)->endOfMonth();
        $appointmentHour = (int) $appointmentDateTime->format('H');

        // Check if appointment is within allowed hours (10 AM to 5 PM)
        if ($appointmentHour < 10 || $appointmentHour >= 17) {
            return redirect()->back()
                ->with('error', 'Appointments are only available between 10 AM and 5 PM.')
                ->withInput();
        }

        // Check if appointment is within the 3-month limit
        if ($appointmentDateTime->gt($maxDate)) {
            return redirect()->back()
                ->with('error', 'Appointments can only be booked up to ' . $maxDate->format('F Y') . '.')
                ->withInput();
        }

        // Check for existing appointment
        $existingAppointment = Appointment::where('doctor_id', $request->doctor_id)
            ->where('date_time', $appointmentDateTime)
            ->whereNotIn('status', ['cancelled'])
            ->first();

        if ($existingAppointment) {
            return redirect()->back()
                ->with('error', 'This time slot is already booked with this doctor. Please select a different time.')
                ->withInput();
        }

        // Check if doctor is unavailable on this date
        $appointmentDate = $appointmentDateTime->format('Y-m-d');
        $isUnavailable = DoctorUnavailability::where('doctor_id', $request->doctor_id)
            ->whereDate('date', $appointmentDate)
            ->exists();

        if ($isUnavailable) {
            return redirect()->back()
                ->with('error', 'Sorry, the doctor is not available on this date. Please select another day.')
                ->withInput();
        }

        // Create the appointment
        $appointment = new Appointment([
            'appointment_id' => (string) Str::uuid(),
            'patient_id' => auth()->id(),
            'doctor_id' => $request->doctor_id,
            'date_time' => $appointmentDateTime,
            'message' => $request->message,
            'status' => 'pending'
        ]);

        $appointment->save();

        return redirect()->route('appointments')->with('success', 'Appointment request submitted successfully!');
    }

    public function cancelAppointment(Request $request, $id)
    {
        try {
            $appointment = Appointment::where('appointment_id', $id)
                ->where('patient_id', auth()->id())
                ->firstOrFail();

            $appointment->update([
                'status' => 'cancelled'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling appointment'
            ], 500);
        }
    }

    // Add this new method to get doctors by department
    public function getDoctorsByDepartment($departmentId)
    {
        $doctors = Doctor::where('department_id', $departmentId)->get();
        return response()->json($doctors);
    }
}
