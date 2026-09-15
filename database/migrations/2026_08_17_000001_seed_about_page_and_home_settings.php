<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            [
                'key'        => 'home_about_title',
                'label'      => 'Main Page About Section - Title',
                'value'      => 'Shopping Internationally Made Effortless',
                'group'      => 'home_page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'home_about_subtitle',
                'label'      => 'Main Page About Section - Subtitle',
                'value'      => 'We buy products directly from top global stores and deliver them straight to your doorstep.',
                'group'      => 'home_page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'home_about_content',
                'label'      => 'Main Page About Section - Description',
                'value'      => 'Jubilee Direct bridges the gap between international retailers and global shoppers. Simply provide a link from Amazon, eBay, or any major online store, and our procurement team will securely handle payment, customs clearance, and fast door-to-door delivery with total fee transparency.',
                'group'      => 'home_page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'home_about_visible',
                'label'      => 'Show About Section on Main Page',
                'value'      => 'true',
                'group'      => 'home_page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'about_title',
                'label'      => 'About Us Page - Main Title',
                'value'      => 'About Jubilee Direct',
                'group'      => 'about_page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'about_subtitle',
                'label'      => 'About Us Page - Hero Subtitle',
                'value'      => 'Connecting shoppers worldwide with global e-commerce stores.',
                'group'      => 'about_page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'about_content',
                'label'      => 'About Us Page - Story & Overview',
                'value'      => "Jubilee Direct was founded with a clear vision: to eliminate cross-border shopping barriers. International e-commerce is often complicated by payment restrictions, complex shipping policies, and hidden duties.\n\nWe simplify everything into a single, intuitive platform. Customers submit product links from leading global marketplaces, and Jubilee Direct manages the entire lifecycle—from verified procurement and secure cross-border payment processing to package consolidation and reliable final-mile delivery.",
                'group'      => 'about_page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'about_mission',
                'label'      => 'About Us Page - Mission Statement',
                'value'      => 'To make global products accessible to anyone, anywhere by offering transparent pricing, secure procurement, and seamless doorstep delivery.',
                'group'      => 'about_page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'about_vision',
                'label'      => 'About Us Page - Vision Statement',
                'value'      => 'To become the premier global purchase-forwarding service trusted by millions for cross-border e-commerce solutions.',
                'group'      => 'about_page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')
            ->whereIn('group', ['home_page', 'about_page'])
            ->delete();
    }
};
