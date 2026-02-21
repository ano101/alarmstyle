<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Slug;
use App\Services\BreadcrumbService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request, string $slug, BreadcrumbService $breadcrumbService, ProductService $productService)
    {
        $slugModel = Slug::where('slug', $slug)
            ->where('sluggable_type', Product::class)
            ->with([
                'sluggable',
                'sluggable.seoMeta',
                'sluggable.images',
                'sluggable.basePrice',
                'sluggable.attributeValues.attribute.attributeGroup',
                'sluggable.mainCategory',
                'sluggable.mainCategory.slug',
            ])
            ->firstOrFail();

        /** @var Product $product */
        $product = $slugModel->sluggable;

        $productService->applySeo($product, $request);

        $breadcrumbs = $breadcrumbService->forProduct($product);

        $productService->applyJsonLd($product, $breadcrumbs, $request);

        $productData = $productService->buildProductData($product);

        return Inertia::render('Product/Show', [
            'product' => $productData,
            'breadcrumbs' => $breadcrumbs,
            'brand' => $product->presenter()->brand(),
            'attributeFeature' => $productData['attributeFeature'],
        ]);
    }
}
