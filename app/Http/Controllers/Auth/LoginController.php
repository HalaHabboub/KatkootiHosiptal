<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        // Remove all middleware restrictions
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        // Validate the form input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'user_type' => 'required|in:patient,doctor,admin',
        ]);

        // Get credentials and user type
        $credentials = $request->only('email', 'password');
        $guard = $request->input('user_type');

        if (Auth::guard($guard)->attempt($credentials)) {
            switch ($guard) {
                case 'patient':
                    return redirect()->route('patient');
                case 'doctor':
                    return redirect()->route('doctor.dashboard');
                case 'admin':
                    return redirect()->route('admin.dashboard');
            }
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function logout(Request $request)
    {
        // Logout from all guards
        Auth::logout();

        // Clear the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to patient view
        return redirect('/');
    }
}