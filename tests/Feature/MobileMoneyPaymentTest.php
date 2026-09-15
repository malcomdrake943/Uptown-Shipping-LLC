<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Platform;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileMoneyPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic fee rules for fee calculator
        \Illuminate\Support\Facades\DB::table('fee_rules')->insert([
            'min_price' => 0.01,
            'max_price' => null,
            'fee_type' => 'percentage',
            'fee_value' => 10.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \Illuminate\Support\Facades\DB::table('size_fee_rules')->insert([
            'size_tier' => 'medium',
            'flat_fee' => 5.00,
            'requires_manual_quote' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Setting::set('mobile_money_phone', '+1 (555) 987-6543', 'Mobile Money Phone Number');
    }

    public function font_test_setting_model_stores_and_retrieves_phone_number(): void
    {
        $phone = Setting::get('mobile_money_phone');
        $this->assertEquals('+1 (555) 987-6543', $phone);

        Setting::set('mobile_money_phone', '+1 (800) 123-4567');
        $this->assertEquals('+1 (800) 123-4567', Setting::get('mobile_money_phone'));
    }

    public function test_order_index_page_loads_dynamic_support_phone_number(): void
    {
        Setting::set('mobile_money_phone', '+1 (999) 888-7777');

        $response = $this->get(route('order.index'));

        $response->assertStatus(200);
        $response->assertSee('+1 (999) 888-7777');
        $response->assertSee('Mobile Money');
        $response->assertSee('To Pay with mobile money, contact our customer support team on this number');
    }

    public function test_mobile_money_payment_processing_creates_pending_order(): void
    {
        $platform = Platform::create([
            'name' => 'Test Store',
            'url' => 'https://teststore.com',
            'logo' => 'platforms/test.png',
            'is_active' => true,
        ]);

        $payload = [
            'platform_id' => $platform->id,
            'product_url' => 'https://teststore.com/item123',
            'product_name' => 'Sample Test Product',
            'estimated_product_price' => 50.00,
            'size_tier' => 'medium',
            'quantity' => 1,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+15550001111',
            'shipping_address' => [
                'line1' => '123 Main St',
                'city' => 'Metropolis',
                'postal_code' => '10001',
                'country' => 'United States',
            ],
        ];

        $response = $this->postJson(route('order.mobile-money'), $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'redirect']);

        $this->assertDatabaseHas('orders', [
            'customer_email' => 'john@example.com',
            'status' => 'pending',
            'size_tier' => 'medium',
        ]);

        $order = Order::where('customer_email', 'john@example.com')->first();
        $this->assertNotNull($order);

        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('pending', $payment->status);
        $this->assertEquals('mobile_money', $payment->stripe_metadata['payment_method'] ?? null);
    }
}
