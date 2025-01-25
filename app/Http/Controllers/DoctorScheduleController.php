<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DoctorUnavailability;
use App\Models\Appointment;
use Carbon\Carbon;

class DoctorScheduleController extends Controller
{
    // ...existing code...

    public function index()
    {
        // Existing code for weekly view
        $weekDays = // ...your existing code...
            $todayAppointments = // ...your existing code...

            // Prepare calendar events
            $calendarEvents = Appointment::where('doctor_id', Auth::id())
                ->get()
                ->map(function ($appointment) {
                    return [
                        'id' => $appointment->appointment_id,
                        'title' => $appointment->patient->name,
                        'start' => $appointment->date_time,
                        'backgroundColor' => $this->getStatusColor($appointment->status),
                        'borderColor' => $this->getStatusColor($appointment->status),
                    ];
                });

        // Get unavailable dates
        $unavailableDates = DoctorUnavailability::where('doctor_id', Auth::id())
            ->pluck('date')
            ->map(function ($date) {
                return $date->format('Y-m-d');
            });

        return view('doctorSchedule', compact('weekDays', 'todayAppointments', 'calendarEvents', 'unavailableDates'));
    }

    private function getStatusColor($status)
    {
        return [
            'pending' => '#ffc107',
            'confirmed' => '#28a745',
            'cancelled' => '#dc3545',
            'completed' => '#17a2b8',
        ][$status] ?? '#6c757d';
    }

    public function markUnavailable(Request $request)
    {
        try {
            \Log::info('Received dates:', $request->all());

            $validated = $request->validate([
                'dates' => 'required|array',
                'dates.*' => 'date',
                'timezone' => 'string|nullable'
            ]);

            \DB::beginTransaction();

            try {
                // Get existing dates
                $existingDates = DoctorUnavailability::where('doctor_id', Auth::id())
                    ->pluck('date')
                    ->map(function ($date) {
                        return $date->format('Y-m-d');
                    })
                    ->toArray();

                // Process new dates with timezone consideration
                $processedDates = collect($validated['dates'])->map(function ($date) use ($request) {
                    // Create Carbon instance in the user's timezone if provided
                    $carbonDate = $request->timezone ?
                        Carbon::parse($date, $request->timezone)->startOfDay() :
                        Carbon::parse($date)->startOfDay();

                    return $carbonDate->format('Y-m-d');
                })->toArray();

                // Combine with new dates and remove duplicates
                $allDates = array_unique(array_merge($existingDates, $processedDates));

                // Insert only new dates that don't already exist
                $newDates = array_diff($allDates, $existingDates);

                if (!empty($newDates)) {
                    $datesToInsert = array_map(function ($date) {
                        return [
                            'doctor_id' => Auth::id(),
                            'date' => $date,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }, $newDates);

                    DoctorUnavailability::insert($datesToInsert);
                }

                \DB::commit();

                \Log::info('Successfully updated unavailable dates for doctor: ' . Auth::id());

                return response()->json([
                    'success' => true,
                    'message' => 'Dates updated successfully',
                    'dates' => $allDates // Return all dates for verification
                ]);

            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Error updating unavailable dates: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update dates: ' . $e->getMessage()
            ], 500);
        }
    }
}