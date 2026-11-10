<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/** Key/value site settings (brand, contact, tracking, keys). Read with the setting() helper. */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    private static ?array $memo = null;

    public static function everything(): array
    {
        return self::$memo ??= Cache::rememberForever('settings.all', fn () => self::query()->pluck('value', 'key')->all());
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $v = self::everything()[$key] ?? null;

        return ($v === null || $v === '') ? $default : $v;
    }

    public static function put(string $key, ?string $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        self::flush();
    }

    public static function flush(): void
    {
        self::$memo = null;
        Cache::forget('settings.all');
    }
}
