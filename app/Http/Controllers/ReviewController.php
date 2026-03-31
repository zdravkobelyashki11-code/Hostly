<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PropertyReview;
use App\Models\UserReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function storeGuestReview(Request $request, Booking $booking)
    {
        if (Auth::id() !== $booking->guest_id) {
            abort(403, 'Unauthorized action.');
        }

        $this->ensureCanReview($booking);

        if ($booking->propertyReviewByGuest()->exists() || $booking->hostReviewByGuest()->exists()) {
            throw ValidationException::withMessages(['review' => 'You have already reviewed this booking.']);
        }

        $validated = $request->validate([
            'property_accuracy' => 'required|integer|min:1|max:5',
            'property_cleanliness' => 'required|integer|min:1|max:5',
            'property_location' => 'required|integer|min:1|max:5',
            'property_value' => 'required|integer|min:1|max:5',
            'property_comment' => 'nullable|string|max:1000',
            
            'host_communication' => 'required|integer|min:1|max:5',
            'host_checkin' => 'required|integer|min:1|max:5',
            'host_helpfulness' => 'required|integer|min:1|max:5',
            'host_comment' => 'nullable|string|max:1000',
        ]);

        $propertyRating = (int) round(collect([
            $validated['property_accuracy'],
            $validated['property_cleanliness'],
            $validated['property_location'],
            $validated['property_value'],
        ])->average());

        PropertyReview::create([
            'booking_id' => $booking->id,
            'reviewer_id' => Auth::id(),
            'rating' => $propertyRating,
            'sub_ratings' => [
                'accuracy' => (int) $validated['property_accuracy'],
                'cleanliness' => (int) $validated['property_cleanliness'],
                'location' => (int) $validated['property_location'],
                'value' => (int) $validated['property_value'],
            ],
            'comment' => $validated['property_comment'],
        ]);

        $hostRating = (int) round(collect([
            $validated['host_communication'],
            $validated['host_checkin'],
            $validated['host_helpfulness'],
        ])->average());

        UserReview::create([
            'booking_id' => $booking->id,
            'reviewer_id' => Auth::id(),
            'rating' => $hostRating,
            'sub_ratings' => [
                'communication' => (int) $validated['host_communication'],
                'checkin' => (int) $validated['host_checkin'],
                'helpfulness' => (int) $validated['host_helpfulness'],
            ],
            'comment' => $validated['host_comment'],
        ]);

        return back()->with('success', 'Thank you for your comprehensive review!');
    }

    public function storeHostReview(Request $request, Booking $booking)
    {
        if (Auth::id() !== $booking->host_id) {
            abort(403, 'Unauthorized action.');
        }

        $this->ensureCanReview($booking);

        if ($booking->reviewByHost()->exists()) {
            throw ValidationException::withMessages(['review' => 'You have already reviewed this guest.']);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        UserReview::create([
            'booking_id' => $booking->id,
            'reviewer_id' => Auth::id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Review submitted successfully!');
    }

    private function ensureCanReview(Booking $booking)
    {
        if ($booking->status !== 'confirmed') {
            throw ValidationException::withMessages(['review' => 'You can only review confirmed bookings.']);
        }

        if ($booking->check_out > now()) {
            throw ValidationException::withMessages(['review' => 'You can only review a booking after the check-out date.']);
        }
    }
}
