<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label')->nullable();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        // Insert initial setting for support/mobile money phone number
        DB::table('settings')->insert([
            [
                'key'        => 'mobile_money_phone',
                'label'      => 'Mobile Money Support Phone Number',
                'value'      => '+1 (800) 555-0199',
                'group'      => 'payment',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'support_phone',
                'label'      => 'General Support Phone Number',
                'value'      => '+1 (800) 555-0199',
                'group'      => 'general',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
