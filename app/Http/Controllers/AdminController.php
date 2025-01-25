<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;

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
}
