<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
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
        Auth::logout();
        return redirect('/');
    }
}