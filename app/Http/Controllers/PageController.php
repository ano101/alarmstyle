<?php

namespace App\Http\Controllers;

use App\Facades\Seo;
use App\Models\Page;
use App\Models\Slug;
use App\Support\Blocks\BlocksResolver;
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

        $blocks = $blocksResolver->resolve($page->blocksForRender ?? $page->blocks ?? []);

        return Inertia::render('Page/Show', [
            'page' => [
                'title' => $page->title,
                'slug' => $page->slug,
                'blocks' => $blocks,
            ],
        ]);
    }
}
