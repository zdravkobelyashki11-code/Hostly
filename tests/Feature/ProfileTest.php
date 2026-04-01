<?php

use App\Models\Profile;
use App\Models\User;

it('allows an authenticated user to create a profile', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('profile.store'), [
        'avatar' => 'https://example.com/avatar.jpg',
        'bio' => 'Short bio',
        'location' => 'Sofia, Bulgaria',
        'phone_number' => '+359888123456',
        'address' => '1 Main Street',
    ]);

    $response->assertRedirect(route('profile.edit'));

    $this->assertDatabaseHas('profiles', [
        'user_id' => $user->id,
        'avatar' => 'https://example.com/avatar.jpg',
        'bio' => 'Short bio',
        'location' => 'Sofia, Bulgaria',
        'phone_number' => '+359888123456',
        'address' => '1 Main Street',
    ]);
});

it('allows an authenticated user to update a profile', function () {
    $user = User::factory()->create();
    $profile = Profile::create([
        'user_id' => $user->id,
        'avatar' => 'https://example.com/old-avatar.jpg',
        'bio' => 'Old bio',
        'location' => 'Old location',
        'phone_number' => '111',
        'address' => 'Old address',
    ]);

    $response = $this->actingAs($user)->put(route('profile.update'), [
        'avatar' => 'https://example.com/new-avatar.jpg',
        'bio' => 'New bio',
        'location' => 'Varna, Bulgaria',
        'phone_number' => '222',
        'address' => 'New address',
    ]);

    $response->assertRedirect(route('profile.edit'));

    $this->assertDatabaseHas('profiles', [
        'id' => $profile->id,
        'avatar' => 'https://example.com/new-avatar.jpg',
        'bio' => 'New bio',
        'location' => 'Varna, Bulgaria',
        'phone_number' => '222',
        'address' => 'New address',
    ]);
});

it('soft deletes a profile when the user deletes it', function () {
    $user = User::factory()->create();
    $profile = Profile::create([
        'user_id' => $user->id,
        'bio' => 'Existing bio',
    ]);

    $response = $this->actingAs($user)->delete(route('profile.destroy'));

    $response->assertRedirect(route('profile.edit'));
    $this->assertSoftDeleted('profiles', ['id' => $profile->id]);
});

it('restores a soft deleted profile instead of creating a duplicate row', function () {
    $user = User::factory()->create();
    $profile = Profile::create([
        'user_id' => $user->id,
        'bio' => 'Archived bio',
    ]);
    $profile->delete();

    $response = $this->actingAs($user)->post(route('profile.store'), [
        'avatar' => 'https://example.com/restored-avatar.jpg',
        'bio' => 'Restored bio',
        'location' => 'Plovdiv, Bulgaria',
        'phone_number' => '333',
        'address' => 'Restored address',
    ]);

    $response->assertRedirect(route('profile.edit'));

    expect(Profile::withTrashed()->where('user_id', $user->id)->count())->toBe(1);

    $this->assertDatabaseHas('profiles', [
        'id' => $profile->id,
        'user_id' => $user->id,
        'avatar' => 'https://example.com/restored-avatar.jpg',
        'bio' => 'Restored bio',
        'location' => 'Plovdiv, Bulgaria',
        'phone_number' => '333',
        'address' => 'Restored address',
        'deleted_at' => null,
    ]);
});
