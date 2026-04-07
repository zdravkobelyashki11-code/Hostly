<?php

use App\Models\Booking;
use App\Models\Property;
use App\Models\User;

it('allows a booking participant to view the booking messages page', function () {
    $host = User::factory()->host()->create();
    $guest = User::factory()->create();
    $property = Property::factory()->create(['host_id' => $host->id]);
    $booking = Booking::factory()->create([
        'property_id' => $property->id,
        'guest_id' => $guest->id,
        'host_id' => $host->id,
    ]);

    $response = $this->actingAs($guest)->get(route('bookings.messages.index', $booking));

    $response->assertOk();
    $response->assertSee('Messages');
    $response->assertSee($property->title);
});

it('allows a booking participant to send a message', function () {
    $host = User::factory()->host()->create();
    $guest = User::factory()->create();
    $property = Property::factory()->create(['host_id' => $host->id]);
    $booking = Booking::factory()->create([
        'property_id' => $property->id,
        'guest_id' => $guest->id,
        'host_id' => $host->id,
    ]);

    $response = $this->actingAs($guest)->post(route('bookings.messages.store', $booking), [
        'body' => 'Hello, I would like to confirm the check-in time.',
    ]);

    $response->assertRedirect(route('bookings.messages.index', $booking));

    $this->assertDatabaseHas('messages', [
        'booking_id' => $booking->id,
        'sender_id' => $guest->id,
        'body' => 'Hello, I would like to confirm the check-in time.',
    ]);
});

it('prevents unrelated users from viewing a booking conversation', function () {
    $host = User::factory()->host()->create();
    $guest = User::factory()->create();
    $otherUser = User::factory()->create();
    $property = Property::factory()->create(['host_id' => $host->id]);
    $booking = Booking::factory()->create([
        'property_id' => $property->id,
        'guest_id' => $guest->id,
        'host_id' => $host->id,
    ]);

    $response = $this->actingAs($otherUser)->get(route('bookings.messages.index', $booking));

    $response->assertNotFound();
});

it('requires a message body when sending a message', function () {
    $host = User::factory()->host()->create();
    $guest = User::factory()->create();
    $property = Property::factory()->create(['host_id' => $host->id]);
    $booking = Booking::factory()->create([
        'property_id' => $property->id,
        'guest_id' => $guest->id,
        'host_id' => $host->id,
    ]);

    $response = $this->actingAs($guest)->from(route('bookings.messages.index', $booking))
        ->post(route('bookings.messages.store', $booking), [
            'body' => '',
        ]);

    $response->assertRedirect(route('bookings.messages.index', $booking));
    $response->assertSessionHasErrors(['body']);
});
