<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeRule extends Model
{
    protected $fillable = [
        'min_price',
        'max_price',
        'fee_type',
        'fee_value',
        'sort_order',
    ];

    protected $casts = [
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'fee_value' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    /**
     * Find the matching fee rule for a given price.
     */
    public static function findForPrice(float $price): ?self
    {
        return static::query()
            ->where('min_price', '<=', $price)
            ->where(function ($q) use ($price) {
                $q->whereNull('max_price')
                  ->orWhere('max_price', '>=', $price);
            })
            ->orderBy('sort_order')
            ->orderBy('min_price', 'desc')
            ->first();
    }
}
