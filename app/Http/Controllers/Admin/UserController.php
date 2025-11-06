<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $query = User::query();
        
        // Search functionality
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Role filter
        if (request('role')) {
            $query->where('role', request('role'));
        }
        
        $users = $query->withCount(['orders', 'services'])->latest('id')->paginate(20);
        
        // Stats for the page
        $stats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'users' => User::where('role', 'user')->orWhereNull('role')->count(),
        ];
        
        return view('admin.users.index', compact('users', 'stats'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'in:user,admin'],
        ]);

        $data['password'] = Hash::make($data['password']);
        
        $user = User::create($data);

        return redirect()->route('admin.users.show', $user)->with('ok', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['orders.plan', 'orders.service']);
        $user->loadCount(['orders', 'services']);
        
        // Get user's services with credentials
        $services = Service::whereHas('order', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['plan', 'order'])->latest('id')->get();
        
        return view('admin.users.show', compact('user', 'services'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone'    => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'in:user,admin'],
        ]);

        // Only update password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        $user->update($data);

        return redirect()->route('admin.users.show', $user)->with('ok', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === Auth::id()) {
            return back()->withErrors('You cannot delete your own account.');
        }
        
        // Prevent deleting other admins
        if ($user->isAdmin()) {
            return back()->withErrors('Cannot delete admin users.');
        }
        
        $user->delete();
        return back()->with('ok','User deleted successfully.');
    }
    
    /**
     * Impersonate a user (Login as user)
     */
    public function impersonate(User $user)
    {
        // Prevent impersonating another admin
        if ($user->isAdmin()) {
            return back()->withErrors('Cannot impersonate admin users.');
        }
        
        // Store the original admin user ID in session
        session(['impersonate_from' => Auth::id()]);
        
        // Login as the selected user
        Auth::login($user);
        
        return redirect()->route('dashboard')->with('success', "You are now logged in as {$user->name}");
    }
    
    /**
     * Stop impersonating and return to admin
     */
    public function stopImpersonating()
    {
        if (!session()->has('impersonate_from')) {
            return redirect()->route('admin.index');
        }
        
        $adminId = session('impersonate_from');
        session()->forget('impersonate_from');
        
        $admin = User::find($adminId);
        if ($admin) {
            Auth::login($admin);
        }
        
        return redirect()->route('admin.index')->with('success', 'Welcome back to admin panel');
    }
    
    /**
     * Show user credentials (for admin viewing)
     */
    public function credentials(User $user)
    {
        $services = Service::whereHas('order', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['plan', 'order'])->latest('id')->get();
        
        return view('admin.users.credentials', compact('user', 'services'));
    }
}
