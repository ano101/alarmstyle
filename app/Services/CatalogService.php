<?php

namespace App\Services;

use App\Facades\Seo;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\CatalogQuickLink;
use App\Models\Category;
use App\Models\CategoryLanding;
use App\Models\Product;
use App\Models\SeoMask;
use App\Models\Slug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CatalogService
{
    public function __construct(
        protected SeoApplier $seoApplier
    ) {}

    public function resolvePath(?string $path): array
    {
        $path = $path ? trim($path, '/') : '';

        if ($path === '') {
            throw new NotFoundHttpException;
        }

        $segments = explode('/', $path);

        // категория
        $categorySlug = array_shift($segments);

        $categorySlugModel = Slug::where('slug', $categorySlug)
            ->where('sluggable_type', Category::class)
            ->with(['sluggable', 'sluggable.seoMeta']) // загружаем категорию и её seoMeta
            ->first();

        if (! $categorySlugModel) {
            throw new NotFoundHttpException;
        }

        /** @var Category $category */
        $category = $categorySlugModel->sluggable;

        // атрибуты
        $attributeValues = collect();
        $needsRedirect = false;
        $redirectPath = null;

        if (! empty($segments)) {
            // ВАЖНО:
            // 1) все slug должны существовать
            // 2) sluggable должен быть AttributeValue
            // 3) AttributeValue->attribute.in_filter должен быть true, иначе 404
            $attributeSlugModels = Slug::query()
                ->whereIn('slug', $segments)
                ->where('sluggable_type', AttributeValue::class)
                ->whereHasMorph(
                    'sluggable',
                    [AttributeValue::class],
                    function (Builder $q) {
                        $q->whereHas('attribute', function (Builder $aq) {
                            $aq->where('in_filter', true);
                        });
                    }
                )
                ->with([
                    'sluggable',
                    'sluggable.attribute', // чтобы дальше не было N+1
                ])
                ->get();

            // если хоть один slug не найден ИЛИ найден, но in_filter=false → 404
            if ($attributeSlugModels->count() !== count($segments)) {
                throw new NotFoundHttpException;
            }

            // канонизация порядка slug'ов (алфавит)
            $canonicalAttributeSlugs = $attributeSlugModels
                ->pluck('slug')
                ->sort()
                ->values()
                ->all();

            if ($segments !== $canonicalAttributeSlugs) {
                $needsRedirect = true;
                $redirectPath = implode('/', array_merge([$categorySlug], $canonicalAttributeSlugs));
            }

            $attributeValues = $attributeSlugModels->pluck('sluggable');
        }

        return [
            'category' => $category,
            'attributeValues' => $attributeValues,
            'needsRedirect' => $needsRedirect,
            'redirectPath' => $redirectPath,
        ];
    }

    /* =======================
     * MEILISEARCH ЧЕРЕЗ SCOUT::search()
     * ======================= */

    protected function buildMeiliFilterStrings(
        Category $category,
        Request $request,
        ?Collection $selectedAttributeValues = null,
        bool $ignorePrice = false
    ): array {
        $filters = [];

        // категория
        $filters[] = 'category_ids = '.(int) $category->id;

        // цена
        if (! $ignorePrice) {
            if ($request->filled('price_from')) {
                $filters[] = 'price >= '.(float) $request->input('price_from');
            }
            if ($request->filled('price_to')) {
                $filters[] = 'price <= '.(float) $request->input('price_to');
            }
        }

        // выбранные значения атрибутов
        if ($selectedAttributeValues && $selectedAttributeValues->isNotEmpty()) {
            $grouped = $selectedAttributeValues->groupBy('attribute_id');

            foreach ($grouped as $attributeId => $values) {
                /** @var \App\Models\AttributeValue $firstValue */
                $firstValue = $values->first();

                // атрибут уже подгружен в resolvePath() (sluggable.attribute),
                // но на всякий случай оставим как есть — Laravel сам решит
                $attribute = $firstValue->attribute;
                $typeFront = (int) ($attribute->type_front ?? 0);

                $valueFilters = [];
                foreach ($values as $val) {
                    /** @var \App\Models\AttributeValue $val */
                    $valueFilters[] = 'attribute_value_ids = '.(int) $val->id;
                }

                if ($typeFront === 1) {
                    // checkbox: OR внутри атрибута
                    if (count($valueFilters) === 1) {
                        $filters[] = $valueFilters[0];
                    } else {
                        $filters[] = $valueFilters; // OR
                    }
                } else {
                    // radio/select: AND
                    foreach ($valueFilters as $f) {
                        $filters[] = $f;
                    }
                }
            }
        }

        return $filters;
    }

    protected function meiliFacetRaw(
        Category $category,
        Request $request,
        ?Collection $selectedAttributeValues,
        array $facets,
        bool $ignorePrice = false,
        array $extraOptions = []
    ): array {
        $filters = $this->buildMeiliFilterStrings(
            $category,
            $request,
            $selectedAttributeValues,
            $ignorePrice
        );

        $builder = Product::search(
            '',
            function ($meiliIndex, string $query, array $options) use ($filters, $facets, $extraOptions) {
                $options['filter'] = $filters;
                $options['facets'] = $facets;
                $options['limit'] = $extraOptions['limit'] ?? 0;

                unset($extraOptions['limit']);
                $options = array_merge($options, $extraOptions);

                return $meiliIndex->search($query, $options);
            }
        );

        return $builder->raw();
    }

    public function searchProductsForListing(
        Category $category,
        Collection $selectedAttributeValues,
        Request $request,
        int $perPage
    ): LengthAwarePaginator {
        $filters = $this->buildMeiliFilterStrings($category, $request, $selectedAttributeValues);

        $sortParam = $request->input('sort');
        $sortOptions = null;

        if (! $sortParam) {
            $sortParam = 'popular_desc';
        }

        switch ($sortParam) {
            case 'price_asc':
                $sortOptions = ['price:asc'];
                break;
            case 'price_desc':
                $sortOptions = ['price:desc'];
                break;
            case 'popular_desc':
                $sortOptions = ['popular:desc'];
                break;
            default:
                $sortOptions = null;
                break;
        }

        // текущая страница (Laravel сам достаёт ?page=)
        $page = LengthAwarePaginator::resolveCurrentPage();
        $limit = $perPage;
        $offset = ($page - 1) * $limit;

        // ЧИСТЫЙ Meili: берём raw
        $raw = Product::search(
            '',
            function ($meiliIndex, string $query, array $options) use ($filters, $sortOptions, $limit, $offset) {
                $options['filter'] = $filters;

                if ($sortOptions) {
                    $options['sort'] = $sortOptions;
                }

                // Управляем пагинацией сами
                $options['limit'] = $limit;
                $options['offset'] = $offset;

                return $meiliIndex->search($query, $options);
            }
        )->raw();

        // hits — это массив документов, которые мы положили в toSearchableArray()
        $items = collect($raw['hits'] ?? []);

        // Общее количество — из estimatedTotalHits (если есть)
        $total = $raw['estimatedTotalHits'] ?? $items->count();

        // Собираем обычный Laravel-пагинатор, только элементы — массивы, а не модели
        $paginator = new LengthAwarePaginator(
            $items,
            $total,
            $limit,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return $paginator;
    }

    public function getPriceBounds(
        Category $category,
        Collection $selectedAttributeValues,
        Request $request
    ): ?array {
        $raw = $this->meiliFacetRaw(
            category: $category,
            request: $request,
            selectedAttributeValues: $selectedAttributeValues,
            facets: ['price'],
            ignorePrice: true,
            extraOptions: []
        );

        $distribution = $raw['facetDistribution']['price'] ?? [];

        if (empty($distribution)) {
            return null;
        }

        $prices = array_map('floatval', array_keys($distribution));

        $min = min($prices);
        $max = max($prices);

        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }

        return [
            'min' => $min,
            'max' => $max,
        ];
    }

    public function getAttributesForSidebarFromMeili(Category $category, Request $request): Collection
    {
        $raw = $this->meiliFacetRaw(
            category: $category,
            request: $request,
            selectedAttributeValues: null,
            facets: ['attribute_value_ids'],
            ignorePrice: false
        );

        $distribution = $raw['facetDistribution']['attribute_value_ids'] ?? [];
        if (empty($distribution)) {
            return collect();
        }

        $valueIds = array_map('intval', array_keys($distribution));

        // 1) получаем нужные значения + их атрибут
        $values = AttributeValue::query()
            ->whereIn('id', $valueIds)
            ->with([
                'slug',
                'attribute' => function ($q) {
                    $q->where('in_filter', true)
                        ->with('slug')
                        ->orderBy('sort');
                },
            ])
            ->get()
            ->filter(fn (AttributeValue $v) => $v->attribute); // отсекаем, если attribute не прошёл in_filter

        // 2) группируем значения по атрибуту и возвращаем коллекцию атрибутов с values
        $grouped = $values->groupBy('attribute_id');

        $attributes = $grouped->map(function (Collection $vals) {
            /** @var Attribute $attr */
            $attr = $vals->first()->attribute;

            // важно: перезаписываем relation values только нужными
            $attr->setRelation('values', $vals->values());

            return $attr;
        })->values();

        // 3) сортировка по sort атрибута (на всякий случай)
        return $attributes->sortBy(fn (Attribute $a) => (int) $a->sort)->values();
    }

    public function buildFacetsFromMeili(
        Category $category,
        Request $request,
        Collection $selectedAttributeValues,
        Collection $attributes
    ): array {
        $raw = $this->meiliFacetRaw(
            category: $category,
            request: $request,
            selectedAttributeValues: $selectedAttributeValues,
            facets: ['attribute_value_ids'],
            ignorePrice: false
        );

        $distribution = $raw['facetDistribution']['attribute_value_ids'] ?? [];

        $selectedIds = $selectedAttributeValues
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $selectedByAttribute = $selectedAttributeValues
            ->groupBy('attribute_id')
            ->map(fn (Collection $group) => $group->pluck('id')->map(fn ($id) => (int) $id)->all());

        $facets = [];

        foreach ($attributes as $attribute) {
            $typeFront = (int) ($attribute->type_front ?? 1);
            $attrId = (int) $attribute->id;

            $selectedForThisAttr = $selectedByAttribute[$attrId] ?? [];

            foreach ($attribute->values as $value) {
                $id = (int) $value->id;

                if (in_array($id, $selectedIds, true)) {
                    $facets[$id] = true;

                    continue;
                }

                if ($typeFront === 1 && ! empty($selectedForThisAttr)) {
                    $facets[$id] = true;

                    continue;
                }

                $count = $distribution[(string) $id] ?? 0;
                $facets[$id] = $count > 0;
            }
        }

        return $facets;
    }

    /**
     * H1 для каталога:
     * - приоритет: seoMeta->h1 (если есть)
     * - затем meta_h1_pattern (только для чистой категории или landing)
     * - для фильтров без landing (noindex) делаем фоллбек: "Категория + h1Suffix"
     */
    public function applyCatalogSeo(
        Category $category,
        LengthAwarePaginator $items,
        Collection $selectedAttributeValues,
        Request $request,
        ?CategoryLanding $landing = null
    ): void {
        $seoSource = $landing ?? $category;
        $isLanding = $landing !== null;

        $currentPage = (int) $items->currentPage();
        $totalItems = (int) $items->total();
        $isPaginated = $currentPage > 1;

        $selectedAttributesCount = $selectedAttributeValues->count();
        $hasFilters = $selectedAttributesCount > 0;

        $seoMeta = $seoSource->seoMeta ?? null;
        $baseNoIndex = optional($seoMeta)->noindex ?? false;

        // =========================
        // NOINDEX: открыты только категории и landing
        // =========================
        $shouldNoIndex = $baseNoIndex;

        // любые GET параметры → noindex
        if (! empty($request->query())) {
            $shouldNoIndex = true;
        }

        // пагинация page>1 → noindex
        if ($isPaginated) {
            $shouldNoIndex = true;
        }

        // выбран хоть 1 атрибут и НЕТ landing → noindex всегда
        if ($hasFilters && ! $isLanding) {
            $shouldNoIndex = true;
        }

        // =========================
        // SEO логика в зависимости от типа страницы
        // =========================
        $filtersUi = $this->buildFilterUiPieces($selectedAttributeValues);
        $filterText = $filtersUi['filterText'] ?? '';

        $sortParam = $request->input('sort', 'popular_desc');
        $sortLabel = match ($sortParam) {
            'price_asc' => 'сначала дешевле',
            'price_desc' => 'сначала дороже',
            'popular_desc' => 'по популярности',
            default => '',
        };

        $priceFrom = $request->input('price_from', '');
        $priceTo = $request->input('price_to', '');

        $vars = [
            'category' => $category->name,
            'category_lc' => mb_strtolower($category->name),
            'parent' => $category->parent?->name ?? '',
            'filters' => $filterText,
            'price_from' => $priceFrom,
            'price_to' => $priceTo,
            'sort_label' => $sortLabel,
            'page' => '',
        ];

        // =========================
        // Применяем SEO
        // =========================
        // Если есть фильтры БЕЗ лэндинга → это noindex страница
        // Для неё НЕ используем seoMeta категории, только маски (если есть)
        if ($hasFilters && ! $isLanding) {
            // Пытаемся применить только маски (без seoMeta)
            $mask = SeoMask::resolveFor('catalog_category', $category);
            if ($mask) {
                $maskTitle = $this->renderSeoMask($mask->meta_title_pattern ?? null, $vars);
                $maskDesc = $this->renderSeoMask($mask->meta_description_pattern ?? null, $vars);
                $maskH1 = $this->renderSeoMask($mask->meta_h1_pattern ?? null, $vars);

                if ($maskTitle) {
                    Seo::setMetaTitle($maskTitle);
                }
                if ($maskDesc) {
                    Seo::setMetaDescription($maskDesc);
                }
                if ($maskH1) {
                    Seo::setH1($maskH1);
                }
            }
            // Если маски нет или она пустая — ничего не устанавливаем,
            // фоллбеки сработают ниже
        } else {
            // Чистая категория или лэндинг → используем полную логику SeoApplier
            $this->seoApplier->apply(
                model: $seoSource,
                context: 'catalog_category',
                vars: $vars,
                useMask: true, // всегда true для категорий и лэндингов
                maskModel: $category
            );
        }

        // =========================
        // Специфичная логика каталога
        // =========================

        // Устанавливаем noindex (может переписать значение из seoMeta)
        Seo::setNoIndex($shouldNoIndex);

        // ---------- TITLE с пагинацией ----------
        $currentTitle = Seo::getTitle();

        // Если title не был установлен, делаем фоллбек
        if (! $currentTitle) {
            $currentTitle = $category->name;
            if ($filterText !== '') {
                $currentTitle .= ' – '.$filterText;
            }
        }

        if ($isPaginated) {
            $currentTitle .= ' – страница '.$currentPage;
        }

        Seo::setMetaTitle($currentTitle);

        // ---------- DESCRIPTION с пагинацией ----------
        $currentDescription = Seo::getDescription();

        // Если description не был установлен, делаем фоллбек
        if (! $currentDescription) {
            $currentDescription = 'Каталог '.mb_strtolower($category->name).' — подбор товаров по параметрам и цене.';
            if ($filterText !== '') {
                $currentDescription .= ' Выбрано: '.$filterText.'.';
            }
        }

        if ($isPaginated) {
            $currentDescription = rtrim($currentDescription);
            $currentDescription = rtrim($currentDescription, '.');
            $currentDescription .= '. Страница '.$currentPage.'.';
        }

        Seo::setMetaDescription($currentDescription);

        // ---------- H1 с фоллбеком ----------
        $currentH1 = Seo::getH1();

        // Если H1 не был установлен, делаем фоллбек
        if (! $currentH1) {
            $h1Suffix = $filtersUi['h1Suffix'] ?? '';
            $currentH1 = $h1Suffix ? ($category->name.' '.$h1Suffix) : $category->name;
        }

        Seo::setH1($currentH1);

        // =========================
        // CANONICAL
        // =========================
        $categoryUrl = route('catalog', ['path' => $category->getSlug()]);

        if ($shouldNoIndex) {
            Seo::setCanonical($categoryUrl);
        } else {
            Seo::setCanonical($request->fullUrl());
        }
    }

    protected function renderSeoMask(?string $pattern, array $vars): ?string
    {
        if (! $pattern) {
            return null;
        }

        $result = $pattern;

        foreach ($vars as $key => $value) {
            $result = str_replace('{'.$key.'}', (string) $value, $result);
        }

        $result = preg_replace('/\{[a-z0-9_]+\}/i', '', $result);
        $result = preg_replace('/\s{2,}/u', ' ', trim($result));

        return $result === '' ? null : $result;
    }

    public function findLanding(Category $category, Collection $selectedAttributeValues): ?CategoryLanding
    {
        if ($selectedAttributeValues->isEmpty()) {
            return null;
        }

        $selectedIds = $selectedAttributeValues
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values()
            ->all();

        if (empty($selectedIds)) {
            return null;
        }

        $key = implode(',', $selectedIds);

        return CategoryLanding::query()
            ->where('category_id', (int) $category->id)
            ->where('attribute_value_ids_key', $key)
            ->with('seoMeta') // загружаем seoMeta для лэндинга
            ->first();
    }

    public function buildFilterUiPieces(Collection $selectedAttributeValues): array
    {
        if ($selectedAttributeValues->isEmpty()) {
            return [
                'filterText' => '',
                'h1Suffix' => '',
                'crumbsBySlug' => [],
            ];
        }

        $grouped = $selectedAttributeValues->groupBy('attribute_id');

        $fullParts = [];
        $h1Parts = [];
        $crumbsBySlug = [];

        foreach ($grouped as $attrId => $values) {
            /** @var AttributeValue $firstVal */
            $firstVal = $values->first();

            // attribute уже подгружен в resolvePath(): sluggable.attribute
            $attribute = $firstVal->attribute;
            if (! $attribute) {
                continue;
            }

            $typeFront = (int) ($attribute->type_front ?? 1);
            $attrName = $attribute->name ?? null;

            $valueLabels = [];

            foreach ($values as $val) {
                /** @var AttributeValue $val */
                $label = (string) ($val->value ?? '');

                if ($label === '') {
                    continue;
                }

                $valueLabels[] = $label;

                $slug = method_exists($val, 'getSlug') ? $val->getSlug() : null;
                if ($slug) {
                    $crumbLabel = ($typeFront === 1 && $attrName)
                        ? ($attrName.' '.$label)
                        : $label;

                    $crumbsBySlug[$slug] = $crumbLabel;
                }
            }

            if (empty($valueLabels)) {
                continue;
            }

            // filterText: "Цвет: красный, синий; Размер: 42"
            if ($attrName) {
                $fullParts[] = $attrName.': '.implode(', ', $valueLabels);
            } else {
                $fullParts[] = implode(', ', $valueLabels);
            }

            // h1Suffix: "Цвет красный синий Размер 42" (или как у тебя было)
            if ($typeFront === 1 && $attrName) {
                $h1Parts[] = trim($attrName.' '.implode(' ', $valueLabels));
            } else {
                $h1Parts[] = implode(' ', $valueLabels);
            }
        }

        return [
            'filterText' => implode('; ', $fullParts),
            'h1Suffix' => implode(' ', $h1Parts),
            'crumbsBySlug' => $crumbsBySlug,
        ];
    }

    public function getQuickLinksForCatalog(
        Category $category,
        Collection $selectedAttributeValues,
        ?CategoryLanding $landing = null
    ): array {
        $hasFilters = $selectedAttributeValues->isNotEmpty();

        // 1) Есть фильтры и НЕТ landing => кнопок нет
        if ($hasFilters && ! $landing) {
            return [];
        }

        // 2) Есть фильтры и landing есть => только кнопки landing
        if ($hasFilters && $landing) {
            $links = $landing->quickLinks()
                ->where('is_active', true)
                ->orderBy('sort')
                ->get();

            return $links->map(fn (CatalogQuickLink $link) => [
                'label' => $link->label,
                'href' => $link->resolveHref(),
                'icon' => $link->icon,
                'color' => $link->color,
            ])->all();
        }

        // 3) Фильтров нет => только кнопки категории
        $links = $category->quickLinks()
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        return $links->map(fn (CatalogQuickLink $link) => [
            'label' => $link->label,
            'href' => $link->resolveHref(),
            'icon' => $link->icon,
            'color' => $link->color,
        ])->all();
    }
}
