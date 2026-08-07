<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_rules', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_price', 10, 2)->default(0);
            $table->decimal('max_price', 10, 2)->nullable()->comment('null = unbounded');
            $table->enum('fee_type', ['flat', 'percentage']);
            $table->decimal('fee_value', 10, 2);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['min_price', 'max_price']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_rules');
    }
};
