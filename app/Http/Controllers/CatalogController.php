<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CatalogService;
use App\Support\Breadcrumbs\Breadcrumbs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CatalogController extends Controller
{
    public function __construct(
        protected CatalogService $catalogService
    ) {}

    public function index(Request $request, ?string $path = null)
    {
        $start = microtime(true);
        $marks = [];

        $mark = function (string $label) use (&$marks, $start) {
            $marks[$label] = round((microtime(true) - $start) * 1000, 2); // ms
        };

        $data = $this->catalogService->resolvePath($path);
        $mark('resolvePath');

        if ($data['needsRedirect']) {
            return redirect()
                ->route('catalog', ['path' => $data['redirectPath']])
                ->setStatusCode(301);
        }

        $category = $data['category'];
        $attributeValues = $data['attributeValues'];

        $filtersUi = $this->catalogService->buildFilterUiPieces($attributeValues);
        $crumbsBySlug = $filtersUi['crumbsBySlug'] ?? [];
        $mark('buildFilterUiPieces');

        $landing = $this->catalogService->findLanding($category, $attributeValues);
        $mark('findLanding');

        $quickLinks = $this->catalogService->getQuickLinksForCatalog($category, $attributeValues, $landing);
        $mark('getQuickLinksForCatalog');

        $perPage = 15;

        $items = $this->catalogService
            ->searchProductsForListing($category, $attributeValues, $request, $perPage);

        $mark('searchProductsForListing');

        $priceBounds = $this->catalogService
            ->getPriceBounds($category, $attributeValues, $request);
        $mark('getPriceBounds');

        $attributes = $this->catalogService
            ->getAttributesForSidebarFromMeili($category, $request);
        $mark('getAttributesForSidebarFromMeili');

        $facets = $this->catalogService
            ->buildFacetsFromMeili($category, $request, $attributeValues, $attributes);
        $mark('buildFacetsFromMeili');

        Log::info('Catalog timing', [
            'path' => $path,
            'total' => round((microtime(true) - $start) * 1000, 2),
            'steps' => $marks,
        ]);
        // остальные категории
        $categories = Category::query()
            ->whereKeyNot($category->id)
            ->with('slug')
            ->get();

        // выбранные slug’и
        $selectedValueSlugs = $attributeValues
            ->map(fn ($v) => $v->getSlug())
            ->filter()
            ->values()
            ->all();

        // канонический порядок slug'ов
        $selectedSlugSequence = $selectedValueSlugs;
        sort($selectedSlugSequence, SORT_STRING);

        $categorySlug = $category->getSlug();

        // SEO (внутри уже новая логика noindex)
        $this->catalogService->applyCatalogSeo(
            $category,
            $items,
            $attributeValues,
            $request,
            $landing
        );

        // H1: теперь из маски meta_h1_pattern (только категория/landing) или фоллбек
        $pageTitle = $this->catalogService->buildCatalogH1(
            $category,
            $attributeValues,
            $request,
            $landing
        );

        // хлебные крошки
        $breadcrumbs = Breadcrumbs::home();

        $breadcrumbs[] = [
            'label' => $category->name,
            'url' => route('catalog', ['path' => $categorySlug]),
            'current' => empty($selectedSlugSequence),
        ];

        if (! empty($selectedSlugSequence)) {
            $accumulated = [];
            $lastIndex = count($selectedSlugSequence) - 1;

            foreach ($selectedSlugSequence as $index => $slug) {
                $accumulated[] = $slug;
                $label = $crumbsBySlug[$slug] ?? $slug;

                $isLast = $index === $lastIndex;
                $path = $categorySlug.'/'.implode('/', $accumulated);

                $breadcrumbs[] = [
                    'label' => $label,
                    'url' => $isLast ? null : route('catalog', ['path' => $path]),
                    'current' => $isLast,
                ];
            }
        }

        return Inertia::render('Catalog/Index', [
            'category' => $category,
            'categories' => $categories,
            'items' => $items,
            'attributes' => $attributes,
            'selectedValueSlugs' => $selectedValueSlugs,
            'categorySlug' => $categorySlug,
            'facets' => $facets,
            'priceBounds' => $priceBounds,
            'landing' => $landing,
            'filters' => $request->query(),
            'quickLinks' => $quickLinks,

            'pageTitle' => $pageTitle,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
