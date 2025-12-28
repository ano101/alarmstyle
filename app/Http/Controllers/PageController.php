<?php

namespace App\Http\Controllers;

use App\Facades\Seo;
use App\Models\Page;
use App\Models\Slug;
use Illuminate\Http\Request;
use App\Support\Blocks\BlocksResolver;
use Inertia\Inertia;

class PageController extends Controller
{
    public function show(BlocksResolver $blocksResolver, Request $request, ?string $slug  = '')
    {
        $slugModel = Slug::where('slug', $slug)
            ->where('sluggable_type', Page::class)
            ->with('sluggable')
            ->firstOrFail();

        /** @var Page $page */
        $page = $slugModel->sluggable;

        if (! $page->is_published) {
            abort(404);
        }

        $page->load('seoMeta');
        $page->applySeo();

        $blocks = $blocksResolver->resolve($page->blocksForRender ?? $page->blocks ?? []);

        return Inertia::render('Page/Show', [
            'page' => [
                'title'  => $page->title,
                'slug'   => $page->slug,
                'blocks' => $blocks,
            ],
        ]);
    }
}
