<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set(
            'mobile_money_phone',
            '+1 (555) 123-4567',
            'Mobile Money Support Phone Number',
            'payment'
        );

        Setting::set(
            'support_phone',
            '+1 (555) 987-6543',
            'General Support Phone Number',
            'general'
        );
    }
}
