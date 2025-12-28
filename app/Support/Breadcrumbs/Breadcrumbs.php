<?php

namespace App\Support\Breadcrumbs;

use App\Models\Category;
use App\Models\Page;

class Breadcrumbs
{
    public static function home(string $label = 'Главная'): array
    {
        return [
            [
                'label'   => $label,
                'url'     => route('page.show', ['slug' => '']), // поправь на свой роут
                'current' => false,
            ],
        ];
    }

    /**
     * Категория каталога + опциональное дополнение по фильтрам.
     *
     * Пример:
     *  catalogCategory($category, 'Pandora · GSM')
     *  → "Автосигнализации — Pandora · GSM"
     */
    public static function catalogCategory(Category $category, ?string $filtersLabel = null): array
    {
        $items = self::home();

        $label = $category->name;
        if ($filtersLabel) {
            $label .= ' — ' . $filtersLabel;
        }

        $items[] = [
            'label'   => $label,
            'url'     => route('catalog', ['path' => $category->getSlug()]),
            'current' => true,
        ];

        return $items;
    }

    public static function page(Page $page): array
    {
        $items = self::home();

        $items[] = [
            'label'   => $page->title,
            'url'     => $page->url ?? url()->current(),
            'current' => true,
        ];

        return $items;
    }
}
