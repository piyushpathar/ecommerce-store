<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
    ];

    /** All stored settings, loaded once per request. */
    protected static ?array $cache = null;

    /**
     * Stored value, else the caller's default, else config('store.defaults').
     * Array defaults are returned JSON-encoded, matching how they are stored.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        static::$cache ??= static::query()->pluck('value', 'key')->all();

        if (isset(static::$cache[$key])) {
            return static::$cache[$key];
        }

        $fallback = $default ?? config("store.defaults.{$key}");

        return is_array($fallback)
            ? json_encode($fallback, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            : $fallback;
    }

    /** Decoded JSON setting, or [] when missing or invalid. */
    public static function json(string $key): array
    {
        $decoded = json_decode((string) static::get($key), true);
        return is_array($decoded) ? $decoded : [];
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        static::flushCache();
    }

    public static function flushCache(): void
    {
        static::$cache = null;
    }
}
