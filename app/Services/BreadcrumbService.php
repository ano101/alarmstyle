<?php

namespace App\Services;

use App\Models\Product;

class BreadcrumbService
{
    public function forProduct(Product $product): array
    {
        $breadcrumbs = [
            ['label' => 'Главная', 'url' => route('page.show', '/')],
        ];

        // Если у товара есть категория
        if ($product->mainCategory) {
            $breadcrumbs[] = [
                'label' => $product->mainCategory->name,
                'url' => route('catalog', $product->mainCategory->getSlug()),
            ];
        }

        // Текущий товар (без ссылки)
        $breadcrumbs[] = [
            'label' => $product->name,
            'url' => null,
        ];

        return $breadcrumbs;
    }
}
