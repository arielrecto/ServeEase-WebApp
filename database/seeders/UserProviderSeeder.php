<?php

namespace Database\Seeders;

use App\Models\PaymentAccount;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Profile;
use App\Models\Service;
use App\Enums\UserRoles;
use App\Models\ServiceType;
use Illuminate\Support\Str;
use App\Models\ProviderProfile;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
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

            $serviceTypeId = ServiceType::all()->random()->id;

            ProviderProfile::create([
                'service_type_id' => 1,
                'experience' => '2',
                'experience_duration' => 'Years',
                'contact' => '09123456789',
                'valid_id_type' => 'Passport',
                'valid_id_image' => 'default.jpg',
                'citizenship_document_type' => 'Barangay ID',
                'citizenship_document_image' => 'default.jpg',
                'verified_at' => Carbon::now()->format('Y-m-d h:i:s'),
                'proof_document_image' => 'default.jpg',
                'profile_id' => $profile->id
            ]);

            Service::factory()->create(['service_type_id' => $serviceTypeId, 'user_id' => $user->id]);

            $accountType = fake()->randomElement(['gcash', 'paymaya', 'bank', 'cash']);

            $paymentAccountData = [
                'account_type' => $accountType,
                'account_number' => match ($accountType) {
                    'gcash' => '09123456789',
                    'paymaya' => '09123456789',
                    'bank' => '09123456789',
                    'cash' => null,
                },
                'account_name' => $accountType ? $user->profile->full_name : null,
                'user_id' => $user->id,
            ];

            PaymentAccount::create($paymentAccountData);
        }
    }
}
