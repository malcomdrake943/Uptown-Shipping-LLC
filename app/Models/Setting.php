<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label',
        'value',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting && !is_null($setting->value) ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value, ?string $label = null): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            array_filter([
                'value' => $value,
                'label' => $label,
            ], fn ($val) => !is_null($val))
        );
    }
}
