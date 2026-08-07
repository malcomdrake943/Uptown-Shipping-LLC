<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique()->comment('Human-readable e.g. PP-1042');

            // Product info
            $table->text('product_url');
            $table->string('product_name')->nullable();
            $table->text('product_image_url')->nullable();
            $table->enum('source_platform', ['amazon', 'ebay', 'other'])->default('other');
            $table->unsignedInteger('quantity')->default(1);
            $table->enum('size_tier', ['small', 'medium', 'large', 'oversized']);

            // Pricing
            $table->decimal('estimated_product_price', 10, 2);
            $table->decimal('final_product_price', 10, 2)->nullable()->comment('Set by admin after actual purchase');
            $table->decimal('service_fee', 10, 2);
            $table->decimal('size_handling_fee', 10, 2);
            $table->decimal('total_charged', 10, 2);
            $table->enum('price_reconciliation_status', ['none', 'refund_due', 'additional_payment_due', 'resolved'])->default('none');

            // Customer info
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->json('shipping_address')->comment('Keys: line1, line2, city, state, postal_code, country');
            $table->text('customer_notes')->nullable();

            // Status & tracking
            $table->enum('status', ['pending', 'under_review', 'purchased', 'shipped', 'delivered', 'cancelled', 'refunded'])->default('pending');
            $table->string('tracking_number')->nullable();
            $table->string('tracking_carrier')->nullable();

            // Admin
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();

            // Stripe
            $table->string('stripe_session_id')->nullable()->index();
            $table->string('stripe_payment_intent_id')->nullable()->index();

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('source_platform');
            $table->index('size_tier');
            $table->index('customer_email');
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
