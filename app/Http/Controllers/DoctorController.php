<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
                ->select('doctor_id', 'name')
                ->get();

            \Log::info('Doctors found for department ' . $departmentId . ':', ['count' => $doctors->count()]);

            return response()->json($doctors);
        } catch (\Exception $e) {
            \Log::error('Error fetching doctors: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch doctors'], 500);
        }
    }
}
