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
            '+1 (478) 442-3863',
            'Mobile Money Support Phone Number',
            'payment'
        );

        Setting::set(
            'support_phone',
            '+1 (804) 915-7862',
            'General Support Phone Number',
            'general'
        );
    }
}
