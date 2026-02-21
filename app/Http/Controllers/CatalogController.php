<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CatalogService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatalogController extends Controller
{
    public function __construct(
        protected CatalogService $catalogService,
    ) {}

    public function index(Request $request, ?string $path = null)
    {
        $data = $this->catalogService->resolvePath($path);

        if ($data['needsRedirect']) {
            return redirect()
                ->route('catalog', ['path' => $data['redirectPath']])
                ->setStatusCode(301);
        }

        $category = $data['category'];
        $attributeValues = $data['attributeValues'];

        $filtersUi = $this->catalogService->buildFilterUiPieces($attributeValues);
        $crumbsBySlug = $filtersUi['crumbsBySlug'] ?? [];

        $landing = $this->catalogService->findLanding($category, $attributeValues);

        $quickLinks = $this->catalogService->getQuickLinksForCatalog($category, $attributeValues, $landing);

        $perPage = 15;

        $items = $this->catalogService
            ->searchProductsForListing($category, $attributeValues, $request, $perPage);

        $priceBounds = $this->catalogService
            ->getPriceBounds($category, $attributeValues, $request);

        $attributes = $this->catalogService
            ->getAttributesForSidebarFromMeili($category, $request);

        $facets = $this->catalogService
            ->buildFacetsFromMeili($category, $request, $attributeValues, $attributes);

        $categories = Category::query()
            ->whereKeyNot($category->id)
            ->with('slug')
            ->get();

        $selectedValueSlugs = $attributeValues
            ->map(fn ($v) => $v->getSlug())
            ->filter()
            ->values()
            ->all();

        $selectedSlugSequence = $selectedValueSlugs;
        sort($selectedSlugSequence, SORT_STRING);

        $this->catalogService->applyCatalogSeo(
            $category,
            $items,
            $attributeValues,
            $request,
            $landing
        );

        $breadcrumbs = $this->catalogService->buildBreadcrumbs(
            $category,
            $selectedSlugSequence,
            $crumbsBySlug
        );

        $this->catalogService->applyBreadcrumbsJsonLd($breadcrumbs, $request);

        return Inertia::render('Catalog/Index', [
            'category' => $category,
            'categories' => $categories,
            'items' => $items,
            'attributes' => $attributes,
            'selectedValueSlugs' => $selectedValueSlugs,
            'categorySlug' => $category->getSlug(),
            'facets' => $facets,
            'priceBounds' => $priceBounds,
            'landing' => $landing,
            'filters' => $request->query(),
            'quickLinks' => $quickLinks,

            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
