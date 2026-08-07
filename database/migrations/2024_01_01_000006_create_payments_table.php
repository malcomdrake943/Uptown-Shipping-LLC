<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('stripe_payment_intent_id')->unique();
            $table->enum('type', ['initial', 'additional', 'refund']);
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('usd');
            $table->string('status')->default('pending');
            $table->json('stripe_metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('order_id');
            $table->index('stripe_payment_intent_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
