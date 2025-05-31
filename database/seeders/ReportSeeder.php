<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Report;
use App\Models\Profile;
use App\Enums\UserRoles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        // Create some users first to ensure we have users to reference
        $users = User::factory(5)->create();

        $this->assignRole($users);

        // Create a mix of pending, approved, and rejected reports
        collect([
            'pending' => 10,
            'approved' => 5,
            'rejected' => 5,
        ])->each(function ($count, $status) use ($users) {
            Report::factory()
                ->$status()
                    ->count($count)
                    ->withAttachments(rand(1, 3))
                    ->create([
                        'user_id' => fn() => $users->random()->id,
                        'reported_by' => fn() => $users->random()->id,
                    ]);
        });
    }

    public function assignRole(Collection $userCollection)
    {
        foreach ($userCollection as $user) {
            $customerRole = Role::where('name', UserRoles::CUSTOMER->value)->first();
            $user->assignRole($customerRole);

            $profile = Profile::factory()->create(['user_id' => $user->id]);
        }
    }
}
