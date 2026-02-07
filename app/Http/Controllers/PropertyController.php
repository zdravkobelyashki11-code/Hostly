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

    public function search()
    {
        $query = Property::with(['primaryImage'])
            ->where('is_active', true);

        // Text search on title and description
        if (request('q')) {
            $searchTerm = request('q');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Location filters
        if (request('city')) {
            $query->where('city', request('city'));
        }
        if (request('country')) {
            $query->where('country', request('country'));
        }

        // Price range filters
        if (request('min_price')) {
            $query->where('price_per_night', '>=', request('min_price'));
        }
        if (request('max_price')) {
            $query->where('price_per_night', '<=', request('max_price'));
        }

        // Property spec filters
        if (request('bedrooms')) {
            $query->where('bedrooms', '>=', request('bedrooms'));
        }
        if (request('bathrooms')) {
            $query->where('bathrooms', '>=', request('bathrooms'));
        }
        if (request('guests')) {
            $query->where('max_guests', '>=', request('guests'));
        }

        $properties = $query->latest()->paginate(12)->withQueryString();

        // Get unique cities and countries for filter dropdowns
        $cities = Property::where('is_active', true)->distinct()->pluck('city')->sort();
        $countries = Property::where('is_active', true)->distinct()->pluck('country')->sort();

        return view('properties.search', compact('properties', 'cities', 'countries'));
    }
}