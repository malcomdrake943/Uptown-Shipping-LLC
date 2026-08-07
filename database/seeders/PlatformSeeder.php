<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure the directory exists in the public storage disk
        Storage::disk('public')->makeDirectory('platforms');

        $platforms = [
            [
                'name' => 'Amazon',
                'logo_filename' => 'amazon.png',
                'url' => 'https://www.amazon.com',
            ],
            [
                'name' => 'Walmart',
                'logo_filename' => 'walmart.png',
                'url' => 'https://www.walmart.com',
            ],
            [
                'name' => 'Best Buy',
                'logo_filename' => 'bestbuy.png',
                'url' => 'https://www.bestbuy.com',
            ],
        ];

        foreach ($platforms as $p) {
            $sourcePath = public_path('images/' . $p['logo_filename']);
            $targetRelativePath = 'platforms/' . $p['logo_filename'];

            if (file_exists($sourcePath)) {
                $logoContents = file_get_contents($sourcePath);
                Storage::disk('public')->put($targetRelativePath, $logoContents);
            }

            Platform::updateOrCreate(
                ['name' => $p['name']],
                [
                    'logo' => $targetRelativePath,
                    'url' => $p['url'],
                    'is_active' => true,
                ]
            );
        }
    }
}
