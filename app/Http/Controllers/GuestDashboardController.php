<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GuestDashboardController extends Controller
{
    /**
     * Display the guest's bookings dashboard.
     */
    public function index()
    {
        $bookings = Booking::where('guest_id', auth()->id())
            ->with('property')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guest.dashboard', compact('bookings'));
    }

    /**
     * Show form to edit booking dates.
     */
    public function edit(Booking $booking)
    {
        if ($booking->guest_id !== auth()->id()) {
            abort(404);
        }

        return view('guest.bookings.edit', compact('booking'));
    }

    /**
     * Update booking dates.
     */
    public function update(Request $request, Booking $booking)
    {
        if ($booking->guest_id !== auth()->id()) {
            abort(404);
        }

        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);

        // Check for overlapping bookings (excluding this current booking)
        $overlap = Booking::where('property_id', $booking->property_id)
            ->where('id', '!=', $booking->id)
            ->where('status', '!=', Booking::STATUS_REJECTED)
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->exists();

        if ($overlap) {
            return back()->withErrors(['dates' => 'This property is already booked for the selected dates.'])->withInput();
        }

        $nights = $checkIn->diffInDays($checkOut);
        $totalPrice = $nights * $booking->property->price_per_night;

        $booking->update([
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'total_price' => $totalPrice,
            'status' => Booking::STATUS_PENDING,
        ]);

        return redirect()->route('guest.dashboard')->with('success', 'Booking updated successfully and is pending host approval.');
    }
}
