# Популярные поисковые запросы

## Описание

Модуль для управления популярными поисковыми запросами, отображаемыми в выпадающем меню поиска.

## Модель

**Файл:** `app/Models/PopularSearch.php`

### Поля

- `query` (string) - текст поискового запроса
- `order` (integer) - порядок отображения (чем меньше, тем выше)
- `is_active` (boolean) - активность записи

### Скоупы

- `active()` - выбрать только активные записи
- `ordered()` - сортировка по полю order

## Управление через Filament

**Путь:** Admin Panel → Популярные запросы

**Ресурс:** `app/Filament/Resources/PopularSearches/PopularSearchResource.php`

### Возможности

- ✅ Создание новых запросов
- ✅ Редактирование существующих
- ✅ Изменение порядка отображения (drag & drop)
- ✅ Активация/деактивация записей
- ✅ Удаление записей
- ✅ Фильтрация по активности

## Использование во frontend

Популярные запросы автоматически передаются во все страницы через middleware `HandleInertiaRequests`.

```javascript
// В любом Vue компоненте
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const popularSearches = computed(() => page.props.popularSearches ?? [])
```

## Начальные данные

Для заполнения начальными данными выполните:

```bash
vendor/bin/sail artisan db:seed --class=PopularSearchSeeder
```

Начальные запросы:
- Автосигнализация
- GPS-трекер
- Автозапуск
- Видеорегистратор

## База данных

**Таблица:** `popular_searches`

**Миграция:** `database/migrations/2026_02_04_203625_create_popular_searches_table.php`

### Структура таблицы

```sql
CREATE TABLE popular_searches (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    query VARCHAR(255) NOT NULL,
    `order` INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

## API

### Получение активных запросов

```php
use App\Models\PopularSearch;

$searches = PopularSearch::active()
    ->ordered()
    ->pluck('query')
    ->toArray();
```

### Создание нового запроса

```php
PopularSearch::create([
    'query' => 'Новый запрос',
    'order' => 5,
    'is_active' => true,
]);
```

### Обновление порядка

```php
$search = PopularSearch::find(1);
$search->update(['order' => 1]);
```
