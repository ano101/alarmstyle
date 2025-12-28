<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMask extends Model
{
    protected $table = 'seo_masks';

    protected $fillable = [
        'context',
        'maskable_type',
        'maskable_id',
        'meta_title_pattern',
        'meta_description_pattern',
        'meta_h1_pattern',
        'extra',
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    public function maskable(): MorphTo
    {
        return $this->morphTo();
    }

    /* =========================
     * Scopes / helpers
     * ========================= */

    /**
     * Маски для заданного контекста (например, "catalog_category").
     */
    public function scopeForContext($query, string $context)
    {
        return $query->where('context', $context);
    }

    /**
     * Найти маску:
     *  - сначала для конкретной модели (maskable_type + maskable_id),
     *  - потом глобальную по контексту (maskable_type / id NULL).
     */
    public static function resolveFor(string $context, ?Model $model = null): ?self
    {
        $query = static::query()->forContext($context);

        if ($model) {
            // Пытаемся найти маску для конкретной модели
            $specific = (clone $query)
                ->where('maskable_type', get_class($model))
                ->where('maskable_id', $model->getKey())
                ->first();

            if ($specific) {
                return $specific;
            }
        }

        // Глобальная маска для контекста
        return $query
            ->whereNull('maskable_type')
            ->whereNull('maskable_id')
            ->first();
    }
}
