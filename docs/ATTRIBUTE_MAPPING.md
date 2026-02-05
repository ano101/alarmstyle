# Гибкая система маппинга атрибутов продукта

## Описание

Реализована гибкая система настройки маппинга атрибутов продуктов через Filament админ-панель. 
Теперь вместо жестко закодированных ID атрибутов в `ProductPresenter`, соответствие настраивается через интерфейс.

## Что изменено

### 1. ProductPresenter (`app/Models/Presenters/ProductPresenter.php`)
- Добавлены методы для работы с маппингом атрибутов из настроек:
  - `getAttributeMapping()` - получает маппинг из таблицы settings
  - `getMappedAttributeId()` - возвращает ID атрибута по ключу
- Обновлены методы `brand()`, `gps()`, `gsm()`, `autoStart()` для использования настроек

### 2. Страница настроек в Filament (`app/Filament/Pages/ProductAttributeMapping.php`)
- Создана новая страница для управления маппингом атрибутов
- Удобный интерфейс с выпадающими списками и поиском
- Автоматическое сохранение в таблицу `settings`

### 3. Seeder (`database/seeders/AttributeMappingSeeder.php`)
- Создан сидер для инициализации настроек с текущими значениями ID

## Использование

### Настройка маппинга через админ-панель

1. Откройте Filament админ-панель
2. Перейдите в раздел "Настройки" → "Маппинг атрибутов"
3. Выберите нужные атрибуты для каждого ключа:
   - **Бренд (brand)** - атрибут для метода `brand()`
   - **GPS (gps)** - атрибут для метода `gps()`
   - **GSM (gsm)** - атрибут для метода `gsm()`
   - **Автозапуск (auto_start)** - атрибут для метода `autoStart()`
4. Нажмите "Сохранить" (или используйте горячую клавишу Ctrl+S / Cmd+S)

### Программное использование

```php
// Получить бренд продукта
$brand = $product->present()->brand();

// Проверить наличие GPS
$hasGps = $product->present()->gps();

// Проверить наличие GSM
$hasGsm = $product->present()->gsm();

// Проверить наличие автозапуска
$hasAutoStart = $product->present()->autoStart();
```

### Добавление новых маппингов

Если нужно добавить новый маппинг атрибута:

1. Добавьте новый select в `ProductAttributeMapping::form()`:
```php
Forms\Components\Select::make('mapping.new_key')
    ->label('Название атрибута')
    ->options($attributes)
    ->searchable()
    ->helperText('Описание'),
```

2. Добавьте метод в `ProductPresenter`:
```php
public function newAttribute(): mixed
{
    $attributeId = $this->getMappedAttributeId('new_key');
    return $attributeId ? $this->attr($attributeId, 'default') : 'default';
}
```

3. Обновите значение по умолчанию в `getAttributeMapping()` и `AttributeMappingSeeder`

## Запуск сидера

Для инициализации настроек с текущими значениями:

```bash
vendor/bin/sail artisan db:seed --class=AttributeMappingSeeder
```

## Преимущества

✅ Гибкость - маппинг настраивается через интерфейс без изменения кода  
✅ Удобство - поиск по атрибутам, понятные подписи  
✅ Безопасность - валидация и кэширование настроек  
✅ Расширяемость - легко добавлять новые маппинги  
✅ Совместимость - работает с существующим кодом на фронте и в мейлках
