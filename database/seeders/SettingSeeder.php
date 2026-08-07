<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'support_phone'],
            [
                'label' => 'Customer Support / Mobile Money Phone Number',
                'value' => '804-239-5736',
            ]
        );
    }
}
