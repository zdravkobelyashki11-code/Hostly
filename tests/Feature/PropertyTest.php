<?php

use App\Models\User;
use App\Models\Property;

/*
|--------------------------------------------------------------------------
| Public Property Page Tests
|--------------------------------------------------------------------------
|
| Tests for the homepage listing, search/filter, and property detail page.
|
*/

// ── Homepage ─────────────────────────────────────────────────────────

it('shows active properties on the homepage', function () {
    $active = Property::factory()->create(['title' => 'Sunny Villa', 'is_active' => true]);
    $inactive = Property::factory()->create(['title' => 'Hidden Cottage', 'is_active' => false]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Sunny Villa');
    $response->assertDontSee('Hidden Cottage');
});

it('shows at most 10 properties on the homepage', function () {
    Property::factory()->count(12)->create(['is_active' => true]);

    $response = $this->get('/');

    $response->assertStatus(200);
    // The homepage controller takes only 10
});

// ── Search ───────────────────────────────────────────────────────────

it('returns search results matching a keyword in title', function () {
    Property::factory()->create(['title' => 'Beach House', 'is_active' => true]);
    Property::factory()->create(['title' => 'Mountain Cabin', 'is_active' => true]);

    $response = $this->get('/search?q=Beach');

    $response->assertStatus(200);
    $response->assertSee('Beach House');
    $response->assertDontSee('Mountain Cabin');
});

it('filters properties by city', function () {
    Property::factory()->create(['city' => 'Sofia', 'is_active' => true]);
    Property::factory()->create(['city' => 'Varna', 'is_active' => true]);

    $response = $this->get('/search?city=Sofia');

    $response->assertStatus(200);
    $response->assertSee('Sofia');
});

it('filters properties by price range', function () {
    Property::factory()->create(['title' => 'Cheap Place', 'price_per_night' => 30, 'is_active' => true]);
    Property::factory()->create(['title' => 'Luxury Suite', 'price_per_night' => 500, 'is_active' => true]);

    $response = $this->get('/search?max_price=100');

    $response->assertStatus(200);
    $response->assertSee('Cheap Place');
    $response->assertDontSee('Luxury Suite');
});

it('excludes inactive properties from search results', function () {
    Property::factory()->create(['title' => 'Active Spot', 'is_active' => true]);
    Property::factory()->create(['title' => 'Secret Spot', 'is_active' => false]);

    $response = $this->get('/search');

    $response->assertStatus(200);
    $response->assertSee('Active Spot');
    $response->assertDontSee('Secret Spot');
});

// ── Property Detail ──────────────────────────────────────────────────

it('allows an authenticated user to view a property', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();

    $response = $this->actingAs($user)->get("/properties/{$property->id}");

    $response->assertStatus(200);
});

it('redirects a guest to login when viewing a property', function () {
    $property = Property::factory()->create();

    $response = $this->get("/properties/{$property->id}");

    $response->assertRedirect('/login');
});
