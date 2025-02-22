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
                'name' => 'Plumbing',
                'thumbnail' => $this->faker->imageUrl(),
                'description' => 'Installation, repair, and maintenance of water pipes, drainage systems, and fixtures.'
            ],
            [
                'name' => 'Carpentry',
                'thumbnail' => $this->faker->imageUrl(),
                'description' => 'Custom woodwork, furniture making, repairs, and structural improvements.'
            ],
            [
                'name' => 'Electrician',
                'thumbnail' => $this->faker->imageUrl(),
                'description' => 'Installation, repair, and maintenance of electrical systems, wiring, and appliances.'
            ],
            [
                'name' => 'Catering',
                'thumbnail' => $this->faker->imageUrl(),
                'description' => 'Professional food preparation and service for events, parties, and corporate functions.'
            ],
            [
                'name' => 'Driver',
                'thumbnail' => $this->faker->imageUrl(),
                'description' => 'Safe and reliable transportation services for individuals, businesses, and deliveries.'
            ],
            [
                'name' => 'IT Professional',
                'thumbnail' => $this->faker->imageUrl(),
                'description' => 'Technical support, network management, software development, and IT solutions.'
            ]
        ];


        foreach ($services as $service) {
            ServiceType::create($service);
        }
    }
}
