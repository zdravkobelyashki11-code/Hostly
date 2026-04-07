<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Booking $booking): View
    {
        $this->ensureBookingParticipant($booking);

        $booking->load([
            'property',
            'guest',
            'host',
            'messages.sender',
        ]);

        return view('messages.index', [
            'booking' => $booking,
            'otherUser' => $booking->guest_id === auth()->id()
                ? $booking->host
                : $booking->guest,
        ]);
    }

    public function store(Request $request, Booking $booking): RedirectResponse
    {
        $this->ensureBookingParticipant($booking);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $booking->messages()->create([
            'sender_id' => auth()->id(),
            'body' => $validated['body'],
        ]);

        return redirect()
            ->route('bookings.messages.index', $booking)
            ->with('success', 'Message sent.');
    }

    private function ensureBookingParticipant(Booking $booking): void
    {
        if (! $booking->hasParticipant(auth()->user())) {
            abort(404);
        }
    }
}
