<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function updateStatus(Request $request, $appointment)
    {
        try {
            $appointmentModel = Appointment::findOrFail($appointment);

            $validated = $request->validate([
                'status' => 'required|in:pending,confirmed,cancelled'
            ]);

            if ($appointmentModel->doctor_id != auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action'
                ], 403);
            }

            $appointmentModel->status = $validated['status'];
            $appointmentModel->save();

            return response()->json([
                'success' => true,
                'message' => 'Appointment status updated to ' . $validated['status']
            ]);
        } catch (\Exception $e) {
            \Log::error('Appointment status update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update appointment status: ' . $e->getMessage()
            ], 500);
        }
    }
}