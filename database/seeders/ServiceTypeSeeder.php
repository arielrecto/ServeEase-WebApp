<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ServiceTypeSeeder extends Seeder
{
    use WithoutModelEvents;

    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    public function run(): void
    {
        $services = [
            [
                'name' => 'Basic Cleaning',
                'thumbnail' => $this->faker->imageUrl(),
                'description' => 'Standard cleaning service for residential spaces'
            ],
            [
                'name' => 'Deep Cleaning',
                'thumbnail' => $this->faker->imageUrl(),
                'description' => 'Thorough cleaning including hard-to-reach areas'
            ],
            [
                'name' => 'Commercial Cleaning',
                'thumbnail' => $this->faker->imageUrl(),
                'description' => 'Office and commercial space maintenance'
            ]
        ];

        foreach ($services as $service) {
            ServiceType::create($service);
        }
    }
}
