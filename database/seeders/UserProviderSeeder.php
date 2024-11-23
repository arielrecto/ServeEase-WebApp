<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Profile;
use App\Models\Service;
use App\Enums\UserRoles;
use App\Models\ProviderProfile;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory(30)->create();

        foreach ($users as $user) {
            $customerRole = Role::where('name', UserRoles::CUSTOMER->value)->first();

            $user->assignRole($customerRole);

            $profile = Profile::factory()->create(['user_id' => $user->id]);

            ProviderProfile::create([
                'service_type' => 'plumbing',
                'experience' => '2-4 yrs.',
                'contact' => '09123456789',
                'verified_at' => Carbon::now()->format('Y-m-d h:i:s'),
                'certificate' => 'default.jpg',
                'profile_id' => $profile->id
            ]);

            Service::factory()->create(['user_id' => $user->id]);
        }
    }
}
