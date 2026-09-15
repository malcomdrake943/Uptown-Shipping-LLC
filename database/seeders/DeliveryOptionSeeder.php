<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliveryOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\DeliveryOption::insert([
            ['name' => 'Express Air', 'duration' => '3 - 7 Days', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Standard Air', 'duration' => '7 - 14 Days', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sea Freight', 'duration' => '4 - 8 Weeks', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
