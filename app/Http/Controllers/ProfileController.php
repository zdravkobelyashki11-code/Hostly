<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $user->load('profile');

        return view('profile.edit', compact('user'));
    }

    public function store(Request $request)
    {
        $this->saveProfile($request);

        return redirect()->route('profile.edit')->with('success', 'Profile saved successfully.');
    }

    public function update(Request $request)
    {
        $this->saveProfile($request);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function destroy()
    {
        $profile = auth()->user()->profile;

        if ($profile) {
            $profile->delete();
        }

        return redirect()->route('profile.edit')->with('success', 'Profile deleted successfully.');
    }

    private function saveProfile(Request $request): void
    {
        $user = auth()->user();

        $validated = $request->validate([
            'avatar' => 'nullable|url|max:2048',
            'bio' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
        ]);

        $profile = Profile::withTrashed()->firstOrNew([
            'user_id' => $user->id,
        ]);

        if ($profile->trashed()) {
            $profile->restore();
        }

        $profile->fill($validated);
        $profile->user()->associate($user);
        $profile->save();
    }
}
