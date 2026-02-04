<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\Request;

class HostDashboardController extends Controller
{
    // My code starts here

    /**
     *  Display the host's properties dashboard.
     */
    public function index()
    {
        $properties = Property::where('host_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('host.dashboard', compact('properties'));
    }

    /**
     * Show form to create a new property.
     */
    public function create()
    {
        return view('host.properties.form');
    }

    /**
     * Store a new property in the database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
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

        $validated['host_id'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active');

        $property = Property::create($validated);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('properties', 'public');
                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('host.dashboard')->with('success', 'Property created successfully!');
    }

    /**
     * Show form to edit an existing property.
     */
    public function edit(Property $property)
    {
        // Ensure the property belongs to the authenticated host
        if ($property->host_id !== auth()->id()) {
            abort(404);
        }

        return view('host.properties.form', compact('property'));
    }

    /**
     * Update an existing property.
     */
    public function update(Request $request, Property $property)
    {
        // Ensure the property belongs to the authenticated host
        if ($property->host_id !== auth()->id()) {
            abort(404);
        }

        $validated = $request->validate([
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

        return redirect()->route('host.dashboard')->with('success', 'Property updated successfully!');
    }

    /**
     * Delete a property.
     */
    public function destroy(Property $property)
    {
        
        if ($property->host_id !== auth()->id()) {
            abort(404);
        }

        $property->delete();

        return redirect()->route('host.dashboard')->with('success', 'Property deleted successfully!');
    }
}