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
        $numberOfWeeks = 4;
        $weekDays = [];

        for ($i = 0; $i < $numberOfWeeks; $i++) {
            $startOfWeek = Carbon::now()->startOfWeek()->addWeeks($i);
            $endOfWeek = Carbon::now()->endOfWeek()->addWeeks($i);

            // Create a date range for the week
            $dates = [];
            for ($date = clone $startOfWeek; $date->lte($endOfWeek); $date->addDay()) {
                $dates[$date->format('Y-m-d')] = [];
            }

            // Get appointments for this week
            $appointments = Appointment::where('doctor_id', Auth::id())
                ->whereBetween('date_time', [$startOfWeek, $endOfWeek])
                ->orderBy('date_time')
                ->get();

            // Group appointments by date
            foreach ($appointments as $appointment) {
                $dateKey = Carbon::parse($appointment->date_time)->format('Y-m-d');
                if (!isset($dates[$dateKey])) {
                    $dates[$dateKey] = [];
                }
                $dates[$dateKey][] = $appointment;
            }

            $weekDays[] = [
                'week_start' => $startOfWeek->format('Y-m-d'),
                'week_end' => $endOfWeek->format('Y-m-d'),
                'dates' => $dates
            ];
        }

        $todayAppointments = Appointment::where('doctor_id', Auth::id())
            ->whereDate('date_time', Carbon::today())
            ->count();

        $unavailableDates = DoctorUnavailability::where('doctor_id', Auth::id())
            ->pluck('date')
            ->map(function ($date) {
                return $date->format('Y-m-d');
            });

        return view('doctorSchedule', compact('weekDays', 'todayAppointments', 'unavailableDates'));
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