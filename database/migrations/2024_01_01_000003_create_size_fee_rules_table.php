<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('size_fee_rules', function (Blueprint $table) {
            $table->id();
            $table->enum('size_tier', ['small', 'medium', 'large', 'oversized'])->unique();
            $table->decimal('flat_fee', 10, 2)->default(0);
            $table->boolean('requires_manual_quote')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('size_fee_rules');
    }
};
