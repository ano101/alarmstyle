<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Slug;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\BreadcrumbService;

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
                'sluggable.mainCategory.slug'
            ])
            ->firstOrFail();

        /** @var Product $product */
        $product = $slugModel->sluggable;

        // Применяем SEO с контекстом 'product' и переменными для масок
        $vars = [
            'name'  => $product->name ?? '',
            'price' => $product->basePrice?->price ?? '',
            'article' => $product->article ?? '',
            // добавьте другие переменные по необходимости
        ];

        $product->applySeo('product', $vars);

        $breadcrumbs = $breadcrumbService->forProduct($product);

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

                if (!$attribute) {
                    continue;
                }

                $group = $attribute->attributeGroup;
                $groupName = $group ? $group->name : 'Без группы';

                // Инициализируем группу если её ещё нет
                if (!isset($attributesByGroup[$groupName])) {
                    $attributesByGroup[$groupName] = [
                        'name' => $groupName,
                        'attributes' => []
                    ];
                }

                if($attributeValue->feature){
                    $attributeFeature[] = $attributeValue->feature;
                }

                // Добавляем атрибут в группу
                $attributesByGroup[$groupName]['attributes'][] = [
                    'name' => $attribute->name,
                    'value' => $attributeValue->value,
                    'type' => $attribute->type,
                    'type_front' => $attribute->type_front,
                    'helper_text' => $attribute->helper_text ?: false
                ];
            }
        }

        // Преобразуем в индексированный массив и добавляем в $productData
        $productData['attributeGroups'] = array_values($attributesByGroup);

        return Inertia::render('Product/Show', [
            'product' => $productData,
            'breadcrumbs' => $breadcrumbs,
            'brand' => $product->presenter()->brand(),
            'attributeFeature' => $attributeFeature
        ]);
    }
}
