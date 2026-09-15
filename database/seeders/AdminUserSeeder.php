<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmins = [
            [
                'name'     => 'Super Admin 1',
                'email'    => 'admin1@parcelproxy.com',
                'phone'    => '+1 (478) 442-3863',
                'password' => Hash::make('password'),
                'role'     => 'superadmin',
            ],
            [
                'name'     => 'Super Admin 2',
                'email'    => 'admin2@parcelproxy.com',
                'phone'    => '+1 (804) 915-7862',
                'password' => Hash::make('password'),
                'role'     => 'superadmin',
            ],
        ];

        foreach ($superAdmins as $adminData) {
            User::updateOrCreate(
                ['email' => $adminData['email']],
                $adminData
            );
        }

        // Also update primary legacy admin if present
        User::where('email', 'admin@parcelproxy.com')->update([
            'phone' => '+1 (478) 442-3863',
        ]);
    }
}
