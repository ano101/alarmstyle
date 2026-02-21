<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Slug;
use App\Services\PageService;
use App\Support\Blocks\BlocksResolver;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    public function show(BlocksResolver $blocksResolver, PageService $pageService, Request $request, ?string $slug = '')
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

        $pageService->applySeo($page, $request);

        $breadcrumbs = $pageService->getBreadcrumbs($page);

        $pageService->applyJsonLd($page, $request, $breadcrumbs);

        $blocks = $blocksResolver->resolve($page->blocksForRender ?? $page->blocks ?? []);

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
