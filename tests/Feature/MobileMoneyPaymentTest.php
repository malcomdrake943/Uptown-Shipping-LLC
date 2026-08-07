<?php

namespace Tests\Feature;

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

        $this->seed();
    }

    public function test_setting_model_can_get_and_set_values(): void
    {
        Setting::set('support_phone', '+233-24-123-4567', 'Support Phone');

        $this->assertEquals('+233-24-123-4567', Setting::get('support_phone'));
    }

    public function test_checkout_page_displays_admin_support_phone_number(): void
    {
        Setting::set('support_phone', '+233-55-987-6543', 'Support Phone');

        $response = $this->get('/order');

        $response->assertStatus(200);
        $response->assertSee('+233-55-987-6543');
        $response->assertSee('To Pay with mobile money, contact our customer support team on this number');
    }

    public function test_order_creation_with_mobile_money_payment(): void
    {
        $platform = Platform::first();

        $payload = [
            'platform_id' => $platform->id,
            'payment_method_type' => 'momo',
            'product_url' => 'https://amazon.com/dp/B08N5WRWNW',
            'product_name' => 'Test Product',
            'estimated_product_price' => 50.00,
            'size_tier' => 'small',
            'quantity' => 1,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '1234567890',
            'shipping_address' => [
                'line1' => '123 Main St',
                'city' => 'Accra',
                'postal_code' => '00233',
                'country' => 'Ghana',
            ],
        ];

        $response = $this->postJson(route('order.charge'), $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'redirect']);
        $this->assertDatabaseHas('orders', [
            'customer_email' => 'john@example.com',
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('payments', [
            'status' => 'pending_momo',
        ]);
    }
}
