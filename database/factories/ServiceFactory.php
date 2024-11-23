<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(),
            'price' => fake()->randomNumber(5, true),
            'price_type' => 'fixed',
            'description' => fake()->paragraph(),
            'is_approved' => true,
            'service_type' => 'plumbing',
            // 'barangay_id' => fake()->numberBetween(15, 30)
            'barangay_id' => 30,
        ];
    }
}
