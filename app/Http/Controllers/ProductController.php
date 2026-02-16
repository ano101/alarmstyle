<?php

namespace App\Http\Controllers;

use App\Facades\JsonLd;
use App\Facades\Seo;
use App\Models\Product;
use App\Models\Slug;
use App\Services\BreadcrumbService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request, string $slug, BreadcrumbService $breadcrumbService)
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

        // Применяем SEO с контекстом 'product' и переменными для масок
        $vars = [
            'name' => $product->name ?? '',
            'price' => $product->basePrice?->price ?? '',
            'id' => $product->id ?? '',
            'category' => $product->mainCategory?->name ?? '',
        ];

        // Добавляем атрибуты по их ID для использования в масках
        // Формат: {attr_5} где 5 - это ID атрибута
        if ($product->attributeValues && $product->attributeValues->isNotEmpty()) {
            foreach ($product->attributeValues as $attributeValue) {
                if ($attributeValue->attribute_id) {
                    $vars['attr_'.$attributeValue->attribute_id] = $attributeValue->value ?? '';
                }
            }
        }

        $product->applySeo('product', $vars);

        // Устанавливаем канонический URL на саму страницу товара
        Seo::setCanonicalIfEmpty(url()->current());

        // Fallback для OG и Twitter метатегов, если не заполнены в seoMeta
        $productImageUrl = $product->image
            ? route('images.show', ['preset' => 'product.og', 'path' => $product->image])
            : null;

        // OG fallback
        Seo::setOgTitleIfEmpty($product->name);
        Seo::setOgDescriptionIfEmpty($product->description);
        Seo::setOgImageIfEmpty($productImageUrl);
        Seo::setOgTypeIfEmpty('product');

        // Twitter fallback
        Seo::setTwitterTitleIfEmpty($product->name);
        Seo::setTwitterDescriptionIfEmpty($product->description);
        Seo::setTwitterImageIfEmpty($productImageUrl);
        Seo::setTwitterCardIfEmpty('summary_large_image');

        // JSON-LD разметка для товара (Schema.org Product)
        $jsonLdProduct = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->description ?? $product->name,
            'sku' => 'PRODUCT-'.$product->id,
        ];

        // Добавляем изображение если есть
        if ($product->image) {
            $jsonLdProduct['image'] = route('images.show', [
                'preset' => 'product.card',
                'path' => $product->image,
            ]);
        }


        // Добавляем бренд если есть
        $brand = $product->presenter()->brand();
        if ($brand) {
            $jsonLdProduct['brand'] = [
                '@type' => 'Brand',
                'name' => $brand,
            ];
        }

        // Добавляем цену и доступность
        if ($product->basePrice && $product->basePrice->price) {
            $jsonLdProduct['offers'] = [
                '@type' => 'Offer',
                'price' => $product->basePrice->price,
                'priceCurrency' => 'RUB',
                'availability' => 'https://schema.org/InStock',
                'url' => url()->current(),
            ];
        }

        // Добавляем категорию если есть
        if ($product->mainCategory) {
            $jsonLdProduct['category'] = $product->mainCategory->name;
        }

        JsonLd::add($jsonLdProduct);

        // JSON-LD для хлебных крошек
        $breadcrumbs = $breadcrumbService->forProduct($product);

        // JSON-LD BreadcrumbList
        $breadcrumbItems = [];
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['label'],
                'item' => $breadcrumb['url'] ?? url()->current(),
            ];
        }

        if (count($breadcrumbItems) > 0) {
            JsonLd::add([
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $breadcrumbItems,
            ]);
        }


        // Форматируем данные продукта для frontend
        $productData = [
            'id' => $product->id,
            'name' => $product->name ?? '',
            'price' => $product->basePrice?->price ?? null,
        ];

        // Собираем галерею изображений на бэкенде
        $gallery = [];

        // Добавляем главное изображение первым
        if ($product->image) {
            $gallery[] = $product->image;
        }

        // Добавляем дополнительные изображения
        if ($product->images && $product->images->isNotEmpty()) {
            foreach ($product->images as $image) {
                $imageUrl = $image->path ?? $image->url ?? null;
                if ($imageUrl) {
                    $gallery[] = $imageUrl;
                }
            }
        }

        // Переопределяем images полной галереей
        $productData['gallery'] = $gallery;

        // Собираем атрибуты по группам
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

                // Инициализируем группу если её ещё нет
                if (! isset($attributesByGroup[$groupName])) {
                    $attributesByGroup[$groupName] = [
                        'name' => $groupName,
                        'attributes' => [],
                    ];
                }

                if ($attributeValue->feature) {
                    $attributeFeature[] = $attributeValue->feature;
                }

                // Добавляем атрибут в группу
                $attributesByGroup[$groupName]['attributes'][] = [
                    'name' => $attribute->name,
                    'value' => $attributeValue->value,
                    'type' => $attribute->type,
                    'type_front' => $attribute->type_front,
                    'helper_text' => $attribute->helper_text ?: false,
                ];
            }
        }

        // Преобразуем в индексированный массив и добавляем в $productData
        $productData['attributeGroups'] = array_values($attributesByGroup);

        return Inertia::render('Product/Show', [
            'product' => $productData,
            'breadcrumbs' => $breadcrumbs,
            'brand' => $product->presenter()->brand(),
            'attributeFeature' => $attributeFeature,
        ]);
    }
}
