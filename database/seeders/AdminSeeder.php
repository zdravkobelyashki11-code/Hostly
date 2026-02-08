<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the admin user.
     */
    public function run(): void
    {
        // Get or create Admin role
        $adminRole = Role::firstOrCreate(
            ['name' => 'Admin'],
            ['description' => 'System Administrator']
        );

        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@hostly.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole->id,
            ]
        );
    }
}
