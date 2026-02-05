# Быстрый старт - Поиск товаров

## 🚀 Запуск поиска

### Шаг 1: Проиндексируйте товары

```bash
vendor/bin/sail artisan scout:import "App\Models\Product"
```

Эта команда добавит все товары в поисковый индекс Meilisearch.

### Шаг 2: Синхронизируйте настройки индекса

```bash
vendor/bin/sail artisan meilisearch:sync
```

Эта команда настроит фильтры и сортировки для оптимальной работы поиска.

### Шаг 3: Проверьте работу

Откройте сайт и начните вводить запрос в поисковую строку в header!

## ✅ Проверка работы через tinker

```bash
vendor/bin/sail artisan tinker
```

```php
// Проверка поиска
>>> App\Models\Product::search('pandora')->take(3)->get()->pluck('name');

// Проверка API контроллера
>>> $request = new Illuminate\Http\Request(['q' => 'pandora']);
>>> $controller = app(App\Http\Controllers\Api\SearchController::class);
>>> $response = $controller->search($request);
>>> $response->getData();
```

## 🔧 Troubleshooting

### Если поиск не работает:

1. **Проверьте, запущен ли Meilisearch:**
   ```bash
   vendor/bin/sail ps | grep meilisearch
   ```

2. **Переиндексируйте товары:**
   ```bash
   vendor/bin/sail artisan scout:flush "App\Models\Product"
   vendor/bin/sail artisan scout:import "App\Models\Product"
   ```

3. **Проверьте настройки Scout в `.env`:**
   ```
   SCOUT_DRIVER=meilisearch
   MEILISEARCH_HOST=http://meilisearch:7700
   MEILISEARCH_KEY=
   ```

4. **Очистите кэш:**
   ```bash
   vendor/bin/sail artisan cache:clear
   vendor/bin/sail artisan route:clear
   vendor/bin/sail artisan config:clear
   ```

## 📝 Что уже сделано:

✅ Контроллер `Api/SearchController` с методом `search()`  
✅ Роут `GET /api/search?q={query}`  
✅ Компонент `Header.vue` с поиском в реальном времени  
✅ Debounce 300ms для оптимизации запросов  
✅ Поддержка desktop и mobile версий  
✅ Популярные запросы из БД (управление через Filament)  

## 📚 Полная документация

См. файл `docs/SEARCH.md` для подробной информации.
