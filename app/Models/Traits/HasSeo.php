<?php

namespace App\Models\Traits;

use App\Facades\Seo;
use App\Models\SeoMeta;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSeo
{
    public static function bootHasSeo(): void
    {
        static::deleting(function ($model) {
            // при удалении модели удаляем seo-запись
            if (method_exists($model, 'seoMeta')) {
                $model->seoMeta()->delete();
            }
        });
    }

    public function seoMeta(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'metaable');
    }

    /**
     * Быстрый апдейт/создание seo.
     */
    public function setSeo(array $data): SeoMeta
    {
        $fillable = (new SeoMeta())->getFillable();

        $payload = array_intersect_key($data, array_flip($fillable));

        return $this->seoMeta()->updateOrCreate([], $payload);
    }

    /**
     * Применить SEO этой модели к глобальному Seo-сервису (фасад Seo).
     * Использует SeoApplier для унифицированной логики.
     *
     * @param string $context Контекст для маски ('product', 'page', и т.д.)
     * @param array $vars Переменные для маски
     * @param bool $useMask Использовать ли маски для добивки пустых полей
     */
    public function applySeo(string $context = 'default', array $vars = [], bool $useMask = true): void
    {
        $applier = app(\App\Services\SeoApplier::class);

        $applier->apply(
            model: $this,
            context: $context,
            vars: $vars,
            useMask: $useMask
        );
    }
}
