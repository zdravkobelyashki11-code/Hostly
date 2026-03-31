<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('now', '+1 month');
        $checkOut = fake()->dateTimeBetween($checkIn, '+2 months');
        $property = \App\Models\Property::factory()->create();

        return [
            'property_id' => $property->id,
            'guest_id' => \App\Models\User::factory(),
            'host_id' => $property->host_id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'total_price' => fake()->randomFloat(2, 50, 1000),
            'status' => 'pending',
        ];
    }
}
