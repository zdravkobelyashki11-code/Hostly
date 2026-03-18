<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Property;
use App\Models\User;
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
        
        // Assert Property Review Created
        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'review_type' => 'property',
            'reviewer_id' => $guest->id,
            'reviewee_id' => $host->id,
            'property_id' => $property->id,
            'rating' => 5,
        ]);

        
        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'review_type' => 'user',
            'reviewer_id' => $guest->id,
            'reviewee_id' => $host->id,
            'property_id' => null,
            'rating' => 5,
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
            'status' => Booking::STATUS_CONFIRMED,
            'check_in' => now()->subDays(5),
            'check_out' => now()->subDays(1),
        ]);

        $response = $this->actingAs($host)->post(route('host.bookings.reviews.store', $booking), [
            'rating' => 4,
            'comment' => 'Good guest.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'review_type' => 'user',
            'reviewer_id' => $host->id,
            'reviewee_id' => $guest->id,
            'property_id' => null,
            'rating' => 4,
        ]);
    }

    public function test_guest_cannot_leave_duplicate_review(): void
    {
        $guest = User::factory()->create();
        $property = Property::factory()->create();
        $booking = Booking::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'status' => Booking::STATUS_CONFIRMED,
            'check_in' => now()->subDays(5),
            'check_out' => now()->subDays(1),
        ]);

        $payload = [
            'property_accuracy' => 5, 'property_cleanliness' => 5, 'property_location' => 5, 'property_value' => 5, 'property_comment' => 'Hi',
            'host_communication' => 5, 'host_checkin' => 5, 'host_helpfulness' => 5, 'host_comment' => 'Hi'
        ];

        // First review
        $this->actingAs($guest)->post(route('guest.bookings.reviews.store', $booking), $payload);

        // Second review
        $response = $this->actingAs($guest)->post(route('guest.bookings.reviews.store', $booking), $payload);

        $response->assertSessionHasErrors('review');
        $this->assertEquals(2, \App\Models\Review::count()); // 1 property + 1 host review from first submission
    }

    public function test_cannot_review_unconfirmed_or_future_booking(): void
    {
        $guest = User::factory()->create();
        $property = Property::factory()->create();
        
        $payload = [
            'property_accuracy' => 5, 'property_cleanliness' => 5, 'property_location' => 5, 'property_value' => 5, 'property_comment' => 'Hi',
            'host_communication' => 5, 'host_checkin' => 5, 'host_helpfulness' => 5, 'host_comment' => 'Hi'
        ];

        // Future checkout
        $booking1 = Booking::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'status' => Booking::STATUS_CONFIRMED,
            'check_in' => now()->subDays(1),
            'check_out' => now()->addDays(2),
        ]);

        $response1 = $this->actingAs($guest)->post(route('guest.bookings.reviews.store', $booking1), $payload);
        $response1->assertSessionHasErrors('review');

        // Unconfirmed
        $booking2 = Booking::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'status' => Booking::STATUS_PENDING,
            'check_in' => now()->subDays(5),
            'check_out' => now()->subDays(1),
        ]);

        $response2 = $this->actingAs($guest)->post(route('guest.bookings.reviews.store', $booking2), $payload);
        $response2->assertSessionHasErrors('review');
    }
}
