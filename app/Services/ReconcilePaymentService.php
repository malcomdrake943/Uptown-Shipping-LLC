<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Notifications\AdditionalPaymentRequiredNotification;
use App\Notifications\RefundIssuedNotification;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Refund;

class ReconcilePaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('cashier.secret'));
    }

    /**
     * Reconcile an order after the admin records the actual final product price.
     *
     * @return array{action: string, amount: float, stripe_id: ?string}
     */
    public function reconcile(Order $order, float $finalProductPrice): array
    {
        // Compute what should have been charged with the actual price
        $actualProductSubtotal = round($finalProductPrice * $order->quantity, 2);

        // Fees are kept the same (already computed at order time)
        $actualTotal = round($actualProductSubtotal + (float) $order->service_fee + (float) $order->size_handling_fee, 2);

        $difference = round($actualTotal - (float) $order->total_charged, 2);

        // Save the final price
        $order->update([
            'final_product_price' => $finalProductPrice,
        ]);

        if ($difference > 0.50) {
            // Customer owes more → create a new PaymentIntent for the difference
            return $this->createAdditionalCharge($order, $difference);
        }

        if ($difference < -0.50) {
            // We owe the customer → issue a partial Stripe refund
            return $this->issueRefund($order, abs($difference));
        }

        // Within $0.50 tolerance → mark resolved, no action
        $order->update(['price_reconciliation_status' => 'resolved']);

        return [
            'action'     => 'none',
            'amount'     => 0.0,
            'stripe_id'  => null,
            'difference' => $difference,
        ];
    }

    private function createAdditionalCharge(Order $order, float $amount): array
    {
        $amountCents = (int) round($amount * 100);

        $intent = PaymentIntent::create([
            'amount'               => $amountCents,
            'currency'             => 'usd',
            'payment_method_types' => ['card'],
            'metadata'             => [
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'type'         => 'additional_charge',
            ],
            'description' => "Additional charge for order {$order->order_number}",
        ]);

        Payment::create([
            'order_id'                 => $order->id,
            'stripe_payment_intent_id' => $intent->id,
            'type'                     => 'additional',
            'amount'                   => $amount,
            'currency'                 => 'usd',
            'status'                   => $intent->status,
        ]);

        $order->update(['price_reconciliation_status' => 'additional_payment_due']);

        // Notify customer
        $order->notify(new AdditionalPaymentRequiredNotification($order, $amount, $intent->client_secret));

        return [
            'action'        => 'additional_charge',
            'amount'        => $amount,
            'stripe_id'     => $intent->id,
            'client_secret' => $intent->client_secret,
            'difference'    => $amount,
        ];
    }

    private function issueRefund(Order $order, float $amount): array
    {
        $amountCents = (int) round($amount * 100);

        // Find the original payment intent
        $originalPayment = $order->payments()
            ->where('type', 'initial')
            ->where('status', 'succeeded')
            ->first();

        if (! $originalPayment) {
            throw new \RuntimeException("No succeeded initial payment found for order {$order->order_number}");
        }

        $refund = Refund::create([
            'payment_intent' => $originalPayment->stripe_payment_intent_id,
            'amount'         => $amountCents,
            'metadata'       => [
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
            ],
        ]);

        Payment::create([
            'order_id'                 => $order->id,
            'stripe_payment_intent_id' => $refund->id,
            'type'                     => 'refund',
            'amount'                   => $amount,
            'currency'                 => 'usd',
            'status'                   => $refund->status,
        ]);

        $order->update(['price_reconciliation_status' => 'resolved']);

        // Notify customer
        $order->notify(new RefundIssuedNotification($order, $amount));

        return [
            'action'     => 'refund',
            'amount'     => $amount,
            'stripe_id'  => $refund->id,
            'difference' => -$amount,
        ];
    }
}
