<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'group', 'type', 'label', 'description'];

    /**
     * Get a setting value by key with caching.
     */
    public static function get(string $key, $default = null)
    {
        $setting = Cache::rememberForever("setting.{$key}", function () use ($key) {
            return self::where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, $value)
    {
        $setting = self::where('key', $key)->first();
        if ($setting) {
            $setting->update(['value' => (string) $value]);
            Cache::forget("setting.{$key}");
        }
    }

    /**
     * Cast value to its appropriate type.
     */
    protected static function castValue($value, $type)
    {
        return match ($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'decimal' => (float) $value,
            default => $value,
        };
    }
}
