# Inertia SSR Configuration

## Обзор

Inertia SSR (Server-Side Rendering) настроен и работает в отдельном Docker контейнере для максимальной производительности и изоляции.

## Архитектура

### Локальная разработка
- SSR сервер запускается на `http://127.0.0.1:13714`
- Запуск: `vendor/bin/sail artisan inertia:start-ssr`
- Остановка: `vendor/bin/sail artisan inertia:stop-ssr`
- Проверка: `vendor/bin/sail artisan inertia:check-ssr`

### Production (Docker)
- Отдельный Docker контейнер `ssr` в `compose.prod.yaml`
- Автоматический запуск и перезапуск при падении
- Health check для мониторинга состояния
- URL: `http://ssr:13714` (внутри Docker сети)

## Конфигурация

### Переменные окружения

```env
# .env (локально)
INERTIA_SSR_ENABLED=true
INERTIA_SSR_URL=http://127.0.0.1:13714

# .env (production) - устанавливается автоматически через compose.prod.yaml
INERTIA_SSR_ENABLED=true
INERTIA_SSR_URL=http://ssr:13714
```

### Конфигурационный файл

`config/inertia.php`:
```php
'ssr' => [
    'enabled' => env('INERTIA_SSR_ENABLED', true),
    'url' => env('INERTIA_SSR_URL', env('APP_ENV') === 'production' ? 'http://ssr:13714' : 'http://127.0.0.1:13714'),
    'ensure_bundle_exists' => env('INERTIA_SSR_ENSURE_BUNDLE_EXISTS', env('APP_ENV') !== 'production'),
],
```

## Docker контейнер SSR

### compose.prod.yaml
```yaml
ssr:
  image: ghcr.io/${GITHUB_REPOSITORY}:latest
  restart: unless-stopped
  command: ["node", "/var/www/html/bootstrap/ssr/ssr.mjs"]
  healthcheck:
    test: ["CMD", "php", "/var/www/html/artisan", "inertia:check-ssr"]
```

## Сборка

### Vite конфигурация
```js
// vite.config.js
laravel({
    input: ['resources/css/app.css', 'resources/js/app.js'],
    ssr: 'resources/js/ssr.js',  // 👈 SSR entry point
    refresh: true,
})
```

### SSR bundle
```bash
# Локально
vendor/bin/sail npm run build

# В CI/CD (автоматически в Dockerfile)
npm run build
```

Создается файл: `bootstrap/ssr/ssr.mjs`

## Deployment

### Автоматический процесс (deploy.sh)

1. Публикация Livewire/Filament ассетов
2. Перезапуск SSR контейнера: `docker compose restart ssr`
3. Health check: `inertia:check-ssr`

### Мониторинг

```bash
# Проверка состояния SSR
docker compose -f compose.prod.yaml exec ssr php /var/www/html/artisan inertia:check-ssr

# Логи SSR контейнера
docker compose -f compose.prod.yaml logs -f ssr

# Статус контейнера
docker compose -f compose.prod.yaml ps ssr
```

## Troubleshooting

### SSR не отвечает

1. **Проверьте статус контейнера:**
   ```bash
   docker compose -f compose.prod.yaml ps ssr
   ```

2. **Проверьте логи:**
   ```bash
   docker compose -f compose.prod.yaml logs --tail=50 ssr
   ```

3. **Перезапустите контейнер:**
   ```bash
   docker compose -f compose.prod.yaml restart ssr
   ```

### SSR bundle не найден

Убедитесь, что выполнена сборка:
```bash
vendor/bin/sail npm run build
```

Проверьте наличие файла:
```bash
ls -la bootstrap/ssr/ssr.mjs
```

### Mixed Content ошибки

Убедитесь, что `APP_URL` в `.env` использует `https://`:
```env
APP_URL=https://test.alarmstyle.ru
```

## Отключение SSR

Если нужно временно отключить SSR:

```env
# .env
INERTIA_SSR_ENABLED=false
```

Или остановите контейнер:
```bash
docker compose -f compose.prod.yaml stop ssr
```

## Полезные команды

```bash
# Локальная разработка
vendor/bin/sail artisan inertia:start-ssr    # Запуск
vendor/bin/sail artisan inertia:stop-ssr     # Остановка
vendor/bin/sail artisan inertia:check-ssr    # Проверка

# Production
docker compose -f compose.prod.yaml restart ssr              # Перезапуск
docker compose -f compose.prod.yaml logs -f ssr              # Логи
docker compose -f compose.prod.yaml exec ssr sh              # Shell в контейнере
docker compose -f compose.prod.yaml exec ssr php artisan inertia:check-ssr  # Health check
```

## Производительность

SSR значительно улучшает:
- **SEO** - поисковые роботы получают готовый HTML
- **Первую загрузку** - пользователь видит контент быстрее
- **Social sharing** - правильные превью в соцсетях

Без SSR все эти преимущества теряются, так как контент рендерится только на клиенте.
