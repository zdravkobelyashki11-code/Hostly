<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Show admin login form.
     */
    public function showLogin()
    {
        // Redirect to dashboard if already logged in as admin
        if (auth()->check() && auth()->user()->role && auth()->user()->role->name === 'Admin') {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.login');
    }

    /**
     * Handle admin login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            // Verify user is an admin
            if (auth()->user()->role && auth()->user()->role->name === 'Admin') {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Admin!');
            }
            
            // Not an admin - logout and show error
            Auth::logout();
            return back()->withErrors([
                'email' => 'This account does not have admin privileges.',
            ]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Admin dashboard with users and properties.
     */
    public function dashboard()
    {
        // Get all users with their roles
        $users = User::with('role')->orderBy('created_at', 'desc')->get();
        
        // Get all hosts with their properties
        $hosts = User::whereHas('role', function ($query) {
            $query->where('name', 'Host');
        })
        ->with(['properties' => function ($query) {
            $query->with('primaryImage')->orderBy('created_at', 'desc');
        }])
        ->get();

        return view('admin.dashboard', compact('users', 'hosts'));
    }

    /**
     * Admin logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/admin')->with('success', 'Logged out successfully.');
    }
}
