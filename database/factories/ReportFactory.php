<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    public function definition(): array
    {
        $types = ['illegal_transaction', 'unfinished_booking'];
        $statuses = ['pending', 'approved', 'rejected'];

        return [
            'user_id' => User::factory(),
            'complaint' => $this->faker->paragraph(3),
            'status' => $this->faker->randomElement($statuses),
            'type' => $this->faker->randomElement($types),
            'reported_by' => User::factory(),
            'created_at' => $this->faker->dateTimeBetween('-3 months'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at']);
            },
        ];
    }

    public function withAttachments($count = 2)
    {
        return $this->afterCreating(function ($report) use ($count) {
            $attachments = \App\Models\Attachment::factory()->count($count)->make()->each(function ($attachment) {
                $attachment->file_path = 'reports/' . basename($attachment->file_path);
            });

            $report->attachments()->saveMany($attachments);
        });
    }

    public function pending()
    {
        return $this->state(function () {
            return ['status' => 'pending'];
        });
    }

    public function approved()
    {
        return $this->state(function () {
            return [
                'status' => 'approved',
                // 'admin_remarks' => $this->faker->paragraph(),
            ];
        });
    }

    public function rejected()
    {
        return $this->state(function () {
            return [
                'status' => 'rejected',
                // 'admin_remarks' => $this->faker->paragraph(),
            ];
        });
    }
}
