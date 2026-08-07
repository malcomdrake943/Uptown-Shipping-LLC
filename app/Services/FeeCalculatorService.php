<?php

namespace App\Services;

use App\Models\FeeRule;
use App\Models\SizeFeeRule;

class FeeCalculatorService
{
    /**
     * Calculate the full fee breakdown for an order.
     *
     * @param  float   $estimatedPrice  Customer-entered product price
     * @param  string  $sizeTier        one of: small, medium, large, oversized
     * @param  int     $quantity
     * @return array{
     *     requires_manual_quote: bool,
     *     tier_fee: float,
     *     size_fee: float,
     *     total_fee: float,
     *     total_charged: float,
     *     breakdown: array
     * }
     *
     * @throws \RuntimeException if no fee rule is found for the given price
     */
    public function calculate(float $estimatedPrice, string $sizeTier, int $quantity = 1): array
    {
        // ── 1. Size fee lookup ──────────────────────────────────────────────────
        $sizeRule = SizeFeeRule::findForTier($sizeTier);

        if (! $sizeRule) {
            throw new \RuntimeException("No size fee rule found for tier: {$sizeTier}");
        }

        // If this size tier requires a manual quote, return early
        if ($sizeRule->requires_manual_quote) {
            return [
                'requires_manual_quote' => true,
                'tier_fee'              => 0.0,
                'size_fee'              => 0.0,
                'total_fee'             => 0.0,
                'total_charged'         => 0.0,
                'breakdown'             => [
                    'message' => 'This size tier requires a manual quote from our team.',
                ],
            ];
        }

        // ── 2. Tier fee lookup by price ─────────────────────────────────────────
        $feeRule = FeeRule::findForPrice($estimatedPrice);

        if (! $feeRule) {
            throw new \RuntimeException("No fee rule found for price: {$estimatedPrice}");
        }

        $tierFeePerUnit = $feeRule->fee_type === 'percentage'
            ? round($estimatedPrice * ((float) $feeRule->fee_value / 100), 2)
            : (float) $feeRule->fee_value;

        // ── 3. Size fee is per-order (not multiplied by quantity) ───────────────
        $sizeFee = (float) $sizeRule->flat_fee;

        // ── 4. Tier fee is multiplied by quantity ───────────────────────────────
        $totalTierFee = round($tierFeePerUnit * $quantity, 2);
        $totalFee     = round($totalTierFee + $sizeFee, 2);
        $totalCharged = round(($estimatedPrice * $quantity) + $totalFee, 2);

        return [
            'requires_manual_quote' => false,
            'tier_fee'              => $totalTierFee,
            'size_fee'              => $sizeFee,
            'total_fee'             => $totalFee,
            'total_charged'         => $totalCharged,
            'breakdown'             => [
                'product_subtotal'   => round($estimatedPrice * $quantity, 2),
                'fee_rule_type'      => $feeRule->fee_type,
                'fee_rule_value'     => (float) $feeRule->fee_value,
                'tier_fee_per_unit'  => $tierFeePerUnit,
                'tier_fee_total'     => $totalTierFee,
                'size_fee'           => $sizeFee,
                'size_tier'          => $sizeTier,
                'quantity'           => $quantity,
            ],
        ];
    }

    /**
     * Return a JS-safe array of size fee rules for client-side fee preview.
     */
    public static function sizeFeeRulesForJs(): array
    {
        return SizeFeeRule::all()
            ->mapWithKeys(fn ($rule) => [
                $rule->size_tier => [
                    'flat_fee'             => (float) $rule->flat_fee,
                    'requires_manual_quote' => (bool) $rule->requires_manual_quote,
                ],
            ])
            ->toArray();
    }

    /**
     * Return the active fee rule data for JS-side calculations.
     * For simplicity, returns the first/primary rule; the JS calculator uses
     * server-side verification anyway.
     */
    public static function feeRulesForJs(): array
    {
        return FeeRule::orderBy('sort_order')->orderBy('min_price')->get()
            ->map(fn ($rule) => [
                'min_price' => (float) $rule->min_price,
                'max_price' => $rule->max_price ? (float) $rule->max_price : null,
                'fee_type'  => $rule->fee_type,
                'fee_value' => (float) $rule->fee_value,
            ])
            ->toArray();
    }
}
