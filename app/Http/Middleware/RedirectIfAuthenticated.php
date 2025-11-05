<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * If the user is already authenticated and hits a "guest" route (login/register),
     * send them to the right home based on their role.
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Default target by role
                $target = ($user && $user->role === 'admin')
                    ? route('admin.home', absolute: false)   // /admin
                    : route('dashboard', absolute: false);    // /dashboard

                // If client expects JSON (SPA/AJAX), respond with 409 + redirect location
                if ($request->expectsJson()) {
                    return response('', 409)->header('X-Redirect-To', $target);
                }

                return redirect($target);
            }
        }

        return $next($request);
    }
}
