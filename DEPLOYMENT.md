# AlarmStyle Production Deployment

## Структура проекта

Проект теперь поддерживает два режима:
- **Локальная разработка**: используется Laravel Sail (`compose.yaml`)
- **Production**: используется отдельная Docker-конфигурация (`compose.prod.yaml`)

## Production компоненты

### Docker контейнеры:
- **nginx** - веб-сервер (Alpine Linux)
- **app** - PHP 8.5-FPM + Supervisor (управляет Horizon и Schedule)
- **mysql** - база данных MySQL 8.4
- **redis** - кеш и очереди
- **meilisearch** - поисковый движок для Scout

### Supervisor процессы:
- `php-fpm` - обработка PHP запросов
- `horizon` - Laravel Horizon для управления очередями
- `schedule-work` - Laravel Scheduler для cron задач

## Настройка production сервера

### 1. Установите Docker и Docker Compose на сервере:
```bash
# Ubuntu/Debian
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER
```

### 2. Клонируйте репозиторий:
```bash
git clone https://github.com/your-username/alarmstyle.git
cd alarmstyle
```

### 3. Создайте .env файл:
```bash
cp .env.production.example .env
nano .env
```

Обязательно настройте:
- `APP_KEY` (сгенерируйте через `php artisan key:generate`)
- `APP_URL`
- `DB_PASSWORD`
- `REDIS_PASSWORD`
- `MEILISEARCH_KEY`
- Настройки почты

### 4. Создайте необходимые директории:
```bash
mkdir -p storage/logs/nginx
chmod -R 775 storage bootstrap/cache
```

### 5. Запустите контейнеры:
```bash
docker compose -f compose.prod.yaml up -d --build
```

### 6. Выполните начальную настройку:
```bash
# Миграции
docker compose -f compose.prod.yaml exec app php artisan migrate --force

# Кеширование
docker compose -f compose.prod.yaml exec app php artisan config:cache
docker compose -f compose.prod.yaml exec app php artisan route:cache
docker compose -f compose.prod.yaml exec app php artisan view:cache
docker compose -f compose.prod.yaml exec app php artisan filament:cache-components

# Индексация для Scout (если нужно)
docker compose -f compose.prod.yaml exec app php artisan scout:sync-index-settings
docker compose -f compose.prod.yaml exec app php artisan scout:import "App\Models\Product"
```

## GitHub Actions автодеплой

### Настройка GitHub Secrets

В настройках репозитория (Settings → Secrets and variables → Actions) добавьте:

- `PROD_HOST` - IP адрес или домен сервера
- `PROD_USERNAME` - SSH пользователь
- `PROD_SSH_KEY` - приватный SSH ключ
- `PROD_SSH_PORT` - порт SSH (по умолчанию 22)
- `PROD_APP_PATH` - путь к приложению на сервере (например: `/var/www/alarmstyle`)
- `PROD_APP_URL` - URL приложения для health check

### Автоматический деплой

При каждом push в ветку `main` GitHub Actions:
1. Собирает Docker образ
2. Публикует в GitHub Container Registry
3. Подключается к серверу по SSH
4. Обновляет код и Docker образы
5. Выполняет миграции и кеширование
6. Перезапускает Horizon
7. Проверяет работоспособность приложения

## Полезные команды

### Мониторинг логов:
```bash
# Все логи
docker compose -f compose.prod.yaml logs -f

# Только приложение
docker compose -f compose.prod.yaml logs -f app

# Nginx
docker compose -f compose.prod.yaml logs -f nginx

# Horizon
docker compose -f compose.prod.yaml exec app tail -f storage/logs/horizon.log
```

### Управление контейнерами:
```bash
# Остановить
docker compose -f compose.prod.yaml stop

# Запустить
docker compose -f compose.prod.yaml start

# Перезапустить
docker compose -f compose.prod.yaml restart

# Полностью пересобрать
docker compose -f compose.prod.yaml up -d --build --force-recreate
```

### Laravel команды:
```bash
# Artisan
docker compose -f compose.prod.yaml exec app php artisan [command]

# Tinker
docker compose -f compose.prod.yaml exec app php artisan tinker

# Horizon статистика
docker compose -f compose.prod.yaml exec app php artisan horizon:status
```

### Очистка кеша:
```bash
docker compose -f compose.prod.yaml exec app php artisan cache:clear
docker compose -f compose.prod.yaml exec app php artisan config:clear
docker compose -f compose.prod.yaml exec app php artisan route:clear
docker compose -f compose.prod.yaml exec app php artisan view:clear
```

## Локальная разработка (без изменений)

Для локальной разработки продолжайте использовать Laravel Sail:

```bash
# Запуск
vendor/bin/sail up -d

# Остановка
vendor/bin/sail stop

# Artisan команды
vendor/bin/sail artisan [command]
```

## Безопасность

### Рекомендации:
1. Используйте сильные пароли для БД и Redis
2. Настройте SSL/TLS сертификаты (Let's Encrypt)
3. Ограничьте доступ к портам через firewall
4. Регулярно обновляйте Docker образы
5. Настройте автоматические бэкапы БД

### Добавление SSL (example с Certbot):
```nginx
# В docker/nginx/conf.d/default.conf добавьте:
server {
    listen 443 ssl http2;
    server_name yourdomain.com;
    
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    
    # ... остальная конфигурация
}
```

## Мониторинг

### Проверка здоровья:
```bash
# Health check endpoint
curl http://your-domain/health

# Статус контейнеров
docker compose -f compose.prod.yaml ps

# Использование ресурсов
docker stats
```

### Horizon Dashboard
Доступен по адресу: `https://yourdomain.com/horizon`

Настройте авторизацию в `app/Providers/HorizonServiceProvider.php`

## Бэкапы

### MySQL бэкап:
```bash
docker compose -f compose.prod.yaml exec mysql mysqldump -u root -p${DB_PASSWORD} ${DB_DATABASE} > backup-$(date +%Y%m%d).sql
```

### Восстановление:
```bash
docker compose -f compose.prod.yaml exec -T mysql mysql -u root -p${DB_PASSWORD} ${DB_DATABASE} < backup.sql
```

## Масштабирование

Для увеличения производительности можно:
1. Увеличить количество PHP-FPM workers в `docker/php/www.conf`
2. Настроить балансировку нагрузки с несколькими app контейнерами
3. Вынести MySQL и Redis на отдельные серверы
4. Использовать CDN для статики

## Поддержка

При проблемах проверьте:
1. Логи контейнеров: `docker compose -f compose.prod.yaml logs`
2. Логи Laravel: `storage/logs/laravel.log`
3. Логи Nginx: `storage/logs/nginx/`
4. Статус Horizon: `php artisan horizon:status`
