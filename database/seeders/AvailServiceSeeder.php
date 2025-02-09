<?php

namespace Database\Seeders;

use App\Models\AvailService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AvailServiceSeeder extends Seeder
{
    public function run()
    {

        $users = User::role( 'customer')->pluck('id');
        $statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];

        foreach (range(1, 100) as $index) {
            $startDate = Carbon::now()->subDays(rand(0, 60));

            AvailService::create([
                'user_id' => $users->random(),
                'service_id' => rand(1, 3),
                'status' => $statuses[array_rand($statuses)],
                'total_hours' => rand(2, 8),
                'start_date' => $startDate,
                'end_date' => $startDate->copy()->addHours(rand(2, 8)),
                'remarks' => 'Service request ' . $index,
                'total_price' => rand(50, 300) * 100
            ]);
        }
    }
}
