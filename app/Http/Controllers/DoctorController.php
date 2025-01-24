<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        return view('doctor');
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
