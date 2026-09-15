<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_method')->default('standard_air')->after('size_tier')->comment('express_air (3-7 Days), standard_air (7-14 Days), sea_freight (4-8 Weeks)');
            $table->decimal('product_weight', 8, 2)->nullable()->after('shipping_method')->comment('Weight in kg/lbs for shipping calculation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_method', 'product_weight']);
        });
    }
};
