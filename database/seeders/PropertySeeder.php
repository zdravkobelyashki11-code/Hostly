<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

// My code starts here
class PropertySeeder extends Seeder
{
    public function run(): void
    {
        // Get or create a Host user
        $hostRole = Role::where('name', 'Host')->first();
        
        if (!$hostRole) {
            $hostRole = Role::create(['name' => 'Host']);
        }

        $host = User::where('role_id', $hostRole->id)->first();
        
        if (!$host) {
            $host = User::create([
                'name' => 'Demo Host',
                'email' => 'host@demo.com',
                'password' => bcrypt('password'),
                'role_id' => $hostRole->id,
            ]);
        }
        

        // Sample properties data
        $properties = [
            [
                'title' => 'Cozy Mountain Cabin',
                'description' => 'A beautiful cabin nestled in the mountains with stunning views.',
                'price_per_night' => 150.00,
                'location' => '123 Mountain Road',
                'city' => 'Aspen',
                'country' => 'USA',
                'max_guests' => 4,
                'bedrooms' => 2,
                'bathrooms' => 1,
            ],
            [
                'title' => 'Beachfront Paradise',
                'description' => 'Wake up to ocean views in this luxurious beachfront property.',
                'price_per_night' => 280.00,
                'location' => '456 Ocean Drive',
                'city' => 'Miami',
                'country' => 'USA',
                'max_guests' => 6,
                'bedrooms' => 3,
                'bathrooms' => 2,
            ],
            [
                'title' => 'Modern City Loft',
                'description' => 'Stylish loft in the heart of downtown with skyline views.',
                'price_per_night' => 120.00,
                'location' => '789 Main Street',
                'city' => 'New York',
                'country' => 'USA',
                'max_guests' => 2,
                'bedrooms' => 1,
                'bathrooms' => 1,
            ],
            [
                'title' => 'Tuscan Villa',
                'description' => 'Authentic Italian villa surrounded by vineyards and olive groves.',
                'price_per_night' => 350.00,
                'location' => 'Via Roma 45',
                'city' => 'Florence',
                'country' => 'Italy',
                'max_guests' => 8,
                'bedrooms' => 4,
                'bathrooms' => 3,
            ],
            [
                'title' => 'Tropical Bungalow',
                'description' => 'Private bungalow with pool in a tropical paradise.',
                'price_per_night' => 200.00,
                'location' => '12 Palm Lane',
                'city' => 'Bali',
                'country' => 'Indonesia',
                'max_guests' => 4,
                'bedrooms' => 2,
                'bathrooms' => 2,
            ],
            [
                'title' => 'Scandinavian Retreat',
                'description' => 'Minimalist cabin with northern lights viewing deck.',
                'price_per_night' => 180.00,
                'location' => 'Nordic Way 8',
                'city' => 'Tromsø',
                'country' => 'Norway',
                'max_guests' => 3,
                'bedrooms' => 1,
                'bathrooms' => 1,
            ],
        ];

        foreach ($properties as $index => $data) {
            $property = Property::create(array_merge($data, ['host_id' => $host->id]));

            // Add local image
            PropertyImage::create([
                'property_id' => $property->id,
                'image_path' => 'properties/property' . ($index + 1) . '.jpg',
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }
    }
}