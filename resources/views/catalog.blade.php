@extends('layout.layout')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-10">

        {{-- Заголовок категории --}}
        <h1 class="text-3xl font-bold mb-8">
            {{ $category->name }}
        </h1>

        <div class="flex flex-col md:flex-row gap-10">

            {{-- SIDEBAR LEFT --}}
            <aside class="w-full md:w-64 flex-shrink-0">

                {{-- Категории --}}
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Категории</h2>

                    <ul class="space-y-2">
                        {{-- текущая категория --}}
                        <li>
                            <a href="{{ route('catalog', ['path' => $categorySlug]) }}"
                               class="block px-4 py-2 rounded-lg bg-black text-white">
                                {{ $category->name }}
                            </a>
                        </li>

                        {{-- остальные --}}
                        @foreach ($categories as $cat)
                            <li>
                                <a href="{{ route('catalog', ['path' => $cat->getSlug()]) }}"
                                   class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                                    {{ $cat->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Фильтры --}}
                <div>
                    <h2 class="text-xl font-semibold mb-4">Фильтры</h2>

                    @php
                        $selectedSlugs = $selectedValueSlugs ?? [];
                        $facetsMap     = $facets ?? [];
                        $minPrice      = $priceBounds['min'] ?? null;
                        $maxPrice      = $priceBounds['max'] ?? null;
                    @endphp

                    {{-- Цена (по GET, basePrice) --}}
                    <form method="GET" class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium mb-1">Цена</label>
                            <div class="flex gap-2">
                                <input
                                    type="number"
                                    name="price_from"
                                    placeholder="от"
                                    @if($minPrice !== null) min="{{ (int) $minPrice }}" @endif
                                    @if($maxPrice !== null) max="{{ (int) $maxPrice }}" @endif
                                    value="{{ request('price_from') }}"
                                    class="w-full border rounded-lg px-3 py-2">
                                <input
                                    type="number"
                                    name="price_to"
                                    placeholder="до"
                                    @if($minPrice !== null) min="{{ (int) $minPrice }}" @endif
                                    @if($maxPrice !== null) max="{{ (int) $maxPrice }}" @endif
                                    value="{{ request('price_to') }}"
                                    class="w-full border rounded-lg px-3 py-2">
                            </div>

                            @if($minPrice !== null && $maxPrice !== null)
                                <p class="mt-1 text-xs text-gray-500">
                                    Диапазон: {{ number_format($minPrice, 0, ',', ' ') }}
                                    – {{ number_format($maxPrice, 0, ',', ' ') }} ₽
                                </p>
                            @endif
                        </div>

                        <button type="submit"
                                class="w-full bg-black text-white py-2 rounded-lg hover:bg-gray-800">
                            Применить
                        </button>
                    </form>

                    {{-- Атрибуты --}}
                    <div class="space-y-6">
                        @foreach($attributes as $attribute)
                            @if($attribute->values->isEmpty())
                                @continue
                            @endif

                            @php
                                $typeFront = (int) ($attribute->type_front ?? 1);

                                // все slug'и значений этого атрибута — пригодятся,
                                // чтобы очищать остальные при выборе radio/select/select-option
                                $attributeValueSlugs = $attribute->values
                                    ->map(fn($v) => $v->getSlug())
                                    ->filter()
                                    ->values()
                                    ->all();
                            @endphp

                            <div>
                                <div class="text-sm font-semibold mb-2">
                                    {{ $attribute->name }}
                                </div>

                                {{-- SELECT --}}
                                @if($typeFront === 3)
                                    @php
                                        // текущий выбранный slug для этого атрибута (если есть)
                                        $currentSlugForAttr = null;
                                        foreach ($attributeValueSlugs as $slug) {
                                            if (in_array($slug, $selectedSlugs, true)) {
                                                $currentSlugForAttr = $slug;
                                                break;
                                            }
                                        }

                                        // URL для сброса атрибута: убираем все его slug'и
                                        $resetSlugs = array_values(array_diff($selectedSlugs, $attributeValueSlugs));
                                        sort($resetSlugs, SORT_STRING);
                                        $resetPath = $categorySlug . (count($resetSlugs) ? '/' . implode('/', $resetSlugs) : '');
                                        $resetUrl  = route('catalog',
                                            array_merge(['path' => $resetPath], request()->query())
                                        );
                                    @endphp

                                    <select
                                        class="w-full border rounded-lg px-3 py-2 text-sm"
                                        onchange="if (this.value) window.location = this.value;">
                                        <option value="{{ $resetUrl }}">
                                            Не выбрано
                                        </option>

                                        @foreach($attribute->values as $value)
                                            @php
                                                $valueSlug = $value->getSlug();
                                                $isActive  = $currentSlugForAttr === $valueSlug;

                                                $canUse     = $facetsMap[$value->id] ?? true;
                                                $isDisabled = ! $canUse && ! $isActive;

                                                // новый набор slug'ов для выбора этого значения
                                                $newSlugs = array_values(array_diff($selectedSlugs, $attributeValueSlugs));
                                                $newSlugs[] = $valueSlug;
                                                sort($newSlugs, SORT_STRING);

                                                $path = $categorySlug . (count($newSlugs) ? '/' . implode('/', $newSlugs) : '');
                                                $url  = route('catalog',
                                                    array_merge(['path' => $path], request()->query())
                                                );
                                            @endphp

                                            <option
                                                value="{{ $url }}"
                                                @if($isActive) selected @endif
                                                @if($isDisabled) disabled @endif
                                            >
                                                {{ $value->value }}
                                            </option>
                                        @endforeach
                                    </select>

                                    {{-- LIST (checkbox / radio) --}}
                                @else
                                    <div class="space-y-1">
                                        @foreach($attribute->values as $value)
                                            @php
                                                $valueSlug = $value->getSlug();
                                                $isActive  = in_array($valueSlug, $selectedSlugs, true);

                                                $canUse     = $facetsMap[$value->id] ?? true;
                                                $isDisabled = ! $canUse && ! $isActive;

                                                $newSlugs = $selectedSlugs;
                                                $url      = null;

                                                if (! $isDisabled) {
                                                    if ($typeFront !== 1 && ! $isActive) {
                                                        // RADIO: убираем все другие значения этого атрибута
                                                        $newSlugs = array_values(array_diff($newSlugs, $attributeValueSlugs));
                                                    }

                                                    // toggle текущего значения
                                                    if ($isActive) {
                                                        // снимаем фильтр
                                                        $newSlugs = array_values(array_diff($newSlugs, [$valueSlug]));
                                                    } else {
                                                        // чекбоксы и радио добавляют значение
                                                        $newSlugs[] = $valueSlug;
                                                    }

                                                    sort($newSlugs, SORT_STRING);

                                                    $path = $categorySlug . (count($newSlugs) ? '/' . implode('/', $newSlugs) : '');

                                                    // сохраняем GET-параметры (цена и т.п.)
                                                    $url = route('catalog',
                                                        array_merge(['path' => $path], request()->query())
                                                    );
                                                }
                                            @endphp

                                            @if($isDisabled)
                                                <span class="flex items-center gap-2 text-sm text-gray-400 cursor-not-allowed opacity-60">
                                                    <span class="w-4 h-4 border
                                                            {{ $typeFront === 2 ? 'rounded-full' : 'rounded' }}
                                                            bg-gray-100 border-gray-200"></span>
                                                    <span>{{ $value->value }}</span>
                                                </span>
                                            @else
                                                <a href="{{ $url }}"
                                                   class="flex items-center gap-2 text-sm">
                                                    <span class="w-4 h-4 border
                                                            {{ $typeFront === 2 ? 'rounded-full' : 'rounded' }}
                                                            {{ $isActive ? 'bg-black border-black' : 'bg-white' }}">
                                                    </span>
                                                    <span class="{{ $isActive ? 'font-semibold' : '' }}">
                                                        {{ $value->value }}
                                                    </span>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                </div>

            </aside>

            {{-- PRODUCTS GRID --}}
            <section class="flex-1">

                {{-- SORT BAR --}}
                @php
                    $currentSort   = request('sort', 'popular_desc');
                    $selectedSlugs = $selectedValueSlugs ?? [];

                    $basePath = $categorySlug . (count($selectedSlugs) ? '/' . implode('/', $selectedSlugs) : '');
                    $baseQuery = request()->query();

                    $makeSortUrl = function (string $sortValue) use ($basePath, $baseQuery) {
                        $query = array_merge($baseQuery, ['sort' => $sortValue]);
                        return route('catalog', array_merge(['path' => $basePath], $query));
                    };
                @endphp

                <div class="flex items-center justify-between mb-6">
                    <div class="text-sm text-gray-500">
                        Найдено {{ $items->total() }} товаров
                    </div>

                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-gray-500">Сортировка:</span>

                        @php
                            $sortOptions = [
                                'popular_desc' => 'По популярности',
                                'price_asc'    => 'Сначала дешевле',
                                'price_desc'   => 'Сначала дороже',
                            ];
                        @endphp

                        @foreach($sortOptions as $value => $label)
                            @php
                                $isActiveSort = $currentSort === $value;
                                $url = $makeSortUrl($value);
                            @endphp

                            <a href="{{ $url }}"
                               class="px-3 py-1 rounded-full border text-xs
                                    {{ $isActiveSort
                                        ? 'bg-black text-white border-black'
                                        : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- GRID --}}
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

                    @forelse ($items as $item)
                        @php
                            $price = optional($item->basePrice)->price;
                        @endphp

                        <div class="border rounded-xl overflow-hidden hover:shadow-md transition">
                            <div class="p-4">
                                <a class="block font-medium text-lg mb-1 hover:text-black">
                                    {{ $item->name }}
                                </a>

                                @if(!is_null($price))
                                    <div class="text-gray-700 mb-3">
                                        {{ number_format($price, 0, ',', ' ') }} ₽
                                    </div>
                                @endif

                                <a class="block w-full text-center bg-black text-white py-2 rounded-lg hover:bg-gray-800">
                                    Подробнее
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">Товары не найдены.</p>
                    @endforelse

                </div>

                {{-- Пагинация --}}
                <div class="mt-8">
                    {{ $items->links() }}
                </div>
                {{-- ОПИСАНИЯ ПОСАДОЧНОЙ / КАТЕГОРИИ (ТОЛЬКО НА ПЕРВОЙ СТРАНИЦЕ) --}}
                @if($items->currentPage() === 1)
                    @if(isset($landing) && $landing && $landing->content)
                        {{-- Посадочная страница --}}
                        <div class="mb-6 prose max-w-none">
                            {!! $landing->content !!}
                        </div>
                    @elseif(!empty($category->description))
                        {{-- Базовое описание категории --}}
                        <div class="mb-6 prose max-w-none text-sm text-gray-700">
                            {!! $category->description !!}
                        </div>
                    @endif
                @endif
            </section>

        </div>
    </div>
@endsection
