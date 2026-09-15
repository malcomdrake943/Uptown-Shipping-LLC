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
                'key'        => 'contact_title',
                'label'      => 'Contact Us Page - Main Title',
                'value'      => 'Get in Touch with UpTown Services',
                'group'      => 'contact_page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'contact_subtitle',
                'label'      => 'Contact Us Page - Hero Subtitle',
                'value'      => 'Have questions about an order, shipping options, or custom quotes? Our team is here to assist you.',
                'group'      => 'contact_page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'contact_email',
                'label'      => 'Contact Us Page - Support Email',
                'value'      => 'support@uptownservices.net',
                'group'      => 'contact_page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'contact_phone',
                'label'      => 'Contact Us Page - Support Phone',
                'value'      => '+1 (555) 123-4567',
                'group'      => 'contact_page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'contact_whatsapp',
                'label'      => 'Contact Us Page - WhatsApp Number',
                'value'      => '+1 (555) 123-4567',
                'group'      => 'contact_page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'contact_address',
                'label'      => 'Contact Us Page - Office Address',
                'value'      => '123 Commerce Boulevard, Suite 400, New York, NY 10001, United States',
                'group'      => 'contact_page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'contact_working_hours',
                'label'      => 'Contact Us Page - Business Hours',
                'value'      => "Monday – Friday: 9:00 AM – 6:00 PM EST\nSaturday: 10:00 AM – 4:00 PM EST\nSunday: Closed",
                'group'      => 'contact_page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'contact_form_intro',
                'label'      => 'Contact Us Page - Form Intro / Description',
                'value'      => 'Send us a message and our procurement specialists will get back to you within 24 hours.',
                'group'      => 'contact_page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'contact_shipping_info',
                'label'      => 'Contact Us Page - Shipping Methods Info',
                'value'      => "We offer transparent cross-border shipping solutions tailored to your cargo size and urgency:\n\n• Express Air (3–7 Days) – Expedited courier for fast doorstep delivery\n• Standard Air (7–14 Days) – Reliable and economical global air freight\n• Sea Freight (4–8 Weeks) – Best value for bulk, heavy, or oversized items\n\nOur system calculates the exact shipping charge after you submit your product link and package specifications.",
                'group'      => 'contact_page',
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
            ->where('group', 'contact_page')
            ->delete();
    }
};
