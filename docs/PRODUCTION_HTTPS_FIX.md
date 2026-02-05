# Исправление Mixed Content на Production

## Что было сделано

### 1. Laravel изменения (уже в репозитории)

✅ **bootstrap/app.php** - добавлен `trustProxies(at: '*')` для доверия всем прокси серверам
✅ **docker/nginx/conf.d/default.conf** - добавлена обработка и передача заголовков `X-Forwarded-*`
✅ **deploy.sh** - обновлен скрипт деплоя

### 2. Что нужно сделать на сервере

#### Шаг 1: Обновить native nginx конфигурацию

В файле конфигурации nginx (вероятно `/etc/nginx/fastpanel2-sites/fastuser/test.alarmstyle.ru` или основной конфиг) в блоке `location /` добавить заголовки:

```nginx
location / {
    proxy_pass http://test.alarmstyle.ru2;
    
    # КРИТИЧЕСКИ ВАЖНО: эти заголовки нужны для HTTPS
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;  # <-- Самый важный!
    proxy_set_header X-Forwarded-Host $host;
    proxy_set_header X-Forwarded-Port $server_port;
    
    # Остальные настройки прокси
    proxy_redirect off;
    proxy_buffering off;
    proxy_http_version 1.1;
    proxy_set_header Connection "";
}
```

И также в блоке `location @fallback`:

```nginx
location @fallback {
    proxy_pass http://test.alarmstyle.ru2;
    
    # Дублируем те же заголовки
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_set_header X-Forwarded-Host $host;
    proxy_set_header X-Forwarded-Port $server_port;
}
```

#### Шаг 2: Проверить и перезагрузить nginx

```bash
sudo nginx -t
sudo systemctl reload nginx
```

#### Шаг 3: Обновить APP_URL в .env

На сервере в файле `/var/www/fastuser/data/www/test.alarmstyle.ru/.env` установить:

```env
APP_URL=https://test.alarmstyle.ru
```

#### Шаг 4: Задеплоить изменения

```bash
cd /var/www/fastuser/data/www/test.alarmstyle.ru
./deploy.sh
```

Скрипт деплоя автоматически:
- Обновит код из репозитория
- Перезапустит Docker контейнеры с новыми настройками nginx
- Очистит и пересоздаст кеш
- Проверит APP_URL и предупредит, если он не начинается с https://

## Проверка

После деплоя откройте https://test.alarmstyle.ru/admin/login в браузере:
- Все ресурсы должны загружаться по HTTPS
- Не должно быть ошибок Mixed Content в консоли
- CSS и JS Filament должны корректно загружаться

## Диагностика (если не работает)

### Проверить заголовки внутри контейнера:

```bash
docker compose -f compose.prod.yaml exec app php artisan tinker
>>> request()->header('X-Forwarded-Proto')
```

Должно вернуть: `"https"`

### Проверить APP_URL:

```bash
docker compose -f compose.prod.yaml exec app php artisan tinker
>>> config('app.url')
```

Должно вернуть: `"https://test.alarmstyle.ru"`

## Дополнительная информация

Подробная документация с полной конфигурацией nginx находится в файле:
`docs/NGINX_PROXY_SETUP.md`
