<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        // Build safe admin default without throwing RouteNotFoundException
        $adminDefault = RouteFacade::has('admin.home')
            ? route('admin.home', [], false)
            : (RouteFacade::has('admin.index')
                ? route('admin.index', [], false)
                : url('/admin'));

        // If user is admin -> admin dashboard, else -> user dashboard
        $isAdmin = false;
        if ($user) {
            // support both `role === 'admin'` and boolean `is_admin`
            $isAdmin = ($user->role ?? null) === 'admin' || (property_exists($user, 'is_admin') && (bool) $user->is_admin);
        }

        $default = $isAdmin
            ? $adminDefault
            : route('dashboard', [], false);

        // honor intended URL if present
        return redirect()->intended($default);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        auth()->guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
