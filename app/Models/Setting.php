<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'label',
        'value',
        'group',
    ];

    /**
     * Get setting value by key.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $setting = static::where('key', $key)->first();
        if ($setting && ! is_null($setting->value) && $setting->value !== '') {
            return $setting->value;
        }

        return $default;
    }

    /**
     * Set/update setting value by key.
     */
    public static function set(string $key, ?string $value, ?string $label = null, string $group = 'general'): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'label' => $label ?? $key,
                'group' => $group,
            ]
        );
    }
}
