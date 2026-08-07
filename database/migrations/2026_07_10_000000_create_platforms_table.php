<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('logo'); // path to logo image
            $table->string('url');  // URL for "Visit Store" button
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('platform_id')->nullable()->after('id')->constrained('platforms')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['platform_id']);
            $table->dropColumn('platform_id');
        });

        Schema::dropIfExists('platforms');
    }
};
