<?php

namespace App\Http\Controllers;

use App\Facades\JsonLd;
use App\Facades\Seo;
use App\Models\Page;
use App\Models\Slug;
use App\Support\Blocks\BlocksResolver;
use App\Support\Breadcrumbs\Breadcrumbs;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    public function show(BlocksResolver $blocksResolver, Request $request, ?string $slug = '')
    {
        // Если слаг пустой, ищем главную страницу
        if ($slug === '' || $slug === null) {
            $page = Page::where('is_homepage', true)
                ->where('is_published', true)
                ->firstOrFail();
        } else {
            $slugModel = Slug::where('slug', $slug)
                ->where('sluggable_type', Page::class)
                ->with('sluggable')
                ->firstOrFail();

            /** @var Page $page */
            $page = $slugModel->sluggable;

            if (! $page->is_published) {
                abort(404);
            }
        }

        $page->load('seoMeta');

        // Применяем SEO с контекстом 'page' и переменными для масок
        $vars = [
            'title' => $page->title ?? '',
            // добавьте другие переменные по необходимости
        ];

        $page->applySeo('page', $vars);

        // Устанавливаем канонический URL на саму страницу
        Seo::setCanonicalIfEmpty($request->fullUrl());

        // Fallback для OG и Twitter метатегов, если не заполнены в seoMeta
        // Для статических страниц обычно нет изображений, но можно добавить логотип или дефолтное изображение

        // OG fallback
        Seo::setOgTitleIfEmpty($page->title);
        Seo::setOgDescriptionIfEmpty($page->meta_description ?? 'Посетите нашу страницу для получения подробной информации');
        Seo::setOgTypeIfEmpty('website');

        // Twitter fallback
        Seo::setTwitterTitleIfEmpty($page->title);
        Seo::setTwitterDescriptionIfEmpty($page->meta_description ?? 'Посетите нашу страницу для получения подробной информации');
        Seo::setTwitterCardIfEmpty('summary');

        $blocks = $blocksResolver->resolve($page->blocksForRender ?? $page->blocks ?? []);

        // Формируем хлебные крошки только для НЕ главной страницы
        $breadcrumbs = null;
        if (! $page->is_homepage) {
            $breadcrumbs = Breadcrumbs::page($page);

            // JSON-LD BreadcrumbList для обычных страниц (не главной)
            if ($breadcrumbs) {
                $breadcrumbItems = [];
                foreach ($breadcrumbs as $index => $breadcrumb) {
                    $breadcrumbItems[] = [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'name' => $breadcrumb['label'],
                        'item' => $breadcrumb['url'] ?? $request->fullUrl(),
                    ];
                }

                if (count($breadcrumbItems) > 0) {
                    JsonLd::add([
                        '@context' => 'https://schema.org',
                        '@type' => 'BreadcrumbList',
                        'itemListElement' => $breadcrumbItems,
                    ]);
                }
            }
        }

        // JSON-LD WebPage разметка
        $webPageSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => Seo::getTitle() ?? $page->title,
            'description' => Seo::getDescription() ?? $page->meta_description,
            'url' => $request->fullUrl(),
        ];

        // Добавляем главную страницу как WebSite с SearchAction
        if ($page->is_homepage) {
            $webPageSchema['@type'] = 'WebSite';
            $webPageSchema['name'] = config('app.name', 'AlarmStyle');
            $webPageSchema['url'] = url('/');

            // Добавляем SearchAction для поисковой строки
            $webPageSchema['potentialAction'] = [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => url('/') . '?search={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ];
        }

        JsonLd::add($webPageSchema);


        return Inertia::render('Page/Show', [
            'page' => [
                'title' => $page->title,
                'slug' => $page->slug,
                'blocks' => $blocks,
            ],
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
