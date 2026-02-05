# Поиск товаров

## Описание

Модуль полнотекстового поиска товаров через Scout (Meilisearch) с выдачей результатов в реальном времени.

## Архитектура

### Backend

**Контроллер:** `app/Http/Controllers/Api/SearchController.php`

**Роут:** `GET /api/search?q={query}`

**Модель:** `app/Models/Product.php` (использует Laravel Scout)

### Frontend

**Компонент:** `resources/js/Components/Header.vue`

Поиск работает в header с debounce 300ms для оптимизации запросов.

## API

### Endpoint поиска

```
GET /api/search?q={query}
```

**Параметры:**
- `q` (string, required) - поисковый запрос (минимум 2 символа)

**Ответ:**
```json
{
    "results": [
        {
            "id": 1,
            "name": "Pandora DXL 5000 PRO V2",
            "slug": "pandora-dxl-5000-pro-v2",
            "url": "http://localhost/product/pandora-dxl-5000-pro-v2",
            "image": "http://localhost/storage/products/image.jpg",
            "price": 25990,
            "category": "Автосигнализации",
            "brand": "Pandora"
        }
    ],
    "total": 1
}
```

## Индексация товаров

### Индексация всех товаров

```bash
vendor/bin/sail artisan scout:import "App\Models\Product"
```

### Синхронизация настроек индекса

```bash
vendor/bin/sail artisan meilisearch:sync
```

### Очистка индекса

```bash
vendor/bin/sail artisan scout:flush "App\Models\Product"
```

## Настройки поиска

Товары индексируются со следующими полями (см. `Product::toSearchableArray()`):

- `name` - название товара
- `brand` - бренд
- `category_ids` - ID категорий
- `price` - цена
- `gps`, `gsm`, `auto` - характеристики
- `attribute_value_ids` - ID значений атрибутов
- `popular` - популярность
- `slug` - URL slug

## Frontend использование

Поиск автоматически доступен в header:

1. Пользователь вводит запрос (минимум 2 символа)
2. Через 300ms отправляется AJAX запрос
3. Результаты отображаются в dropdown
4. Максимум 6 результатов в быстром поиске
5. Кнопка "Посмотреть все результаты" ведет на страницу каталога

### Состояния UI

- **Пустой запрос** - показываются популярные запросы
- **Загрузка** - spinner
- **Результаты** - список товаров с изображениями
- **Нет результатов** - сообщение "Ничего не найдено"

## Оптимизация

### Debounce

Запросы к API выполняются только после 300ms с момента последнего ввода.

### Лимит результатов

В быстром поиске показывается максимум 6 товаров для производительности.

### Кэширование

Scout автоматически кэширует результаты поиска на стороне Meilisearch.

## Troubleshooting

### Поиск не работает

1. Проверьте, запущен ли Meilisearch:
```bash
vendor/bin/sail ps
```

2. Проверьте индексацию:
```bash
vendor/bin/sail artisan tinker
>>> App\Models\Product::search('test')->count()
```

3. Переиндексируйте товары:
```bash
vendor/bin/sail artisan scout:flush "App\Models\Product"
vendor/bin/sail artisan scout:import "App\Models\Product"
```

### Некорректные результаты

Синхронизируйте настройки индекса:
```bash
vendor/bin/sail artisan meilisearch:sync
```

### Проверка роута

```bash
vendor/bin/sail artisan route:list --name=api.search
```

## Расширение

### Добавление фильтров

В контроллере `SearchController@search` можно добавить дополнительные параметры:

```php
$results = Product::search($query)
    ->where('price', '>', 10000)
    ->where('category_id', 5)
    ->take(6)
    ->get();
```

### Изменение лимита результатов

В `SearchController.php`:
```php
->take(10) // вместо 6
```

### Добавление сортировки

```php
->orderBy('popular', 'desc')
->orderBy('price', 'asc')
```
