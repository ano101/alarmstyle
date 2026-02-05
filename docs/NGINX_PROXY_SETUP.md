# Настройка Native Nginx для HTTPS проксирования

## Проблема
При работе за HTTPS reverse proxy Laravel генерирует ссылки с `http://` вместо `https://`, что приводит к ошибкам Mixed Content.

## Решение

### 1. Настройки в `/etc/nginx/proxy_params` (или в основной конфигурации)

Убедитесь, что в файле `/etc/nginx/proxy_params` присутствуют следующие заголовки:

```nginx
proxy_set_header Host $host;
proxy_set_header X-Real-IP $remote_addr;
proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
proxy_set_header X-Forwarded-Proto $scheme;
proxy_set_header X-Forwarded-Host $host;
proxy_set_header X-Forwarded-Port $server_port;
```

**Критически важен** заголовок `X-Forwarded-Proto $scheme` - именно он сообщает Laravel, что запрос пришел по HTTPS.

### 2. Обновленная конфигурация для test.alarmstyle.ru

```nginx
upstream test.alarmstyle.ru2 {
    server 127.0.0.1:8080;
}

server {
    server_name test.alarmstyle.ru;
    listen 212.109.198.115:443 ssl http2;
    
    ssl_certificate "/var/www/httpd-cert/test.alarmstyle.ru_2026-02-05-17-11_18.crt";
    ssl_certificate_key "/var/www/httpd-cert/test.alarmstyle.ru_2026-02-05-17-11_18.key";
    add_header Strict-Transport-Security "max-age=31536000" always;

    charset utf-8;

    gzip on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private auth;
    gzip_types text/css image/x-ico application/pdf image/jpeg image/png image/gif application/javascript application/x-javascript application/x-pointplus;
    gzip_comp_level 1;

    set $root_path /var/www/fastuser/data/www/test.alarmstyle.ru;
    root $root_path;
    disable_symlinks if_not_owner from=$root_path;

    location / {
        proxy_pass http://test.alarmstyle.ru2;
        
        # Критически важные заголовки для HTTPS
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header X-Forwarded-Host $host;
        proxy_set_header X-Forwarded-Port $server_port;
        
        # Остальные настройки прокси
        proxy_redirect off;
        proxy_buffering off;
        proxy_http_version 1.1;
        proxy_set_header Connection "";
    }

    location ~* ^.+\.(jpg|jpeg|gif|png|svg|js|css|mp3|ogg|mpeg|avi|zip|gz|bz2|rar|swf|ico|7z|doc|docx|map|ogg|otf|pdf|ttf|tif|txt|wav|webp|woff|woff2|xls|xlsx|xml)$ {
        try_files $uri $uri/ @fallback;
    }

    location @fallback {
        proxy_pass http://test.alarmstyle.ru2;
        
        # Дублируем заголовки для fallback
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header X-Forwarded-Host $host;
        proxy_set_header X-Forwarded-Port $server_port;
    }

    include "/etc/nginx/fastpanel2-sites/fastuser/test.alarmstyle.ru.includes";
    include /etc/nginx/fastpanel2-includes/*.conf;

    error_log /var/www/fastuser/data/logs/test.alarmstyle.ru-frontend.error.log;
    access_log /var/www/fastuser/data/logs/test.alarmstyle.ru-frontend.access.log;
}

server {
    server_name test.alarmstyle.ru;
    listen 212.109.198.115:80;
    return 301 https://$host$request_uri;

    error_log /var/www/fastuser/data/logs/test.alarmstyle.ru-frontend.error.log;
    access_log /var/www/fastuser/data/logs/test.alarmstyle.ru-frontend.access.log;
}

server {
    server_name www.test.alarmstyle.ru;
    
    listen 212.109.198.115:80;
    listen 212.109.198.115:443 ssl http2;
    
    ssl_certificate "/var/www/httpd-cert/test.alarmstyle.ru_2026-02-05-17-11_18.crt";
    ssl_certificate_key "/var/www/httpd-cert/test.alarmstyle.ru_2026-02-05-17-11_18.key";
    add_header Strict-Transport-Security "max-age=31536000" always;
    
    return 301 https://test.alarmstyle.ru$request_uri;
    
    error_log /var/www/fastuser/data/logs/test.alarmstyle.ru-frontend.error.log;
    access_log /var/www/fastuser/data/logs/test.alarmstyle.ru-frontend.access.log;
}
```

### 3. Применение изменений

```bash
# Проверка конфигурации
sudo nginx -t

# Перезагрузка nginx
sudo systemctl reload nginx
```

### 4. Проверка на сервере

После обновления конфигурации убедитесь, что заголовки передаются правильно:

```bash
# Внутри Docker контейнера app
docker compose -f compose.prod.yaml exec app php artisan tinker
>>> request()->header('X-Forwarded-Proto')
# Должно вернуть: "https"
```

## Что сделано в приложении

1. ✅ Настроен `TrustProxies` в `bootstrap/app.php` для доверия всем прокси
2. ✅ Docker nginx настроен на передачу заголовков `X-Forwarded-*` в PHP-FPM
3. ✅ Добавлена обработка `real_ip_header` для корректного логирования IP

## Проверка работоспособности

После деплоя откройте https://test.alarmstyle.ru/admin/login и проверьте в DevTools:
- Все ресурсы должны загружаться по HTTPS
- Не должно быть ошибок Mixed Content
