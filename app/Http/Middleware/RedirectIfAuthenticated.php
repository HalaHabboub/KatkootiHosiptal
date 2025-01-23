<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Check if the user has completed their profile
                $user = Auth::guard($guard)->user();
                if (!$user->date_of_birth) {
                    return redirect()->route('profile.complete');
                }
                return redirect()->route('patient.dashboard');
            }
        }

        return $next($request);
    }
}
