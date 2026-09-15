<?php

namespace App\Http\Controllers;

use App\Jobs\FetchProductJob;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Platform;
use App\Models\Setting;
use App\Notifications\OrderConfirmedNotification;
use App\Notifications\OrderNeedsManualQuoteNotification;
use App\Services\FeeCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;

class OrderController extends Controller
{
    public function __construct(
        private readonly FeeCalculatorService $feeCalculator,
        private readonly \App\Services\CardScanService $cardScanService
    ) {
        Stripe::setApiKey(config('cashier.secret'));
    }

    // ── Card Scan Integration ──────────────────────────────────────────────────

    public function initiateCardScan(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_name'  => 'required|string|max:200',
            'customer_email' => 'required|email|max:200',
            'customer_phone' => 'required|string|max:50',
            'force'          => 'nullable|boolean',
        ]);

        $force = (bool) ($data['force'] ?? false);
        $result = $this->cardScanService->initiateScan($data, $force);

        return response()->json($result);
    }

    public function getScanStatus(string $scanId): JsonResponse
    {
        $result = $this->cardScanService->getStatus($scanId);

        return response()->json($result);
    }

    // ── Custom Payment Charge (Stripe PaymentIntent) ──────────────────────────

    public function processPayment(Request $request): JsonResponse
    {
        $data = $request->validate([
            'platform_id'            => 'required|exists:platforms,id',
            'payment_method_id'      => 'required|string',
            'product_url'            => 'required|url|max:2048',
            'product_name'           => 'nullable|string|max:500',
            'product_image_url'      => 'nullable|url|max:2048',
            'estimated_product_price'=> 'required|numeric|min:0.01',
            'size_tier'              => 'required|in:small,medium,large,oversized',
            'shipping_method'        => 'nullable|string|in:express_air,standard_air,sea_freight',
            'product_weight'         => 'nullable|numeric|min:0.01|max:9999',
            'quantity'               => 'required|integer|min:1|max:100',
            'customer_name'          => 'required|string|max:200',
            'customer_email'         => 'required|email|max:200',
            'customer_phone'         => 'required|string|max:50',
            'shipping_address'       => 'required|array',
            'shipping_address.line1' => 'required|string|max:255',
            'shipping_address.line2' => 'nullable|string|max:255',
            'shipping_address.city'  => 'required|string|max:100',
            'shipping_address.state' => 'nullable|string|max:100',
            'shipping_address.postal_code' => 'required|string|max:20',
            'shipping_address.country'     => 'required|string|max:100',
            'customer_notes'         => 'nullable|string|max:1000',
        ]);

        try {
            $feeBreakdown = $this->feeCalculator->calculate(
                (float) $data['estimated_product_price'],
                $data['size_tier'],
                (int) $data['quantity']
            );
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        if ($feeBreakdown['requires_manual_quote']) {
            $order = $this->createOrder($data, $feeBreakdown, 'under_review');
            $order->notify(new OrderNeedsManualQuoteNotification($order));

            return response()->json([
                'manual_quote' => true,
                'order_number' => $order->order_number,
                'redirect'     => route('order.manual-quote-confirmation'),
            ]);
        }

        $stripeSecret = config('cashier.secret');
        $isMock = empty($stripeSecret) || str_contains($stripeSecret, 'your_secret_key_here') || str_starts_with($data['payment_method_id'], 'pm_mock_');

        if ($isMock) {
            $mockIntentId = 'pi_mock_' . \Illuminate\Support\Str::random(16);

            $order = $this->createOrder($data, $feeBreakdown, 'pending', null, $mockIntentId);

            Payment::create([
                'order_id'                 => $order->id,
                'stripe_payment_intent_id' => $mockIntentId,
                'type'                     => 'initial',
                'amount'                   => $feeBreakdown['total_charged'],
                'currency'                 => 'usd',
                'status'                   => 'succeeded',
            ]);

            try {
                $order->notify(new OrderConfirmedNotification($order));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Notification failed during mock order: " . $e->getMessage());
            }

            return response()->json([
                'success'  => true,
                'redirect' => route('order.success') . '?session_id=' . $mockIntentId,
            ]);
        }

        $totalCents = (int) round($feeBreakdown['total_charged'] * 100);

        try {
            $intent = \Stripe\PaymentIntent::create([
                'amount'                    => $totalCents,
                'currency'                  => 'usd',
                'payment_method'            => $data['payment_method_id'],
                'confirm'                   => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
                'description'               => "Jubilee Direct order purchase",
                'receipt_email'             => $data['customer_email'],
                'metadata'                  => [
                    'customer_name'  => $data['customer_name'],
                    'customer_email' => $data['customer_email'],
                ]
            ]);

            if ($intent->status === 'succeeded') {
                $order = $this->createOrder($data, $feeBreakdown, 'pending', null, $intent->id);

                Payment::create([
                    'order_id'                 => $order->id,
                    'stripe_payment_intent_id' => $intent->id,
                    'type'                     => 'initial',
                    'amount'                   => $feeBreakdown['total_charged'],
                    'currency'                 => 'usd',
                    'status'                   => 'succeeded',
                ]);

                $order->notify(new OrderConfirmedNotification($order));

                return response()->json([
                    'success'  => true,
                    'redirect' => route('order.success') . '?session_id=' . $intent->id,
                ]);
            }

            return response()->json([
                'success' => false,
                'error'   => 'Payment requires extra authentication or verification.'
            ], 402);

        } catch (\Stripe\Exception\CardException $e) {
            return response()->json(['success' => false, 'error' => $e->getError()->message], 402);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => 'Payment processing failed: ' . $e->getMessage()], 500);
        }
    }

    // ── Public Order Form ───────────────────────────────────────────────────────

    public function index(): \Illuminate\View\View
    {
        $sizeFeeRules = FeeCalculatorService::sizeFeeRulesForJs();
        $feeRules     = FeeCalculatorService::feeRulesForJs();
        $platforms    = Platform::where('is_active', true)->get();
        $supportPhone = Setting::get('mobile_money_phone')
            ?? Setting::get('support_phone')
            ?? config('app.support_phone', '+1 (800) 555-0199');
        $deliveryOptions = \Illuminate\Support\Facades\Schema::hasTable('delivery_options') ? \App\Models\DeliveryOption::where('is_active', true)->get() : collect();

        return view('order.index', compact('sizeFeeRules', 'feeRules', 'platforms', 'supportPhone', 'deliveryOptions'));
    }

    // ── Mobile Money Payment Processing ───────────────────────────────────────

    public function processMobileMoney(Request $request): JsonResponse
    {
        $data = $request->validate([
            'platform_id'            => 'required|exists:platforms,id',
            'product_url'            => 'required|url|max:2048',
            'product_name'           => 'nullable|string|max:500',
            'product_image_url'      => 'nullable|url|max:2048',
            'estimated_product_price'=> 'required|numeric|min:0.01',
            'size_tier'              => 'required|in:small,medium,large,oversized',
            'shipping_method'        => 'nullable|string|in:express_air,standard_air,sea_freight',
            'product_weight'         => 'nullable|numeric|min:0.01|max:9999',
            'quantity'               => 'required|integer|min:1|max:100',
            'customer_name'          => 'required|string|max:200',
            'customer_email'         => 'required|email|max:200',
            'customer_phone'         => 'required|string|max:50',
            'shipping_address'       => 'required|array',
            'shipping_address.line1' => 'required|string|max:255',
            'shipping_address.line2' => 'nullable|string|max:255',
            'shipping_address.city'  => 'required|string|max:100',
            'shipping_address.state' => 'nullable|string|max:100',
            'shipping_address.postal_code' => 'required|string|max:20',
            'shipping_address.country'     => 'required|string|max:100',
            'customer_notes'         => 'nullable|string|max:1000',
        ]);

        try {
            $feeBreakdown = $this->feeCalculator->calculate(
                (float) $data['estimated_product_price'],
                $data['size_tier'],
                (int) $data['quantity']
            );
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        if ($feeBreakdown['requires_manual_quote']) {
            $order = $this->createOrder($data, $feeBreakdown, 'under_review');
            $order->notify(new OrderNeedsManualQuoteNotification($order));

            return response()->json([
                'manual_quote' => true,
                'order_number' => $order->order_number,
                'redirect'     => route('order.manual-quote-confirmation'),
            ]);
        }

        $momoReference = 'momo_' . Str::random(16);

        $order = $this->createOrder($data, $feeBreakdown, 'pending', null, $momoReference);

        Payment::create([
            'order_id'                 => $order->id,
            'stripe_payment_intent_id' => $momoReference,
            'type'                     => 'initial',
            'amount'                   => $feeBreakdown['total_charged'],
            'currency'                 => 'usd',
            'status'                   => 'pending',
            'stripe_metadata'          => [
                'payment_method' => 'mobile_money',
                'note'           => 'Mobile Money payment pending customer support contact.',
            ],
        ]);

        try {
            $order->notify(new OrderConfirmedNotification($order));
        } catch (\Throwable $e) {
            Log::warning("Notification failed during Mobile Money order: " . $e->getMessage());
        }

        return response()->json([
            'success'  => true,
            'redirect' => route('order.success') . '?session_id=' . $momoReference,
        ]);
    }

    // ── Step 1: Fetch product info (dispatch queued job) ────────────────────────

    public function fetchProduct(Request $request): JsonResponse
    {
        $request->validate(['url' => 'required|url|max:2048']);

        $jobKey = Str::uuid()->toString();
        $url    = $request->input('url');

        // Return immediately with a job key; frontend polls for result
        FetchProductJob::dispatch($url, $jobKey);

        return response()->json(['job_key' => $jobKey]);
    }

    public function fetchProductResult(string $jobKey): JsonResponse
    {
        $result = Cache::get("fetch_job_{$jobKey}");

        if (! $result) {
            return response()->json(['status' => 'pending']);
        }

        return response()->json($result);
    }

    // ── Step 1: Calculate fees (server-side verification) ───────────────────────

    public function calculateFees(Request $request): JsonResponse
    {
        $data = $request->validate([
            'estimated_price' => 'required|numeric|min:0.01|max:99999',
            'size_tier'       => 'required|in:small,medium,large,oversized',
            'quantity'        => 'required|integer|min:1|max:100',
        ]);

        try {
            $breakdown = $this->feeCalculator->calculate(
                (float) $data['estimated_price'],
                $data['size_tier'],
                (int) $data['quantity']
            );
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json($breakdown);
    }

    // ── Step 3: Create Stripe Checkout Session ──────────────────────────────────

    public function createSession(Request $request): JsonResponse
    {
        $data = $request->validate([
            'platform_id'            => 'required|exists:platforms,id',
            'product_url'            => 'required|url|max:2048',
            'product_name'           => 'nullable|string|max:500',
            'product_image_url'      => 'nullable|url|max:2048',
            'estimated_product_price'=> 'required|numeric|min:0.01',
            'size_tier'              => 'required|in:small,medium,large,oversized',
            'shipping_method'        => 'nullable|string|in:express_air,standard_air,sea_freight',
            'product_weight'         => 'nullable|numeric|min:0.01|max:9999',
            'quantity'               => 'required|integer|min:1|max:100',
            'customer_name'          => 'required|string|max:200',
            'customer_email'         => 'required|email|max:200',
            'customer_phone'         => 'required|string|max:50',
            'shipping_address'       => 'required|array',
            'shipping_address.line1' => 'required|string|max:255',
            'shipping_address.line2' => 'nullable|string|max:255',
            'shipping_address.city'  => 'required|string|max:100',
            'shipping_address.state' => 'nullable|string|max:100',
            'shipping_address.postal_code' => 'required|string|max:20',
            'shipping_address.country'     => 'required|string|max:100',
            'customer_notes'         => 'nullable|string|max:1000',
        ]);

        // Re-verify fees server-side
        try {
            $feeBreakdown = $this->feeCalculator->calculate(
                (float) $data['estimated_product_price'],
                $data['size_tier'],
                (int) $data['quantity']
            );
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        // ── Oversized / manual-quote path ────────────────────────────────────────
        if ($feeBreakdown['requires_manual_quote']) {
            $order = $this->createOrder($data, $feeBreakdown, 'under_review');
            $order->notify(new OrderNeedsManualQuoteNotification($order));

            return response()->json([
                'manual_quote' => true,
                'order_number' => $order->order_number,
                'redirect'     => route('order.manual-quote-confirmation'),
            ]);
        }

        // ── Normal path: create Stripe Checkout Session ──────────────────────────
        $totalCents = (int) round($feeBreakdown['total_charged'] * 100);

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => [[
                'price_data' => [
                    'currency'     => 'usd',
                    'unit_amount'  => $totalCents,
                    'product_data' => [
                        'name'        => ($data['product_name'] ?? 'Product Purchase') . " (×{$data['quantity']})",
                        'description' => "Jubilee Direct forwarding service – Order includes product price + service fees",
                        'images'      => array_filter([$data['product_image_url'] ?? null]),
                    ],
                ],
                'quantity' => 1, // total already includes quantity
            ]],
            'mode'       => 'payment',
            'success_url' => route('order.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('order.cancel'),
            'customer_email' => $data['customer_email'],
            'metadata'   => [
                'order_data'   => json_encode($data),
                'fee_breakdown'=> json_encode($feeBreakdown),
            ],
        ]);

        // Store order data in session for post-webhook reference
        Cache::put("checkout_{$checkoutSession->id}", [
            'order_data'    => $data,
            'fee_breakdown' => $feeBreakdown,
        ], now()->addHours(2));

        return response()->json(['checkout_url' => $checkoutSession->url]);
    }

    // ── Stripe Webhook ───────────────────────────────────────────────────────────

    public function webhook(Request $request): \Illuminate\Http\Response
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('cashier.webhook.secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            Log::warning('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        match ($event->type) {
            'checkout.session.completed'   => $this->handleCheckoutCompleted($event->data->object),
            'payment_intent.succeeded'     => $this->handlePaymentIntentSucceeded($event->data->object),
            'charge.refunded'              => $this->handleChargeRefunded($event->data->object),
            default                        => null,
        };

        return response('OK', 200);
    }

    private function handleCheckoutCompleted(object $session): void
    {
        $cached = Cache::get("checkout_{$session->id}");

        if (! $cached) {
            Log::error("No cached order data for checkout session: {$session->id}");
            return;
        }

        $data         = $cached['order_data'];
        $feeBreakdown = $cached['fee_breakdown'];

        // Create order
        $order = $this->createOrder($data, $feeBreakdown, 'pending', $session->id, $session->payment_intent);

        // Record payment
        Payment::create([
            'order_id'                 => $order->id,
            'stripe_payment_intent_id' => $session->payment_intent,
            'type'                     => 'initial',
            'amount'                   => $feeBreakdown['total_charged'],
            'currency'                 => 'usd',
            'status'                   => 'succeeded',
        ]);

        // Send confirmation with magic link
        $order->notify(new OrderConfirmedNotification($order));

        // Clean up cache
        Cache::forget("checkout_{$session->id}");
    }

    private function handlePaymentIntentSucceeded(object $intent): void
    {
        // Handle additional charges (reconciliation)
        if (($intent->metadata['type'] ?? '') === 'additional_charge') {
            $order = Order::find($intent->metadata['order_id'] ?? null);
            if ($order) {
                Payment::where('stripe_payment_intent_id', $intent->id)
                    ->update(['status' => 'succeeded']);
                $order->update(['price_reconciliation_status' => 'resolved']);
            }
        }
    }

    private function handleChargeRefunded(object $charge): void
    {
        // Mark corresponding payment record
        Payment::where('stripe_payment_intent_id', $charge->id)
            ->update(['status' => 'refunded']);
    }

    // ── Success / Cancel ─────────────────────────────────────────────────────────

    public function success(Request $request): \Illuminate\View\View
    {
        $sessionId = $request->query('session_id');
        $order     = Order::where('stripe_session_id', $sessionId)
            ->orWhere('stripe_payment_intent_id', $sessionId)
            ->first();

        return view('order.success', compact('order'));
    }

    public function cancel(): \Illuminate\View\View
    {
        return view('order.cancel');
    }

    public function manualQuoteConfirmation(): \Illuminate\View\View
    {
        return view('order.manual-quote-confirmation');
    }

    // ── Helper: create order from data ───────────────────────────────────────────

    private function createOrder(
        array  $data,
        array  $feeBreakdown,
        string $status,
        ?string $stripeSessionId    = null,
        ?string $stripePaymentIntent = null,
    ): Order {
        $order = Order::create([
            'platform_id'              => $data['platform_id'] ?? null,
            'order_number'             => Order::generateOrderNumber(),
            'product_url'              => $data['product_url'],
            'product_name'             => $data['product_name'] ?? null,
            'product_image_url'        => $data['product_image_url'] ?? null,
            'source_platform'          => Order::detectPlatform($data['product_url']),
            'quantity'                 => $data['quantity'],
            'size_tier'                => $data['size_tier'],
            'shipping_method'          => $data['shipping_method'] ?? 'standard_air',
            'product_weight'           => isset($data['product_weight']) && $data['product_weight'] !== '' ? $data['product_weight'] : null,
            'estimated_product_price'  => $data['estimated_product_price'],
            'service_fee'              => $feeBreakdown['tier_fee'],
            'size_handling_fee'        => $feeBreakdown['size_fee'],
            'total_charged'            => $feeBreakdown['total_charged'],
            'customer_name'            => $data['customer_name'],
            'customer_email'           => $data['customer_email'],
            'customer_phone'           => $data['customer_phone'],
            'shipping_address'         => $data['shipping_address'],
            'customer_notes'           => $data['customer_notes'] ?? null,
            'status'                   => $status,
            'stripe_session_id'        => $stripeSessionId,
            'stripe_payment_intent_id' => $stripePaymentIntent,
        ]);

        // Record initial status history
        $order->statusHistory()->create([
            'status'     => $status,
            'note'       => 'Order created',
            'changed_by' => null,
        ]);

        return $order;
    }
}
