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
     */
    public function applySeo(): void
    {
        $meta = $this->seoMeta;

        if (!$meta) {
            return;
        }

        // Основные
        if ($meta->meta_title) {
            Seo::setMetaTitle($meta->meta_title);
        }

        if ($meta->meta_description) {
            Seo::setMetaDescription($meta->meta_description);
        }

        if ($meta->meta_keywords) {
            Seo::setMetaKeywords($meta->meta_keywords);
        }

        if ($meta->canonical) {
            Seo::setCanonical($meta->canonical);
        }

        // Robots
        Seo::setNoIndex($meta->noindex);

        // OpenGraph
        if ($meta->og_title) {
            Seo::setOgTitle($meta->og_title);
        }
        if ($meta->og_description) {
            Seo::setOgDescription($meta->og_description);
        }
        if ($meta->og_image) {
            Seo::setOgImage($meta->og_image);
        }
        if ($meta->og_type) {
            Seo::setOgType($meta->og_type);
        }

        // Twitter
        if ($meta->twitter_card) {
            Seo::setTwitterCard($meta->twitter_card);
        }
        if ($meta->twitter_title) {
            Seo::setTwitterTitle($meta->twitter_title);
        }
        if ($meta->twitter_description) {
            Seo::setTwitterDescription($meta->twitter_description);
        }
        if ($meta->twitter_image) {
            Seo::setTwitterImage($meta->twitter_image);
        }

        // Если что-то доп. лежит в extra — тут можно кастомно обработать
        // $meta->extra ...
    }
}
