<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use App\Models\Profile;
use App\Enums\UserRoles;
use App\Enums\ServicesType;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\ServiceType as ModelsServiceType;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


        $roles = UserRoles::cases();

        collect($roles)->map(function ($role) {
            Role::create([
                'name' => $role->value
            ]);
        });

        $adminRole = Role::where('name', UserRoles::ADMIN->value)->first();

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123')
        ]);

        $admin->assignRole($adminRole);

        Profile::factory()->create(['user_id' => $admin->id]);

        // $serviceTypes = ServicesType::cases();

        // collect($serviceTypes)->map(function($serviceType) {
        //     ModelsServiceType::create([
        //         'name' => $serviceType->value
        //     ]);
        // });

        $this->call([
            ServiceTypeSeeder::class,
            BarangaySeeder::class,
            UserProviderSeeder::class,
            AvailServiceSeeder::class,
            FeedbackSeeder::class,
        ]);
    }
}
