<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorScheduleController;  // Add this line
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

Route::middleware(['web'])->group(function () {
    Route::middleware(['cache.headers:private;max_age=0;must_revalidate;'])->group(function () {

        Route::get('/home', function () {
            return view('patient');
        })->name('home');

        Route::middleware(['auth:patient'])->group(function () {
            Route::get('/', function () {
                return view('patient');
            });
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

        // Update the register route to use our custom controller
        Route::post('/register', [RegisterController::class, 'register'])->name('register');

        // Update the logout route - remove middleware
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        // Update the root route to always show patient view
        Route::get('/', function () {
            return view('patient');
        })->name('home');

        // Patient Dashboard
        Route::middleware(['auth:patient'])->group(function () {

            Route::get('/appointments', [PatientController::class, 'appointments'])->name('appointments');

            Route::get('/patient/dashboard', [PatientController::class, 'index'])->name('patient.dashboard');
            Route::get('/patient', function () {
                return view('patient');
            })->name('patient');
            Route::put('/profile/update', [PatientController::class, 'updateProfile'])->name('profile.update');
            Route::post('/appointments', [PatientController::class, 'storeAppointment'])->name('appointments.store');
            Route::post('/appointments/{id}/cancel', [PatientController::class, 'cancelAppointment'])->name('appointments.cancel');
            Route::get('/doctors/department/{id}', [PatientController::class, 'getDoctorsByDepartment'])->name('doctors.by.department');
        });

        // Doctor Dashboard
        Route::middleware(['auth:doctor'])->group(function () {
            Route::get('/doctor/dashboard', [DoctorController::class, 'index'])->name('doctor.dashboard');
            Route::get('/doctor', [DoctorController::class, 'index'])->name('doctor');
            Route::get('/appointment/details/{appointmentId}', [DoctorController::class, 'showAppointmentDetails'])
                ->name('appointment.details');

            // Add these new routes for doctor schedule
            Route::get('/doctor/schedule', [DoctorController::class, 'schedule'])->name('doctor.schedule');
            Route::post('/doctor/schedule', [DoctorController::class, 'storeSchedule'])->name('doctor.schedule.store');
            Route::delete('/doctor/schedule/{schedule}', [DoctorController::class, 'deleteSchedule'])->name('doctor.schedule.delete');

            // Update or add this route for appointment status updates
            Route::post('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])
                ->name('appointments.updateStatus');

            // Add the new route for marking doctor unavailable
            Route::post('/doctor/mark-unavailable', [DoctorScheduleController::class, 'markUnavailable'])
                ->name('doctor.mark-unavailable');

            // Add the new route for getting weekly appointments
            Route::get('/doctor/appointments/week/{date}', [DoctorController::class, 'getWeeklyAppointments'])
                ->name('doctor.weekly-appointments');
        });

        // Admin Dashboard
        Route::middleware(['auth:admin'])->group(function () {
            Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
            Route::get('/admin/appointment/{appointmentId}', [AdminController::class, 'showAppointment'])
                ->name('admin.appointment.details');  // This matches the route name used in admin.blade.php

            // Doctor management routes
            Route::prefix('admin/doctors')->name('admin.doctors.')->group(function () {
                Route::get('/', [AdminController::class, 'manageDoctors'])->name('manage');
                Route::post('/', [AdminController::class, 'storeDoctor'])->name('store');
                Route::put('/{doctor}', [AdminController::class, 'updateDoctor'])->name('update');
                Route::delete('/{doctor}', [AdminController::class, 'deleteDoctor'])->name('delete');
            });
        });

        Route::get('/about', function () {
            return view('about');
        })->name('about');

        Route::get('/viewAllDoctors', [DoctorController::class, 'getAllDoctors'])->name('doctors.index');

        Route::get('/patientProfile', function () {
            return view('profile.update-profile-information-form');
        })->name('profile')->middleware('auth:patient');

        Route::get('/auth/login', [LoginController::class, 'showLoginForm'])->name('login');

        // Add this temporarily to debug routes
        Route::get('/debug-routes', function () {
            $routes = Route::getRoutes()->getRoutesByName();
            dd(isset($routes['logout']), $routes['logout'] ?? 'Logout route not found');
        });

        // Make route accessible without auth middleware initially
        Route::get('/complete-registration', [PatientController::class, 'completeRegistration'])
            ->name('profile.complete');

        Route::post('/store-profile', [PatientController::class, 'storeProfile'])
            ->name('profile.store')
            ->middleware('auth:patient');

        Auth::routes();
    });
});

// Move this route outside of any middleware groups
Route::get('/doctors/by-department/{id}', [DoctorController::class, 'getByDepartment'])
    ->name('doctors.by.department');

// Add the new route for getting unavailable dates for a doctor
Route::get('/doctors/{doctorId}/unavailable-dates', [DoctorController::class, 'getUnavailableDates'])
    ->name('doctors.unavailable-dates');
