<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Order extends Model
{
    use Notifiable;

    protected $fillable = [
        'platform_id',
        'order_number',
        'product_url',
        'product_name',
        'product_image_url',
        'source_platform',
        'quantity',
        'size_tier',
        'estimated_product_price',
        'final_product_price',
        'service_fee',
        'size_handling_fee',
        'total_charged',
        'price_reconciliation_status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'customer_notes',
        'status',
        'tracking_number',
        'tracking_carrier',
        'handled_by',
        'stripe_session_id',
        'stripe_payment_intent_id',
    ];

    protected $casts = [
        'shipping_address'          => 'array',
        'estimated_product_price'   => 'decimal:2',
        'final_product_price'       => 'decimal:2',
        'service_fee'               => 'decimal:2',
        'size_handling_fee'         => 'decimal:2',
        'total_charged'             => 'decimal:2',
        'quantity'                  => 'integer',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Generate a unique human-readable order number like PP-1042.
     */
    public static function generateOrderNumber(): string
    {
        do {
            $number = 'PP-' . random_int(1000, 9999);
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    /**
     * Detect platform from URL host.
     */
    public static function detectPlatform(string $url): string
    {
        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
        if (str_contains($host, 'amazon')) return 'amazon';
        if (str_contains($host, 'ebay'))   return 'ebay';
        return 'other';
    }

    /**
     * Route notifications to customer email.
     */
    public function routeNotificationForMail(): string
    {
        return $this->customer_email;
    }

    /**
     * Record a status change with optional note and user.
     */
    public function recordStatusChange(string $status, ?string $note = null, ?int $changedBy = null): void
    {
        $this->update(['status' => $status]);
        $this->statusHistory()->create([
            'status'     => $status,
            'note'       => $note,
            'changed_by' => $changedBy,
        ]);
    }

    /**
     * Check if the shipping address is in a given country code.
     */
    public function shippingCountry(): string
    {
        return $this->shipping_address['country'] ?? '';
    }
}
