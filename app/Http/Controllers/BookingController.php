<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Property;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function store(Request $request, Property $property)
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);

        // Check for overlapping bookings
        $overlap = Booking::where('property_id', $property->id)
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->exists();

        if ($overlap) {
            return back()->withErrors(['dates' => 'This property is already booked for the selected dates.'])->withInput();
        }

        // Calculate total price
        $nights = $checkIn->diffInDays($checkOut);
        $totalPrice = $nights * $property->price_per_night;

        Booking::create([
            'property_id' => $property->id,
            'guest_id' => auth()->id(),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'total_price' => $totalPrice,
            'status' => 'confirmed',
        ]);

        return back()->with('success', "Booking confirmed! {$nights} night(s) for $" . number_format($totalPrice, 2));
    }
}
