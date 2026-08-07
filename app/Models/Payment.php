<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'stripe_payment_intent_id',
        'type',
        'amount',
        'currency',
        'status',
        'stripe_metadata',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'stripe_metadata' => 'array',
        'created_at'      => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
