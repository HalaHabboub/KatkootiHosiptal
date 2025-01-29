<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\DoctorUnavailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    public function index()
    {
        $appointments = Appointment::where('doctor_id', Auth::id())
            ->whereDate('date_time', Carbon::today())
            ->with('patient')
            ->get();

        return view('doctor', compact('appointments'));
    }

    public function getAllDoctors()
    {
        $doctors = Doctor::all();
        return view('viewAllDoctors', compact('doctors'));
    }

    public function getByDepartment($departmentId)
    {
        try {
            $doctors = Doctor::where('department_id', $departmentId)
                ->where('status', 'active')
                ->with([
                    'unavailableDates' => function ($query) {
                        $query->where('date', '>=', now()->format('Y-m-d'));
                    }
                ])
                ->get()
                ->map(function ($doctor) {
                    $doctor->unavailable_dates = $doctor->unavailableDates->pluck('date')->map(function ($date) {
                        return Carbon::parse($date)->format('Y-m-d');
                    });
                    return $doctor;
                });

            \Log::info('Doctors found for department ' . $departmentId . ':', ['count' => $doctors->count()]);

            return response()->json($doctors);
        } catch (\Exception $e) {
            \Log::error('Error fetching doctors: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch doctors'], 500);
        }
    }

    public function showAppointmentDetails($appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $patient = $appointment->patient;

        return view('viewAppointmentDetails', compact('appointment', 'patient'));
    }

    public function schedule()
    {
        $today = Carbon::today();
        $weekStart = $today->copy()->startOfWeek();
        $weekEnd = $today->copy()->endOfWeek();

        // Get week's appointments
        $weekDays = [];
        for ($date = $weekStart; $date->lte($weekEnd); $date->addDay()) {
            $weekDays[$date->format('Y-m-d')] = Appointment::where('doctor_id', Auth::id())
                ->whereDate('date_time', $date)
                ->with('patient')
                ->orderBy('date_time')
                ->get();
        }

        // Load unavailable dates from database
        $unavailableDates = DoctorUnavailability::where('doctor_id', Auth::id())
            ->pluck('date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        // Get today's total appointments
        $todayAppointments = Appointment::where('doctor_id', Auth::id())
            ->whereDate('date_time', today())
            ->count();

        // Get all appointments for the current month for calendar highlighting
        $monthAppointments = Appointment::where('doctor_id', Auth::id())
            ->whereMonth('date_time', today()->month)
            ->whereYear('date_time', today()->year)
            ->get()
            ->groupBy(function ($appointment) {
                return $appointment->date_time->format('Y-m-d');
            });

        return view('doctorSchedule', compact('weekDays', 'todayAppointments', 'monthAppointments', 'unavailableDates'));
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'day' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        auth()->user()->schedules()->create([
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('doctor.schedule')->with('success', 'Schedule added successfully');
    }

    public function deleteSchedule($id)
    {
        $schedule = auth()->user()->schedules()->findOrFail($id);
        $schedule->delete();

        return redirect()->route('doctor.schedule')->with('success', 'Schedule deleted successfully');
    }

    public function getMonthAppointments(Request $request)
    {
        $date = Carbon::parse($request->date);

        $appointments = Appointment::where('doctor_id', Auth::id())
            ->whereMonth('date_time', $date->month)
            ->whereYear('date_time', $date->year)
            ->get()
            ->groupBy(function ($appointment) {
                return $appointment->date_time->format('Y-m-d');
            });

        return response()->json($appointments);
    }

    public function showSchedule()
    {
        $doctorId = auth()->user()->id;

        // Example query to fetch unavailable days for the logged-in doctor
        $unavailableDays = DoctorUnavailability::where('doctor_id', $doctorId)
            ->pluck('date')
            ->toArray();

        $todayAppointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('date_time', now()->format('Y-m-d'))
            ->count();

        $weekDays = $this->fetchWeeklyAppointments($doctorId);

        return view('doctor.schedule', [
            'unavailableDays' => $unavailableDays,
            'todayAppointments' => $todayAppointments,
            'weekDays' => $weekDays,
        ]);
    }

    private function fetchWeeklyAppointments($doctorId)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        return Appointment::where('doctor_id', $doctorId)
            ->whereBetween('date_time', [$startOfWeek, $endOfWeek])
            ->get()
            ->groupBy(function ($appointment) {
                return $appointment->date_time->toDateString();
            });
    }

    public function schedule2()
    {
        $doctor = Auth::user();
        $today = Carbon::now();
        $startOfWeek = $today->copy()->startOfWeek();
        $endOfWeek = $today->copy()->endOfWeek();

        // Get appointments for the entire month for calendar
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();

        $monthlyAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereBetween('date_time', [$monthStart, $monthEnd])
            ->with('patient')
            ->get()
            ->groupBy(function ($appointment) {
                return Carbon::parse($appointment->date_time)->format('Y-m-d');
            });

        // Format appointments for calendar
        $calendarEvents = [];
        foreach ($monthlyAppointments as $date => $appointments) {
            foreach ($appointments as $appointment) {
                $calendarEvents[] = [
                    'id' => $appointment->id,
                    'title' => $appointment->patient->name,
                    'start' => Carbon::parse($appointment->date_time)->format('Y-m-d\TH:i:s'),
                    'end' => Carbon::parse($appointment->date_time)->addHours(1)->format('Y-m-d\TH:i:s'),
                    'className' => 'bg-' . $this->getStatusColor($appointment->status)
                ];
            }
        }

        // Weekly appointments (keeping existing functionality)
        $weekDays = Appointment::where('doctor_id', $doctor->id)
            ->whereBetween('date_time', [$startOfWeek, $endOfWeek])
            ->with('patient')
            ->orderBy('date_time')
            ->get()
            ->groupBy(function ($appointment) {
                return Carbon::parse($appointment->date_time)->format('Y-m-d');
            });

        $todayAppointments = $weekDays[$today->format('Y-m-d')] ?? collect([]);

        return view('doctorSchedule', compact('weekDays', 'todayAppointments', 'calendarEvents', 'monthlyAppointments'));
    }

    private function getStatusColor($status)
    {
        return [
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'completed' => 'info'
        ][$status] ?? 'secondary';
    }

    public function getUnavailableDates($doctorId)
    {
        try {
            $unavailableDates = DoctorUnavailability::where('doctor_id', $doctorId)
                ->where('date', '>=', now()->format('Y-m-d'))
                ->pluck('date')
                ->map(function ($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                });

            return response()->json([
                'success' => true,
                'unavailable_dates' => $unavailableDates
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching unavailable dates'
            ], 500);
        }
    }

    public function getWeeklyAppointments($date)
    {
        $startDate = Carbon::parse($date)->startOfWeek();
        $endDate = Carbon::parse($date)->endOfWeek();

        $appointments = Appointment::where('doctor_id', Auth::id())
            ->whereBetween('date_time', [$startDate, $endDate])
            ->with('patient')
            ->get()
            ->map(function ($appointment) {
                $appointment->time = Carbon::parse($appointment->date_time)->format('H:i');
                return $appointment;
            })
            ->groupBy(function ($appointment) {
                return Carbon::parse($appointment->date_time)->format('Y-m-d');
            });

        // Fill in empty days
        $result = [];
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $result[$dateStr] = $appointments[$dateStr] ?? [];
        }

        return response()->json($result);
    }
}
