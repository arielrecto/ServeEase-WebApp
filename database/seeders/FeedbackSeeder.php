<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\FeedBack;
use App\Models\AvailService;
use Illuminate\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    public function run()
    {
        $services = AvailService::where('status', 'completed')->get();

        foreach ($services as $service) {
            $feedback = FeedBack::create([
                'user_id' => $service->user_id,
                'content' => 'Service was ' . ['excellent', 'good', 'average'][rand(0, 2)],
                'rate' => rand(3, 5),
                'avail_service_id' => $service->id
            ]);

            $attachments = \App\Models\Attachment::factory()->count(rand(0, 2))->make()->each(function ($attachment) {
                $attachment->file_path = 'reports/' . basename($attachment->file_path);
            });

            $feedback->attachments()->saveMany($attachments);
        }
    }
}
