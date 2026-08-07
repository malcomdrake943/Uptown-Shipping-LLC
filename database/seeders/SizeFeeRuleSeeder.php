<?php

namespace Database\Seeders;

use App\Models\SizeFeeRule;
use Illuminate\Database\Seeder;

class SizeFeeRuleSeeder extends Seeder
{
    public function run(): void
    {
        SizeFeeRule::truncate();

        $rules = [
            ['size_tier' => 'small',     'flat_fee' => 0,  'requires_manual_quote' => false],
            ['size_tier' => 'medium',    'flat_fee' => 5,  'requires_manual_quote' => false],
            ['size_tier' => 'large',     'flat_fee' => 12, 'requires_manual_quote' => false],
            ['size_tier' => 'oversized', 'flat_fee' => 30, 'requires_manual_quote' => true],
        ];

        foreach ($rules as $rule) {
            SizeFeeRule::create($rule);
        }
    }
}
