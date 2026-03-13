<?php

use App\Models\User;
use App\Models\Property;
use App\Models\Booking;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Booking Feature Tests
|--------------------------------------------------------------------------
|
| Tests for creating bookings and validating dates and overlaps.
|
*/

it('allows a user to successfully book a property', function () {
    $guest = User::factory()->create();
    $property = Property::factory()->create(['price_per_night' => 100]);

    $checkIn = Carbon::tomorrow();
    $checkOut = Carbon::tomorrow()->addDays(3);

    $response = $this->actingAs($guest)->post("/properties/{$property->id}/book", [
        'check_in' => $checkIn->format('Y-m-d'),
        'check_out' => $checkOut->format('Y-m-d'),
    ]);

    $response->assertSessionHas('success');
    
    $this->assertDatabaseHas('bookings', [
        'property_id' => $property->id,
        'guest_id' => $guest->id,
        'check_in' => $checkIn->startOfDay()->toDateTimeString(),
        'check_out' => $checkOut->startOfDay()->toDateTimeString(),
    ]);
});

it('requires dates to book a property', function () {
    $guest = User::factory()->create();
    $property = Property::factory()->create();

    $response = $this->actingAs($guest)->post("/properties/{$property->id}/book", []);

    $response->assertSessionHasErrors(['check_in', 'check_out']);
});

it('does not allow booking in the past', function () {
    $guest = User::factory()->create();
    $property = Property::factory()->create();

    $checkIn = Carbon::yesterday();
    $checkOut = Carbon::tomorrow();

    $response = $this->actingAs($guest)->post("/properties/{$property->id}/book", [
        'check_in' => $checkIn->format('Y-m-d'),
        'check_out' => $checkOut->format('Y-m-d'),
    ]);

    $response->assertSessionHasErrors(['check_in']);
});

it('requires check-out to be after check-in', function () {
    $guest = User::factory()->create();
    $property = Property::factory()->create();

    $checkIn = Carbon::tomorrow()->addDays(2);
    $checkOut = Carbon::tomorrow();

    $response = $this->actingAs($guest)->post("/properties/{$property->id}/book", [
        'check_in' => $checkIn->format('Y-m-d'),
        'check_out' => $checkOut->format('Y-m-d'),
    ]);

    $response->assertSessionHasErrors(['check_out']);
});

it('prevents double bookings on exact same dates', function () {
    $guest1 = User::factory()->create();
    $guest2 = User::factory()->create();
    $property = Property::factory()->create();

    $checkIn = Carbon::tomorrow();
    $checkOut = Carbon::tomorrow()->addDays(3);

    // First booking
    Booking::factory()->create([
        'property_id' => $property->id,
        'guest_id' => $guest1->id,
        'check_in' => $checkIn,
        'check_out' => $checkOut,
    ]);

    // Second booking attempt on same dates
    $response = $this->actingAs($guest2)->post("/properties/{$property->id}/book", [
        'check_in' => $checkIn->format('Y-m-d'),
        'check_out' => $checkOut->format('Y-m-d'),
    ]);

    $response->assertSessionHasErrors(['dates' => 'This property is already booked for the selected dates.']);
});

it('prevents double bookings on partially overlapping dates', function () {
    $guest1 = User::factory()->create();
    $guest2 = User::factory()->create();
    $property = Property::factory()->create();

    // First booking: day 2 to day 5
    Booking::factory()->create([
        'property_id' => $property->id,
        'guest_id' => $guest1->id,
        'check_in' => Carbon::tomorrow()->addDays(2),
        'check_out' => Carbon::tomorrow()->addDays(5),
    ]);

    // Second booking attempt: day 4 to day 7 (overlaps on day 4)
    $response = $this->actingAs($guest2)->post("/properties/{$property->id}/book", [
        'check_in' => Carbon::tomorrow()->addDays(4)->format('Y-m-d'),
        'check_out' => Carbon::tomorrow()->addDays(7)->format('Y-m-d'),
    ]);

    $response->assertSessionHasErrors(['dates' => 'This property is already booked for the selected dates.']);
});

it('calculates the total price correctly based on nights', function () {
    $guest = User::factory()->create();
    $property = Property::factory()->create(['price_per_night' => 150]);

    // 4 nights stay
    $checkIn = Carbon::tomorrow();
    $checkOut = Carbon::tomorrow()->addDays(4);

    $this->actingAs($guest)->post("/properties/{$property->id}/book", [
        'check_in' => $checkIn->format('Y-m-d'),
        'check_out' => $checkOut->format('Y-m-d'),
    ]);

    $this->assertDatabaseHas('bookings', [
        'property_id' => $property->id,
        'guest_id' => $guest->id,
        'total_price' => 600, // 4 nights * 150
    ]);
});

it('sets the booking status to pending by default', function () {
    $guest = User::factory()->create();
    $property = Property::factory()->create();

    $checkIn = Carbon::tomorrow();
    $checkOut = Carbon::tomorrow()->addDays(2);

    $this->actingAs($guest)->post("/properties/{$property->id}/book", [
        'check_in' => $checkIn->format('Y-m-d'),
        'check_out' => $checkOut->format('Y-m-d'),
    ]);

    $this->assertDatabaseHas('bookings', [
        'property_id' => $property->id,
        'guest_id' => $guest->id,
        'status' => 'pending',
    ]);
});
