<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    public function definition(): array
    {
        $fileTypes = ['pdf', 'jpg', 'png', 'doc'];
        $fileType = $this->faker->randomElement($fileTypes);

        return [
            'file_name' => $this->faker->word() . '.' . $fileType,
            'file_path' => 'storage/attachments/' . $this->faker->uuid() . '.' . $fileType,
            'file_type' => $fileType,
            'file_size' => $this->faker->numberBetween(100000, 5000000), // Between 100KB and 5MB
            'mime_type' => match ($fileType) {
                'pdf' => 'application/pdf',
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'doc' => 'application/msword',
            },
        ];
    }
}
