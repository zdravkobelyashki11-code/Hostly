<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Property;
use App\Models\PropertyReview;
use App\Models\User;
use App\Models\UserReview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_leave_review_for_past_confirmed_booking(): void
    {
        $host = User::factory()->host()->create();
        $guest = User::factory()->create();
        $property = Property::factory()->create(['host_id' => $host->id]);

        $booking = Booking::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'host_id' => $host->id,
            'status' => Booking::STATUS_CONFIRMED,
            'check_in' => now()->subDays(5),
            'check_out' => now()->subDays(1),
        ]);

        $response = $this->actingAs($guest)->post(route('guest.bookings.reviews.store', $booking), [
            'property_accuracy' => 5,
            'property_cleanliness' => 4,
            'property_location' => 5,
            'property_value' => 4,
            'property_comment' => 'Great place!',
            'host_communication' => 5,
            'host_checkin' => 5,
            'host_helpfulness' => 5,
            'host_comment' => 'Great host!',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('property_reviews', [
            'booking_id' => $booking->id,
            'reviewer_id' => $guest->id,
            'rating' => 5,
            'comment' => 'Great place!',
        ]);

        $this->assertDatabaseHas('user_reviews', [
            'booking_id' => $booking->id,
            'reviewer_id' => $guest->id,
            'rating' => 5,
            'comment' => 'Great host!',
        ]);
    }

    public function test_host_can_leave_review_for_past_confirmed_booking(): void
    {
        $host = User::factory()->host()->create();
        $guest = User::factory()->create();
        $property = Property::factory()->create(['host_id' => $host->id]);

        $booking = Booking::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'host_id' => $host->id,
            'status' => Booking::STATUS_CONFIRMED,
            'check_in' => now()->subDays(5),
            'check_out' => now()->subDays(1),
        ]);

        $response = $this->actingAs($host)->post(route('host.bookings.reviews.store', $booking), [
            'rating' => 4,
            'comment' => 'Good guest.',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('user_reviews', [
            'booking_id' => $booking->id,
            'reviewer_id' => $host->id,
            'rating' => 4,
            'comment' => 'Good guest.',
        ]);
    }

    public function test_guest_cannot_leave_duplicate_review(): void
    {
        $host = User::factory()->host()->create();
        $guest = User::factory()->create();
        $property = Property::factory()->create(['host_id' => $host->id]);

        $booking = Booking::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'host_id' => $host->id,
            'status' => Booking::STATUS_CONFIRMED,
            'check_in' => now()->subDays(5),
            'check_out' => now()->subDays(1),
        ]);

        $payload = [
            'property_accuracy' => 5,
            'property_cleanliness' => 5,
            'property_location' => 5,
            'property_value' => 5,
            'property_comment' => 'Hi',
            'host_communication' => 5,
            'host_checkin' => 5,
            'host_helpfulness' => 5,
            'host_comment' => 'Hi',
        ];

        $this->actingAs($guest)->post(route('guest.bookings.reviews.store', $booking), $payload);
        $response = $this->actingAs($guest)->post(route('guest.bookings.reviews.store', $booking), $payload);

        $response->assertSessionHasErrors('review');
        $this->assertSame(1, PropertyReview::count());
        $this->assertSame(1, UserReview::count());
    }

    public function test_cannot_review_unconfirmed_or_future_booking(): void
    {
        $host = User::factory()->host()->create();
        $guest = User::factory()->create();
        $property = Property::factory()->create(['host_id' => $host->id]);

        $payload = [
            'property_accuracy' => 5,
            'property_cleanliness' => 5,
            'property_location' => 5,
            'property_value' => 5,
            'property_comment' => 'Hi',
            'host_communication' => 5,
            'host_checkin' => 5,
            'host_helpfulness' => 5,
            'host_comment' => 'Hi',
        ];

        $futureBooking = Booking::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'host_id' => $host->id,
            'status' => Booking::STATUS_CONFIRMED,
            'check_in' => now()->subDays(1),
            'check_out' => now()->addDays(2),
        ]);

        $futureResponse = $this->actingAs($guest)->post(route('guest.bookings.reviews.store', $futureBooking), $payload);
        $futureResponse->assertSessionHasErrors('review');

        $pendingBooking = Booking::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'host_id' => $host->id,
            'status' => Booking::STATUS_PENDING,
            'check_in' => now()->subDays(5),
            'check_out' => now()->subDays(1),
        ]);

        $pendingResponse = $this->actingAs($guest)->post(route('guest.bookings.reviews.store', $pendingBooking), $payload);
        $pendingResponse->assertSessionHasErrors('review');
    }
}
