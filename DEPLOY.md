# Production Deploy

## Быстрый старт

### 1. Настройка .env
```bash
cp .env.production.example .env
nano .env
```

**Обязательно настроить:**
- `APP_KEY` - сгенерировать командой ниже
- `APP_URL` - ваш домен
- `APP_PORT=8080` - HTTP порт (нестандартный, чтобы не конфликтовать с нативным Nginx)
- `APP_SSL_PORT=8443` - HTTPS порт
- `DB_PASSWORD` - надежный пароль для MySQL
- `REDIS_PASSWORD` - надежный пароль для Redis
- `MEILISEARCH_KEY` - ключ для Meilisearch (минимум 16 символов)
- Email настройки (MAIL_HOST, MAIL_PORT, etc.)

### 2. Запуск
```bash
# Сгенерировать APP_KEY
docker compose -f compose.prod.yaml run --rm app php artisan key:generate --show

# Добавить ключ в .env, затем запустить
docker compose -f compose.prod.yaml up -d --build

# Миграции
docker compose -f compose.prod.yaml exec app php artisan migrate --force

# Кеширование
docker compose -f compose.prod.yaml exec app php artisan config:cache
docker compose -f compose.prod.yaml exec app php artisan route:cache
docker compose -f compose.prod.yaml exec app php artisan view:cache
docker compose -f compose.prod.yaml exec app php artisan filament:cache-components

# Индексация поиска
docker compose -f compose.prod.yaml exec app php artisan scout:sync-index-settings
```

### 3. Проверка
```bash
# Статус контейнеров
docker compose -f compose.prod.yaml ps

# Доступность
curl http://localhost:8080

# Логи
docker compose -f compose.prod.yaml logs -f
```

## Доступ

Приложение доступно:
- **HTTP**: `http://your-server:8080`
- **HTTPS**: `https://your-server:8443`

## Проксирование через основной Nginx

Если на сервере уже работает Nginx на портах 80/443, добавьте проксирование:

```nginx
# /etc/nginx/sites-available/alarmstyle
server {
    listen 80;
    server_name yourdomain.com;
    
    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Активировать:
```bash
sudo ln -s /etc/nginx/sites-available/alarmstyle /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## Полезные команды

### Управление
```bash
# Остановить
docker compose -f compose.prod.yaml stop

# Запустить
docker compose -f compose.prod.yaml start

# Перезапустить
docker compose -f compose.prod.yaml restart

# Пересобрать
docker compose -f compose.prod.yaml up -d --build --force-recreate
```

### Логи
```bash
# Все логи
docker compose -f compose.prod.yaml logs -f

# Только app
docker compose -f compose.prod.yaml logs -f app

# Только nginx
docker compose -f compose.prod.yaml logs -f nginx
```

### Обновление после git pull
```bash
docker compose -f compose.prod.yaml up -d --build
docker compose -f compose.prod.yaml exec app php artisan migrate --force
docker compose -f compose.prod.yaml exec app php artisan config:cache
docker compose -f compose.prod.yaml exec app php artisan route:cache
docker compose -f compose.prod.yaml exec app php artisan view:cache
docker compose -f compose.prod.yaml exec app php artisan horizon:terminate
```

### Очистка кеша
```bash
docker compose -f compose.prod.yaml exec app php artisan cache:clear
docker compose -f compose.prod.yaml exec app php artisan config:clear
docker compose -f compose.prod.yaml exec app php artisan route:clear
docker compose -f compose.prod.yaml exec app php artisan view:clear
```

## Troubleshooting

### Порт занят
```bash
# Проверить что занимает порт
sudo lsof -i :8080

# Изменить порт в .env
APP_PORT=9080
APP_SSL_PORT=9443

# Перезапустить
docker compose -f compose.prod.yaml up -d
```

### Проблемы с правами
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Horizon не работает
```bash
docker compose -f compose.prod.yaml exec app supervisorctl status
docker compose -f compose.prod.yaml exec app supervisorctl restart horizon
```

### Firewall
```bash
# Открыть порты
sudo ufw allow 8080/tcp
sudo ufw allow 8443/tcp
sudo ufw status
```
