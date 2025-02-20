<?php

namespace Database\Factories;

use App\Models\Barangay;
use App\Models\ServiceType;
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
            'service_type_id' => ServiceType::all()->random()->id,
            'barangay_id' => Barangay::all()->random()->id,
        ];
    }
}
