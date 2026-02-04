<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // My code starts here
        Role::updateOrCreate(['name' => 'Admin'], ['description' => 'System Administrator']);
        Role::updateOrCreate(['name' => 'Host'], ['description' => 'Property Owner']);
        Role::updateOrCreate(['name' => 'Guest'], ['description' => 'Standard User']);
    }
}
