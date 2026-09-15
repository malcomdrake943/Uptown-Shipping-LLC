<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            FeeRuleSeeder::class,
            SizeFeeRuleSeeder::class,
            AdminUserSeeder::class,
            PlatformSeeder::class,
        ]);
    }
}
