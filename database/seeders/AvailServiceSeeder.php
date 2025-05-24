<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Service;
use App\Models\AvailService;
use Illuminate\Database\Seeder;

class AvailServiceSeeder extends Seeder
{
    public function run()
    {

        $users = User::role('customer')->pluck('id');
        $statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];

        foreach (range(1, 100) as $index) {
            $startDate = Carbon::now()->subDays(rand(0, 60));

            AvailService::create([
                'user_id' => $users->random(),
                'service_id' => Service::all()->pluck('id')->random(),
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
