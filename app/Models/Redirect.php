<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = [
        'from_url',
        'to_url',
        'status_code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'status_code' => 'integer',
        ];
    }

    /**
     * Найти активный редирект по URL
     */
    public static function findActiveRedirect(string $url): ?self
    {
        return static::where('from_url', $url)
            ->where('is_active', true)
            ->first();
    }
}
