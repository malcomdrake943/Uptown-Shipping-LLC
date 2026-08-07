<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SizeFeeRule extends Model
{
    protected $fillable = [
        'size_tier',
        'flat_fee',
        'requires_manual_quote',
    ];

    protected $casts = [
        'flat_fee' => 'decimal:2',
        'requires_manual_quote' => 'boolean',
    ];

    public static function findForTier(string $tier): ?self
    {
        return static::where('size_tier', $tier)->first();
    }
}
