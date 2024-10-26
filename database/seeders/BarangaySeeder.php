<?php

namespace Database\Seeders;

use App\Models\Barangay;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brgys = require_once database_path('/seeders/data/barangay.php');

        foreach ($brgys as $brgy) {
            Barangay::create(['name' => $brgy]);
        }
    }
}
