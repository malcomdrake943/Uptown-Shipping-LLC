<?php

namespace Database\Seeders;

use App\Models\FeeRule;
use Illuminate\Database\Seeder;

class FeeRuleSeeder extends Seeder
{
    public function run(): void
    {
        FeeRule::truncate();

        // Default: 12% service fee across all price ranges.
        // Admin can later split this into price brackets (e.g., 15% for <$50, 10% for >$200)
        // by adding/editing rows in Filament → Settings → Fee Rules without any code changes.
        FeeRule::create([
            'min_price'  => 0,
            'max_price'  => null, // unbounded
            'fee_type'   => 'percentage',
            'fee_value'  => 12,
            'sort_order' => 0,
        ]);
    }
}
