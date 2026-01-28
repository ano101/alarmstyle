<?php

namespace App\Services;

use App\Facades\Seo;
use App\Models\SeoMask;
use Illuminate\Database\Eloquent\Model;

class SeoApplier
{
    /**
     * Применить SEO к глобальному SeoService (через фасад Seo):
     * 1) сначала seoMeta (явные значения)
     * 2) затем, если useMask=true, добивка из SeoMask (только пустые поля)
     *
     * $context:
     *  - 'product'
     *  - 'catalog_category'
     *  - 'landing'
     *  - и т.д. (как ты хранишь в seo_masks.context)
     *
     * $vars:
     *  - переменные для масок: ['name' => ..., 'price' => ...]
     *
     * $maskModel:
     *  - если хочешь искать specific mask не по текущей модели, а по другой
     *    (пример: контекст catalog_category, но specific mask привязана к Category)
     */
    public function apply(
        Model $model,
        string $context,
        array $vars = [],
        bool $useMask = true,
        ?Model $maskModel = null
    ): void {
        $meta = $this->getSeoMeta($model);

        // =========================
        // 1) SEO META (явные значения)
        // =========================
        if ($meta) {
            if (! empty($meta->meta_title)) {
                Seo::setMetaTitle($meta->meta_title);
            }
            if (! empty($meta->h1)) {
                Seo::setH1($meta->h1);
            }
            if (! empty($meta->meta_description)) {
                Seo::setMetaDescription($meta->meta_description);
            }
            if (! empty($meta->meta_keywords)) {
                Seo::setMetaKeywords($meta->meta_keywords);
            }
            if (! empty($meta->canonical)) {
                Seo::setCanonical($meta->canonical);
            }

            Seo::setNoIndex((bool) $meta->noindex);

            // OpenGraph
            if (! empty($meta->og_title)) {
                Seo::setOgTitle($meta->og_title);
            }
            if (! empty($meta->og_description)) {
                Seo::setOgDescription($meta->og_description);
            }
            if (! empty($meta->og_image)) {
                Seo::setOgImage($meta->og_image);
            }
            if (! empty($meta->og_type)) {
                Seo::setOgType($meta->og_type);
            }

            // Twitter
            if (! empty($meta->twitter_card)) {
                Seo::setTwitterCard($meta->twitter_card);
            }
            if (! empty($meta->twitter_title)) {
                Seo::setTwitterTitle($meta->twitter_title);
            }
            if (! empty($meta->twitter_description)) {
                Seo::setTwitterDescription($meta->twitter_description);
            }
            if (! empty($meta->twitter_image)) {
                Seo::setTwitterImage($meta->twitter_image);
            }
        }

        // =========================
        // 2) SEO MASK (добивка пустых полей)
        // =========================
        if (! $useMask) {
            return;
        }

        $maskOwner = $maskModel ?? $model;
        $mask = SeoMask::resolveFor($context, $maskOwner);

        if (! $mask) {
            return;
        }

        // Рендерим маски
        $maskTitle = $this->renderSeoMask($mask->meta_title_pattern ?? null, $vars);
        $maskH1 = $this->renderSeoMask($mask->meta_h1_pattern ?? null, $vars);
        $maskDesc = $this->renderSeoMask($mask->meta_description_pattern ?? null, $vars);

        // Добиваем пустые поля (важно: только IfEmpty)
        Seo::setMetaTitleIfEmpty($maskTitle);
        Seo::setH1IfEmpty($maskH1);
        Seo::setMetaDescriptionIfEmpty($maskDesc);

        // OG/Twitter — добивка
        // Если ты не заводил отдельные og_*_pattern — логично наследовать из meta масок
        Seo::setOgTitleIfEmpty($maskTitle);
        Seo::setOgDescriptionIfEmpty($maskDesc);

        Seo::setTwitterTitleIfEmpty($maskTitle);
        Seo::setTwitterDescriptionIfEmpty($maskDesc);

        // если захочешь — можно так же добивать изображения из vars:
        // Seo::setOgImageIfEmpty($vars['og_image'] ?? null);
        // Seo::setTwitterImageIfEmpty($vars['twitter_image'] ?? null);
    }

    /**
     * Безопасно получить seoMeta (если у модели есть relationship seoMeta()).
     */
    protected function getSeoMeta(Model $model)
    {
        if (! method_exists($model, 'seoMeta')) {
            return null;
        }

        return $model->seoMeta;
    }

    /**
     * Рендер маски: {key} -> значение, остатки {xxx} чистим, лишние пробелы убираем.
     */
    protected function renderSeoMask(?string $pattern, array $vars): ?string
    {
        if (! $pattern) {
            return null;
        }

        $result = $pattern;

        foreach ($vars as $key => $value) {
            $result = str_replace('{'.$key.'}', (string) $value, $result);
        }

        // убрать плейсхолдеры, которые не были заменены
        $result = preg_replace('/\{[a-z0-9_]+\}/i', '', $result);

        // схлопнуть пробелы
        $result = preg_replace('/\s{2,}/u', ' ', trim($result));

        return $result === '' ? null : $result;
    }
}
