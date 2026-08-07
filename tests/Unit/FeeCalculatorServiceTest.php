<?php

namespace Tests\Unit;

use App\Models\FeeRule;
use PHPUnit\Framework\Attributes\Test;
use App\Models\SizeFeeRule;
use App\Services\FeeCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeCalculatorServiceTest extends TestCase
{
    use RefreshDatabase;

    private FeeCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FeeCalculatorService();

        // Seed the default rules
        FeeRule::create([
            'min_price'  => 0,
            'max_price'  => null,
            'fee_type'   => 'percentage',
            'fee_value'  => 12,
            'sort_order' => 0,
        ]);

        SizeFeeRule::create(['size_tier' => 'small',     'flat_fee' => 0,  'requires_manual_quote' => false]);
        SizeFeeRule::create(['size_tier' => 'medium',    'flat_fee' => 5,  'requires_manual_quote' => false]);
        SizeFeeRule::create(['size_tier' => 'large',     'flat_fee' => 12, 'requires_manual_quote' => false]);
        SizeFeeRule::create(['size_tier' => 'oversized', 'flat_fee' => 30, 'requires_manual_quote' => true]);
    }

    // ── 12% Service Fee Tests ─────────────────────────────────────────────────

    #[Test]
    public function it_calculates_12_percent_fee_on_a_100_dollar_item(): void
    {
        $result = $this->service->calculate(100.00, 'small', 1);

        $this->assertFalse($result['requires_manual_quote']);
        $this->assertEquals(12.00, $result['tier_fee']);   // 12% of $100
        $this->assertEquals(0.00, $result['size_fee']);    // small = $0
        $this->assertEquals(12.00, $result['total_fee']);
        $this->assertEquals(112.00, $result['total_charged']);
    }

    #[Test]
    public function it_calculates_12_percent_fee_on_a_50_dollar_item_with_medium_size(): void
    {
        $result = $this->service->calculate(50.00, 'medium', 1);

        $this->assertFalse($result['requires_manual_quote']);
        $this->assertEquals(6.00, $result['tier_fee']);    // 12% of $50
        $this->assertEquals(5.00, $result['size_fee']);    // medium = $5
        $this->assertEquals(11.00, $result['total_fee']);
        $this->assertEquals(61.00, $result['total_charged']);
    }

    #[Test]
    public function it_multiplies_tier_fee_by_quantity_but_not_size_fee(): void
    {
        // 2 × $100 items, large size
        $result = $this->service->calculate(100.00, 'large', 2);

        $this->assertFalse($result['requires_manual_quote']);
        $this->assertEquals(24.00, $result['tier_fee']);   // 12% of $100 × 2
        $this->assertEquals(12.00, $result['size_fee']);   // large = $12 (once per order)
        $this->assertEquals(36.00, $result['total_fee']);
        $this->assertEquals(236.00, $result['total_charged']); // (100 × 2) + 36
    }

    #[Test]
    public function it_rounds_percentage_fee_to_two_decimals(): void
    {
        $result = $this->service->calculate(33.33, 'small', 1);

        $this->assertFalse($result['requires_manual_quote']);
        // 12% of $33.33 = $4.0 (rounded)
        $this->assertEquals(round(33.33 * 0.12, 2), $result['tier_fee']);
    }

    #[Test]
    public function it_handles_flat_fee_type_correctly(): void
    {
        // Replace with a flat $10 rule
        FeeRule::truncate();
        FeeRule::create(['min_price' => 0, 'max_price' => null, 'fee_type' => 'flat', 'fee_value' => 10, 'sort_order' => 0]);

        $result = $this->service->calculate(200.00, 'small', 1);

        $this->assertEquals(10.00, $result['tier_fee']);
        $this->assertEquals(210.00, $result['total_charged']);
    }

    #[Test]
    public function it_handles_multiple_price_bracket_rules(): void
    {
        // Add a lower bracket for expensive items
        FeeRule::truncate();
        FeeRule::create(['min_price' => 0,   'max_price' => 99.99, 'fee_type' => 'percentage', 'fee_value' => 15, 'sort_order' => 0]);
        FeeRule::create(['min_price' => 100, 'max_price' => null,  'fee_type' => 'percentage', 'fee_value' => 10, 'sort_order' => 1]);

        $cheapResult      = $this->service->calculate(50.00, 'small', 1);   // 15%
        $expensiveResult  = $this->service->calculate(200.00, 'small', 1);  // 10%

        $this->assertEquals(7.50, $cheapResult['tier_fee']);
        $this->assertEquals(20.00, $expensiveResult['tier_fee']);
    }

    // ── Oversized Manual Quote Path ───────────────────────────────────────────

    #[Test]
    public function it_flags_oversized_orders_for_manual_quote(): void
    {
        $result = $this->service->calculate(500.00, 'oversized', 1);

        $this->assertTrue($result['requires_manual_quote']);
        $this->assertEquals(0.0, $result['tier_fee']);
        $this->assertEquals(0.0, $result['size_fee']);
        $this->assertEquals(0.0, $result['total_charged']);
    }

    #[Test]
    public function it_throws_exception_when_no_fee_rule_found(): void
    {
        FeeRule::truncate();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/No fee rule found/');

        $this->service->calculate(100.00, 'small', 1);
    }

    #[Test]
    public function it_throws_exception_when_no_size_rule_found(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/No size fee rule found/');

        $this->service->calculate(100.00, 'invalid_tier', 1);
    }
}
