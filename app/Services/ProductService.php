<?php

namespace App\Services;

use App\Facades\JsonLd;
use App\Facades\Seo;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductService
{
    public function __construct(
        protected BreadcrumbService $breadcrumbService,
    ) {}

    /**
     * Применить SEO и OG/Twitter fallback для страницы товара.
     */
    public function applySeo(Product $product, Request $request): void
    {
        $vars = [
            'name' => $product->name ?? '',
            'price' => $product->basePrice?->price ?? '',
            'id' => $product->id ?? '',
            'category' => $product->mainCategory?->name ?? '',
        ];

        if ($product->attributeValues && $product->attributeValues->isNotEmpty()) {
            foreach ($product->attributeValues as $attributeValue) {
                if ($attributeValue->attribute_id) {
                    $vars['attr_'.$attributeValue->attribute_id] = $attributeValue->value ?? '';
                }
            }
        }

        $product->applySeo('product', $vars);

        Seo::setCanonicalIfEmpty(url()->current());

        $productImageUrl = $product->image
            ? route('images.show', ['preset' => 'product.og', 'path' => $product->image])
            : null;

        Seo::setOgTitleIfEmpty($product->name);
        Seo::setOgDescriptionIfEmpty($product->description);
        Seo::setOgImageIfEmpty($productImageUrl);
        Seo::setOgTypeIfEmpty('product');

        Seo::setTwitterTitleIfEmpty($product->name);
        Seo::setTwitterDescriptionIfEmpty($product->description);
        Seo::setTwitterImageIfEmpty($productImageUrl);
        Seo::setTwitterCardIfEmpty('summary_large_image');
    }

    /**
     * Добавить JSON-LD разметку для товара и хлебных крошек.
     *
     * @param  array<int, array{label: string, url: string|null}>  $breadcrumbs
     */
    public function applyJsonLd(Product $product, array $breadcrumbs, Request $request): void
    {
        $jsonLdProduct = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->description ?? $product->name,
            'sku' => 'PRODUCT-'.$product->id,
        ];

        if ($product->image) {
            $jsonLdProduct['image'] = route('images.show', [
                'preset' => 'product.card',
                'path' => $product->image,
            ]);
        }

        $brand = $product->presenter()->brand();
        if ($brand) {
            $jsonLdProduct['brand'] = [
                '@type' => 'Brand',
                'name' => $brand,
            ];
        }

        if ($product->basePrice && $product->basePrice->price) {
            $jsonLdProduct['offers'] = [
                '@type' => 'Offer',
                'price' => $product->basePrice->price,
                'priceCurrency' => 'RUB',
                'availability' => 'https://schema.org/InStock',
                'url' => url()->current(),
            ];
        }

        if ($product->mainCategory) {
            $jsonLdProduct['category'] = $product->mainCategory->name;
        }

        JsonLd::add($jsonLdProduct);

        $this->applyBreadcrumbsJsonLd($breadcrumbs, $request);
    }

    /**
     * Собрать данные товара для frontend.
     *
     * @return array{id: int, name: string, price: mixed, description: string|null, gallery: string[], attributeGroups: array<int, mixed>}
     */
    public function buildProductData(Product $product): array
    {
        $gallery = [];

        if ($product->image) {
            $gallery[] = $product->image;
        }

        if ($product->images && $product->images->isNotEmpty()) {
            foreach ($product->images as $image) {
                $imageUrl = $image->path ?? $image->url ?? null;
                if ($imageUrl) {
                    $gallery[] = $imageUrl;
                }
            }
        }

        $attributesByGroup = [];
        $attributeFeature = [];

        if ($product->attributeValues && $product->attributeValues->isNotEmpty()) {
            foreach ($product->attributeValues as $attributeValue) {
                $attribute = $attributeValue->attribute;

                if (! $attribute) {
                    continue;
                }

                $group = $attribute->attributeGroup;
                $groupName = $group ? $group->name : 'Без группы';

                if (! isset($attributesByGroup[$groupName])) {
                    $attributesByGroup[$groupName] = [
                        'name' => $groupName,
                        'attributes' => [],
                    ];
                }

                if ($attributeValue->feature) {
                    $attributeFeature[] = $attributeValue->feature;
                }

                $attributesByGroup[$groupName]['attributes'][] = [
                    'name' => $attribute->name,
                    'value' => $attributeValue->value,
                    'type' => $attribute->type,
                    'type_front' => $attribute->type_front,
                    'helper_text' => $attribute->helper_text ?: false,
                ];
            }
        }

        return [
            'id' => $product->id,
            'name' => $product->name ?? '',
            'price' => $product->basePrice?->price ?? null,
            'description' => $product->description,
            'gallery' => $gallery,
            'attributeGroups' => array_values($attributesByGroup),
            'attributeFeature' => $attributeFeature,
        ];
    }

    /**
     * Добавить JSON-LD BreadcrumbList.
     *
     * @param  array<int, array{label: string, url: string|null}>  $breadcrumbs
     */
    protected function applyBreadcrumbsJsonLd(array $breadcrumbs, Request $request): void
    {
        if (empty($breadcrumbs)) {
            return;
        }

        $breadcrumbItems = [];
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['label'],
                'item' => $breadcrumb['url'] ?? url()->current(),
            ];
        }

        JsonLd::add([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems,
        ]);
    }
}
