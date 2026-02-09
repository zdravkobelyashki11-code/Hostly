<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;
use App\Models\PropertyImage;
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

    // USER CRUD 

    /**
     * Show form to create a new user.
     */
    public function createUser()
    {
        $roles = \App\Models\Role::all();
        return view('admin.users.form', compact('roles'));
    }

    /**
     * Store a new user.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        User::create($validated);

        return redirect()->route('admin.dashboard')->with('success', 'User created successfully!');
    }

    /**
     * Show form to edit a user.
     */
    public function editUser(User $user)
    {
        $roles = \App\Models\Role::all();
        return view('admin.users.form', compact('user', 'roles'));
    }

    /**
     * Update a user.
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.dashboard')->with('success', 'User updated successfully!');
    }

    /**
     * Delete a user.
     */
    public function destroyUser(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.dashboard')->with('error', 'You cannot delete yourself!');
        }

        $user->delete();

        return redirect()->route('admin.dashboard')->with('success', 'User deleted successfully!');
    }

    // PROPERTY CRUD 

    /**
     * Show form to create a new property.
     */
    public function createProperty()
    {
        $hosts = User::whereHas('role', function ($query) {
            $query->where('name', 'Host');
        })->get();

        return view('admin.properties.form', compact('hosts'));
    }

    /**
     * Store a new property.
     */
    public function storeProperty(Request $request)
    {
        $validated = $request->validate([
            'host_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'max_guests' => 'required|integer|min:1',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'images.*' => 'image|max:5120',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $property = Property::create($validated);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('properties', 'public');
                \App\Models\PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.dashboard')->with('success', 'Property created successfully!');
    }

    /**
     * Show form to edit a property.
     */
    public function editProperty(Property $property)
    {
        $hosts = User::whereHas('role', function ($query) {
            $query->where('name', 'Host');
        })->get();

        return view('admin.properties.form', compact('property', 'hosts'));
    }

    /**
     * Update a property.
     */
    public function updateProperty(Request $request, Property $property)
    {
        $validated = $request->validate([
            'host_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'max_guests' => 'required|integer|min:1',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'images.*' => 'image|max:5120',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $property->update($validated);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $existingCount = $property->images()->count();
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('properties', 'public');
                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => $path,
                    'is_primary' => $existingCount === 0 && $index === 0,
                    'sort_order' => $existingCount + $index,
                ]);
            }
        }

        return redirect()->route('admin.dashboard')->with('success', 'Property updated successfully!');
    }

    /**
     * Delete a property.
     */
    public function destroyProperty(Property $property)
    {
        $property->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Property deleted successfully!');
    }
}
