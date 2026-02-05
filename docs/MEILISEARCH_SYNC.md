# Синхронизация Meilisearch

## Команда `meilisearch:sync`

Команда для автоматической синхронизации настроек индексов Meilisearch и импорта моделей в поисковый индекс.

### Использование

#### Полная синхронизация (настройки + импорт данных)

```bash
vendor/bin/sail artisan meilisearch:sync
```

Эта команда выполнит:
1. Синхронизацию настроек индексов из конфигурации `config/scout.php`
2. Импорт всех моделей Product в индекс Meilisearch

#### Только синхронизация настроек (без импорта)

```bash
vendor/bin/sail artisan meilisearch:sync --no-import
```

Полезно, когда нужно обновить только настройки индекса (filterableAttributes, sortableAttributes и т.д.) без переиндексации данных.

#### Полная переиндексация (с очисткой индекса)

```bash
vendor/bin/sail artisan meilisearch:sync --flush
```

Сначала полностью очищает индекс, затем импортирует все данные заново. Используйте при необходимости "чистого листа".

### Конфигурация

Настройки индекса определены в файле `config/scout.php`:

```php
'meilisearch' => [
    'index-settings' => [
        \App\Models\Product::class => [
            'filterableAttributes' => [
                'category_ids',
                'main_category_id',
                'price',
                'attribute_value_ids',
            ],
            'sortableAttributes' => [
                'price',
                'popular',
            ],
        ],
    ],
],
```

### Модели для индексации

В текущей конфигурации индексируется модель:
- **Product** - товары с категориями, ценами и атрибутами

### Важные замечания

1. Модель `Product` использует трейт `Searchable` из Laravel Scout
2. Метод `toSearchableArray()` определяет, какие данные попадают в индекс
3. Метод `shouldBeSearchable()` гарантирует, что удаленные товары не попадают в индекс
4. При массовом импорте через `scout:import` индексируются только активные товары (не soft-deleted)

### Отдельные Scout команды

Если нужен более детальный контроль, можно использовать стандартные Scout команды:

```bash
# Только синхронизация настроек
vendor/bin/sail artisan scout:sync-index-settings

# Только импорт модели
vendor/bin/sail artisan scout:import "App\Models\Product"

# Очистка индекса
vendor/bin/sail artisan scout:flush "App\Models\Product"

# Импорт через очередь (для больших объемов)
vendor/bin/sail artisan scout:queue-import "App\Models\Product"
```

### Производительность

Для больших объемов данных (>10000 записей) рекомендуется использовать очередь:

```bash
vendor/bin/sail artisan scout:queue-import "App\Models\Product"
```

Это разбивает импорт на задания и выполняет их через систему очередей Laravel.
