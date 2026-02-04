<?php

namespace App\Http\Controllers;

use App\Models\Property;

// My code starts here
class PropertyController extends Controller
{
   
     // Display a listing of latest 10 active properties for the homepage.
     
    public function index()
    {
        $properties = Property::with(['primaryImage', 'images'])
            ->where('is_active', true)
            ->latest()
            ->take(10)
            ->get();

        return view('home', compact('properties'));
    }

        
     //Display a single property (auth required).
     
    public function show(Property $property)
    {
        $property->load(['images', 'host']);
        return view('properties.show', compact('property'));
    }
}