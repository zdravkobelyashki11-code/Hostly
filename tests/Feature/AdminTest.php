<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Property;

/*
|--------------------------------------------------------------------------
| Admin Panel Tests
|--------------------------------------------------------------------------
|
| Tests for AdminController: login, middleware, user CRUD, property CRUD.
|
*/

// ── Admin Login ──────────────────────────────────────────────────────

it('allows an admin to login via the admin login page', function () {
    $admin = User::factory()->admin()->create(['password' => bcrypt('secret123')]);

    $response = $this->post('/admin/login', [
        'email' => $admin->email,
        'password' => 'secret123',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($admin);
});

it('rejects a non-admin user from admin login', function () {
    $customer = User::factory()->create(['password' => bcrypt('secret123')]);

    $response = $this->post('/admin/login', [
        'email' => $customer->email,
        'password' => 'secret123',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('rejects wrong credentials on admin login', function () {
    $admin = User::factory()->admin()->create(['password' => bcrypt('secret123')]);

    $response = $this->post('/admin/login', [
        'email' => $admin->email,
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

// ── Admin Middleware ─────────────────────────────────────────────────

it('allows an admin to access the admin dashboard', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertStatus(200);
});

it('redirects a non-admin user from the admin dashboard', function () {
    $customer = User::factory()->create();

    $response = $this->actingAs($customer)->get('/admin/dashboard');

    $response->assertRedirect('/admin');
});

it('redirects a guest from the admin dashboard', function () {
    $response = $this->get('/admin/dashboard');

    $response->assertRedirect('/login');
});

// ── Admin Logout ─────────────────────────────────────────────────────

it('allows an admin to logout', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post('/admin/logout');

    $response->assertRedirect('/admin');
    $this->assertGuest();
});

// ── User CRUD ────────────────────────────────────────────────────────

it('allows an admin to create a user', function () {
    $admin = User::factory()->admin()->create();
    $role = Role::firstOrCreate(['name' => 'Customer'], ['description' => 'A customer']);

    $response = $this->actingAs($admin)->post('/admin/users', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'role_id' => $role->id,
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
});

it('allows an admin to update a user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create(['name' => 'Old Name']);

    $response = $this->actingAs($admin)->put("/admin/users/{$user->id}", [
        'name' => 'New Name',
        'email' => $user->email,
        'role_id' => $user->role_id,
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name']);
});

it('allows an admin to delete a user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($admin)->delete("/admin/users/{$user->id}");

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

it('prevents an admin from deleting themselves', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");

    $response->assertRedirect(route('admin.dashboard'));
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

// ── Property CRUD (admin) ────────────────────────────────────────────

it('allows an admin to create a property', function () {
    $admin = User::factory()->admin()->create();
    $host = User::factory()->host()->create();

    $response = $this->actingAs($admin)->post('/admin/properties', [
        'host_id' => $host->id,
        'title' => 'Admin Created Listing',
        'description' => 'A listing created by an admin.',
        'price_per_night' => 200.00,
        'location' => '456 Admin Rd',
        'city' => 'Plovdiv',
        'country' => 'Bulgaria',
        'max_guests' => 6,
        'bedrooms' => 3,
        'bathrooms' => 2,
        'is_active' => true,
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertDatabaseHas('properties', ['title' => 'Admin Created Listing']);
});

it('allows an admin to delete a property', function () {
    $admin = User::factory()->admin()->create();
    $property = Property::factory()->create();

    $response = $this->actingAs($admin)->delete("/admin/properties/{$property->id}");

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertDatabaseMissing('properties', ['id' => $property->id]);
});
