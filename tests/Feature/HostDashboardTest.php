<?php

use App\Models\User;
use App\Models\Property;

/*
|--------------------------------------------------------------------------
| Host Dashboard Tests
|--------------------------------------------------------------------------
|
| Tests for HostDashboardController + HostMiddleware authorization.
|
*/

// ── Authorization ────────────────────────────────────────────────────

it('allows a host to access the dashboard', function () {
    $host = User::factory()->host()->create();

    $response = $this->actingAs($host)->get('/host/dashboard');

    $response->assertStatus(200);
});

it('redirects a customer away from the host dashboard', function () {
    $customer = User::factory()->create(); // defaults to Customer role

    $response = $this->actingAs($customer)->get('/host/dashboard');

    $response->assertRedirect('/');
});

it('redirects a guest away from the host dashboard', function () {
    $response = $this->get('/host/dashboard');

    $response->assertRedirect('/login');
});

// ── Create Property ──────────────────────────────────────────────────

it('allows a host to create a property', function () {
    $host = User::factory()->host()->create();

    $response = $this->actingAs($host)->post('/host/properties', [
        'title' => 'My New Listing',
        'description' => 'A beautiful place to stay.',
        'price_per_night' => 120.00,
        'location' => '123 Main St',
        'city' => 'Sofia',
        'country' => 'Bulgaria',
        'max_guests' => 4,
        'bedrooms' => 2,
        'bathrooms' => 1,
        'is_active' => true,
    ]);

    $response->assertRedirect(route('host.dashboard'));
    $this->assertDatabaseHas('properties', [
        'title' => 'My New Listing',
        'host_id' => $host->id,
    ]);
});

it('validates required fields when creating a property', function () {
    $host = User::factory()->host()->create();

    $response = $this->actingAs($host)->post('/host/properties', []);

    $response->assertSessionHasErrors(['title', 'description', 'price_per_night', 'location', 'city', 'country', 'max_guests', 'bedrooms', 'bathrooms']);
});

// ── Update Property ──────────────────────────────────────────────────

it('allows a host to update their own property', function () {
    $host = User::factory()->host()->create();
    $property = Property::factory()->create(['host_id' => $host->id, 'title' => 'Old Title']);

    $response = $this->actingAs($host)->put("/host/properties/{$property->id}", [
        'title' => 'Updated Title',
        'description' => $property->description,
        'price_per_night' => $property->price_per_night,
        'location' => $property->location,
        'city' => $property->city,
        'country' => $property->country,
        'max_guests' => $property->max_guests,
        'bedrooms' => $property->bedrooms,
        'bathrooms' => $property->bathrooms,
        'is_active' => true,
    ]);

    $response->assertRedirect(route('host.dashboard'));
    $this->assertDatabaseHas('properties', ['id' => $property->id, 'title' => 'Updated Title']);
});

it('prevents a host from editing another hosts property', function () {
    $host = User::factory()->host()->create();
    $otherHost = User::factory()->host()->create();
    $property = Property::factory()->create(['host_id' => $otherHost->id]);

    $response = $this->actingAs($host)->get("/host/properties/{$property->id}/edit");

    $response->assertStatus(404);
});

// ── Delete Property ──────────────────────────────────────────────────

it('allows a host to delete their own property', function () {
    $host = User::factory()->host()->create();
    $property = Property::factory()->create(['host_id' => $host->id]);

    $response = $this->actingAs($host)->delete("/host/properties/{$property->id}");

    $response->assertRedirect(route('host.dashboard'));
    $this->assertDatabaseMissing('properties', ['id' => $property->id]);
});

it('prevents a host from deleting another hosts property', function () {
    $host = User::factory()->host()->create();
    $otherHost = User::factory()->host()->create();
    $property = Property::factory()->create(['host_id' => $otherHost->id]);

    $response = $this->actingAs($host)->delete("/host/properties/{$property->id}");

    $response->assertStatus(404);
    $this->assertDatabaseHas('properties', ['id' => $property->id]);
});
