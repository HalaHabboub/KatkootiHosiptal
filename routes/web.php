<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;



Route::get('/', function () {
    return view('patient');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


// Handle login form submission (POST request)
Route::post('/auth/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// Patient Dashboard
Route::middleware(['auth:patient'])->group(function () {
    Route::get('/patient/dashboard', [PatientController::class, 'index'])->name('patient.dashboard');
    Route::get('/patient', function () {
        return view('patient');
    })->name('patient');
});

// Doctor Dashboard
Route::middleware(['auth:doctor'])->group(function () {
    Route::get('/doctor/dashboard', [DoctorController::class, 'index'])->name('doctor.dashboard');
    Route::get('/doctor', function () {
        return view('doctor');
    })->name('doctor');
});

// Admin Dashboard
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});


Route::get('/index', function () {
    return view('index');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/viewAllDoctors', function () {
    return view('viewAllDoctors');
})->name('doctors.index');

Route::get('/patientProfile', [PatientController::class, 'show'])->name('profile');

Route::get('/patientAppointments', [PatientController::class, 'appointments'])->name('appointments');

Route::get('/auth/login', [LoginController::class, 'showLoginForm'])->name('login');



Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
