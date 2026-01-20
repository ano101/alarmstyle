<?php

namespace App\Support\Blocks\Handlers;

use App\Models\Product;
use App\Support\Blocks\Contracts\BlockHandler;

class ProductsSliderHandler implements BlockHandler
{
    public function handle(array $block): array
    {
        $data = $block['data'] ?? [];

        $query = Product::query()->with('prices', 'images');

        // Фильтрация по категории
        if (! empty($data['category_id'])) {
            $query->where('category_id', $data['category_id']);
        }

        // Сортировка
        match ($data['sort'] ?? 'popular') {
            'new' => $query->latest(),
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default => $query->orderBy('popularity', 'desc'),
        };

        // Лимит
        $limit = $data['limit'] ?? 10;
        $products = $query->limit($limit)->get();

        // Добавляем продукты к данным блока
        return array_merge($block, [
            'data' => array_merge($data, [
                'products' => $products,
            ]),
        ]);
    }
}
