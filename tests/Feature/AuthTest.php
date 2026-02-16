<?php

use App\Models\User;
use App\Models\Role;

/*
|--------------------------------------------------------------------------
| Authentication Tests
|--------------------------------------------------------------------------
|
| Tests for user registration, login, and logout via AuthController.
|
*/

// ── Registration ─────────────────────────────────────────────────────

it('allows a user to register with valid data', function () {
    $role = Role::firstOrCreate(['name' => 'Customer'], ['description' => 'A customer']);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role_id' => $role->id,
    ]);

    $response->assertRedirect('/');
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    $this->assertAuthenticated();
});

it('rejects registration with missing name', function () {
    $role = Role::firstOrCreate(['name' => 'Customer'], ['description' => 'A customer']);

    $response = $this->post('/register', [
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role_id' => $role->id,
    ]);

    $response->assertSessionHasErrors('name');
});

it('rejects registration with duplicate email', function () {
    $role = Role::firstOrCreate(['name' => 'Customer'], ['description' => 'A customer']);
    User::factory()->create(['email' => 'taken@example.com']);

    $response = $this->post('/register', [
        'name' => 'Another User',
        'email' => 'taken@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role_id' => $role->id,
    ]);

    $response->assertSessionHasErrors('email');
});

it('rejects registration with short password', function () {
    $role = Role::firstOrCreate(['name' => 'Customer'], ['description' => 'A customer']);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
        'role_id' => $role->id,
    ]);

    $response->assertSessionHasErrors('password');
});

it('rejects registration when password confirmation does not match', function () {
    $role = Role::firstOrCreate(['name' => 'Customer'], ['description' => 'A customer']);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different123',
        'role_id' => $role->id,
    ]);

    $response->assertSessionHasErrors('password');
});

// ── Login ────────────────────────────────────────────────────────────

it('allows a user to login with correct credentials', function () {
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
});

it('rejects login with wrong password', function () {
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('rejects login with non-existent email', function () {
    $response = $this->post('/login', [
        'email' => 'nobody@example.com',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

// ── Logout ───────────────────────────────────────────────────────────

it('allows an authenticated user to logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect('/');
    $this->assertGuest();
});
