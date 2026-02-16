<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'host_id' => User::factory()->host(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'price_per_night' => fake()->randomFloat(2, 30, 500),
            'location' => fake()->address(),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'max_guests' => fake()->numberBetween(1, 10),
            'bedrooms' => fake()->numberBetween(1, 5),
            'bathrooms' => fake()->numberBetween(1, 3),
            'is_active' => true,
        ];
    }

    /**
     * State for an inactive property.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
