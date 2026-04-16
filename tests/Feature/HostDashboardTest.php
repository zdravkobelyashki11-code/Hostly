<?php

use App\Models\User;
use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Support\Facades\Storage;

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
        'street_address' => '123 Main St',
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

    $response->assertSessionHasErrors(['title', 'description', 'price_per_night', 'street_address', 'city', 'country', 'max_guests', 'bedrooms', 'bathrooms']);
});

// ── Update Property ──────────────────────────────────────────────────

it('allows a host to update their own property', function () {
    $host = User::factory()->host()->create();
    $property = Property::factory()->create(['host_id' => $host->id, 'title' => 'Old Title']);

    $response = $this->actingAs($host)->put("/host/properties/{$property->id}", [
        'title' => 'Updated Title',
        'description' => $property->description,
        'price_per_night' => $property->price_per_night,
        'street_address' => $property->street_address,
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
    $this->assertSoftDeleted('properties', ['id' => $property->id]);
});

it('prevents a host from deleting another hosts property', function () {
    $host = User::factory()->host()->create();
    $otherHost = User::factory()->host()->create();
    $property = Property::factory()->create(['host_id' => $otherHost->id]);

    $response = $this->actingAs($host)->delete("/host/properties/{$property->id}");

    $response->assertStatus(404);
    $this->assertDatabaseHas('properties', ['id' => $property->id]);
});

it('allows a host to delete an image from their own property', function () {
    Storage::fake('public');

    $host = User::factory()->host()->create();
    $property = Property::factory()->create(['host_id' => $host->id]);

    Storage::disk('public')->put('properties/primary.jpg', 'primary');
    Storage::disk('public')->put('properties/secondary.jpg', 'secondary');

    $primaryImage = PropertyImage::create([
        'property_id' => $property->id,
        'image_path' => 'properties/primary.jpg',
        'is_primary' => true,
        'sort_order' => 0,
    ]);

    $secondaryImage = PropertyImage::create([
        'property_id' => $property->id,
        'image_path' => 'properties/secondary.jpg',
        'is_primary' => false,
        'sort_order' => 1,
    ]);

    $response = $this->actingAs($host)->delete(route('host.properties.images.destroy', [
        'property' => $property,
        'image' => $primaryImage,
    ]));

    $response->assertRedirect(route('host.properties.edit', $property));
    $this->assertDatabaseMissing('property_images', ['id' => $primaryImage->id]);
    $this->assertDatabaseHas('property_images', ['id' => $secondaryImage->id, 'is_primary' => true]);
    Storage::disk('public')->assertMissing('properties/primary.jpg');
});

it('prevents a host from deleting another hosts property image', function () {
    Storage::fake('public');

    $host = User::factory()->host()->create();
    $otherHost = User::factory()->host()->create();
    $property = Property::factory()->create(['host_id' => $otherHost->id]);

    Storage::disk('public')->put('properties/foreign.jpg', 'foreign');

    $image = PropertyImage::create([
        'property_id' => $property->id,
        'image_path' => 'properties/foreign.jpg',
        'is_primary' => true,
        'sort_order' => 0,
    ]);

    $response = $this->actingAs($host)->delete(route('host.properties.images.destroy', [
        'property' => $property,
        'image' => $image,
    ]));

    $response->assertStatus(404);
    $this->assertDatabaseHas('property_images', ['id' => $image->id]);
    Storage::disk('public')->assertExists('properties/foreign.jpg');
});
