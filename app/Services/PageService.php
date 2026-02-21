<?php

namespace App\Services;

use App\Facades\JsonLd;
use App\Facades\Seo;
use App\Models\Page;
use App\Support\Breadcrumbs\Breadcrumbs;
use Illuminate\Http\Request;

class PageService
{
    /**
     * Применить SEO и OG/Twitter fallback для страницы.
     */
    public function applySeo(Page $page, Request $request): void
    {
        $vars = [
            'title' => $page->title ?? '',
        ];

        $page->applySeo('page', $vars);

        Seo::setCanonicalIfEmpty($request->fullUrl());

        Seo::setOgTitleIfEmpty($page->title);
        Seo::setOgDescriptionIfEmpty($page->meta_description ?? 'Посетите нашу страницу для получения подробной информации');
        Seo::setOgTypeIfEmpty('website');

        Seo::setTwitterTitleIfEmpty($page->title);
        Seo::setTwitterDescriptionIfEmpty($page->meta_description ?? 'Посетите нашу страницу для получения подробной информации');
        Seo::setTwitterCardIfEmpty('summary');
    }

    /**
     * Добавить JSON-LD разметку для страницы (WebPage / WebSite).
     * Для НЕ главной страницы также добавляет BreadcrumbList.
     *
     * @param  array<int, array{label: string, url: string|null}>|null  $breadcrumbs
     */
    public function applyJsonLd(Page $page, Request $request, ?array $breadcrumbs): void
    {
        if ($breadcrumbs) {
            $this->applyBreadcrumbsJsonLd($breadcrumbs, $request);
        }

        $webPageSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => Seo::getTitle() ?? $page->title,
            'description' => Seo::getDescription() ?? $page->meta_description,
            'url' => $request->fullUrl(),
        ];

        if ($page->is_homepage) {
            $webPageSchema['@type'] = 'WebSite';
            $webPageSchema['name'] = config('app.name', 'AlarmStyle');
            $webPageSchema['url'] = url('/');
            $webPageSchema['potentialAction'] = [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => url('/').'?search={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ];
        }

        JsonLd::add($webPageSchema);
    }

    /**
     * Получить хлебные крошки для страницы (null для главной).
     *
     * @return array<int, array{label: string, url: string|null}>|null
     */
    public function getBreadcrumbs(Page $page): ?array
    {
        if ($page->is_homepage) {
            return null;
        }

        return Breadcrumbs::page($page);
    }

    /**
     * Добавить JSON-LD BreadcrumbList.
     *
     * @param  array<int, array{label: string, url: string|null}>  $breadcrumbs
     */
    protected function applyBreadcrumbsJsonLd(array $breadcrumbs, Request $request): void
    {
        $breadcrumbItems = [];
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['label'],
                'item' => $breadcrumb['url'] ?? $request->fullUrl(),
            ];
        }

        if (! empty($breadcrumbItems)) {
            JsonLd::add([
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $breadcrumbItems,
            ]);
        }
    }
}
