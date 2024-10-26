<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enums\ServicesType;
use App\Models\User;
use App\Enums\UserRoles;
use App\Models\ServiceType as ModelsServiceType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

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



        // $serviceTypes = ServicesType::cases();


        // collect($serviceTypes)->map(function($serviceType) {
        //     ModelsServiceType::create([
        //         'name' => $serviceType->value
        //     ]);
        // });
    }
}
