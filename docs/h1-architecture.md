# Управление заголовками H1 в разных типах страниц

## Архитектура

Layout `MainLayout.vue` предоставляет **именованный слот `#title`** для гибкого управления заголовками на разных страницах.

### Структура MainLayout.vue

```vue
<main class="flex-1">
    <div class="mx-auto max-w-6xl px-4 py-6 md:py-10">
        <!-- Breadcrumbs всегда сверху -->
        <Breadcrumbs v-if="breadcrumbs.length" />

        <!-- Слот для заголовка (можно переопределить) -->
        <slot name="title">
            <!-- Дефолтный h1 если не переопределён -->
            <h1 v-if="pageTitle">{{ pageTitle }}</h1>
        </slot>

        <!-- Основной контент -->
        <slot />
    </div>
</main>
```

## Примеры использования

### 1. Статические страницы (по умолчанию)

Если НЕ переопределять слот `#title`, используется дефолтный заголовок из `pageTitle`.

**resources/js/Pages/Page/Show.vue:**
```vue
<template>
    <!-- Переопределяем слот title -->
    <template #title>
        <h1 class="text-3xl font-bold mb-6">
            {{ page.title }}
        </h1>
    </template>

    <!-- Основной контент (без лишнего div) -->
    <BlocksRenderer :blocks="page.blocks || []" />
</template>
```

⚠️ **Важно:** Если используете слот `#title`, основной контент должен быть либо:
- Один корневой элемент (например, `<div>`)
- Или компонент (например, `<BlocksRenderer />`)
- Нельзя иметь несколько корневых элементов без обёртки

### 2. Каталог (заголовок перед товарами)

**resources/js/Pages/Catalog/Index.vue:**
```vue
<template>
    <!-- Переопределяем слот title для каталога -->
    <template #title>
        <h1 v-if="category?.name" class="text-2xl md:text-3xl font-bold mb-6">
            {{ category.name }}
        </h1>
    </template>

    <div class="bg-slate-50/40">
        <!-- Фильтры и товары -->
        <CatalogSidebar />
        <CatalogProducts />
    </div>
</template>
```

### 3. Страница товара (заголовок с мета-информацией)

**resources/js/Pages/Product/Show.vue:**
```vue
<template>
    <!-- Переопределяем слот title для товара -->
    <template #title>
        <div class="mb-6">
            <div class="text-sm text-slate-500 mb-2">
                Артикул: {{ product.sku }}
            </div>
            <h1 class="text-2xl md:text-4xl font-bold">
                {{ product.name }}
            </h1>
            <div class="mt-2 flex items-center gap-3">
                <span class="text-sm text-slate-500">
                    Код товара: {{ product.id }}
                </span>
                <span v-if="product.in_stock" class="text-sm text-emerald-600">
                    В наличии
                </span>
            </div>
        </div>
    </template>

    <!-- Контент товара -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Галерея, цена, описание -->
    </div>
</template>
```

## Преимущества подхода

✅ **Гибкость** - каждая страница контролирует свой заголовок  
✅ **Консистентность** - Breadcrumbs всегда в одном месте  
✅ **DRY** - Layout остаётся простым, страницы управляют своей спецификой  
✅ **SEO** - H1 всегда уникальный и подходящий для страницы  
✅ **Типизация** - Можно легко добавлять разметку schema.org для каждого типа

## Когда использовать дефолтный заголовок

Если страница простая и не требует кастомизации, можно просто передать `pageTitle` через Inertia props:

```php
// Controller
return Inertia::render('Page/Show', [
    'pageTitle' => 'О компании',
    'page' => $page
]);
```

И НЕ переопределять слот `#title` - будет использован дефолтный из Layout.

## Резюме

- **Layout** управляет структурой (breadcrumbs, контейнеры)
- **Страницы** управляют заголовками через слот `#title`
- **Каждый тип страницы** может иметь свою разметку H1

