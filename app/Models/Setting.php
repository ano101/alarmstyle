<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    protected static function booted()
    {
        // Сброс кеша при любом обновлении/создании
        static::saved(function () {
            Cache::forget('settings.all');
        });

        // Сброс кеша при удалении
        static::deleted(function () {
            Cache::forget('settings.all');
        });
    }

    public static function get(string $key, $default = null)
    {
        $settings = self::allCached();

        return $settings[$key]['value'] ?? $settings[$key]['data'] ?? $default;
    }

    public static function getData(string $key, $default = null)
    {
        $settings = self::allCached();

        return $settings[$key]['data'] ?? $default;
    }

    public static function set(string $key, $value = null, $data = null): self
    {
        $model = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'data' => $data],
        );

        Cache::forget('settings.all');

        return $model;
    }

    public static function allCached(): array
    {
        return Cache::rememberForever('settings.all', function () {
            return static::query()
                ->get()
                ->keyBy('key')
                ->map(fn (self $s) => [
                    'value' => $s->value,
                    'data'  => $s->data,
                ])
                ->toArray();
        });
    }
}
