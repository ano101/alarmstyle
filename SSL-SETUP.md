# SSL/HTTPS Configuration with Let's Encrypt

## Автоматическая установка SSL с Certbot

### Шаг 1: Установка Certbot

```bash
sudo apt-get update
sudo apt-get install -y certbot
```

### Шаг 2: Остановка Nginx (временно)

```bash
docker compose -f compose.prod.yaml stop nginx
```

### Шаг 3: Получение сертификата

```bash
sudo certbot certonly --standalone \
  -d yourdomain.com \
  -d www.yourdomain.com \
  --email your-email@example.com \
  --agree-tos \
  --no-eff-email
```

Сертификаты будут сохранены в:
- `/etc/letsencrypt/live/yourdomain.com/fullchain.pem`
- `/etc/letsencrypt/live/yourdomain.com/privkey.pem`

### Шаг 4: Обновите Docker Compose

Добавьте volume для SSL сертификатов в `compose.prod.yaml`:

```yaml
services:
  nginx:
    volumes:
      - ./:/var/www/html:ro
      - ./docker/nginx/nginx.conf:/etc/nginx/nginx.conf:ro
      - ./docker/nginx/conf.d:/etc/nginx/conf.d:ro
      - ./storage/logs/nginx:/var/log/nginx
      - /etc/letsencrypt:/etc/letsencrypt:ro  # <-- Добавьте эту строку
```

### Шаг 5: Создайте SSL конфигурацию Nginx

Создайте файл `docker/nginx/conf.d/ssl.conf`:

```nginx
# HTTP -> HTTPS redirect
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    
    # ACME challenge for Let's Encrypt renewal
    location ^~ /.well-known/acme-challenge/ {
        root /var/www/html/public;
    }
    
    location / {
        return 301 https://$server_name$request_uri;
    }
}

# HTTPS server
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    
    root /var/www/html/public;
    index index.php;
    
    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    
    # SSL Security (Mozilla Intermediate Configuration)
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers 'ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384';
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    ssl_session_tickets off;
    
    # OCSP Stapling
    ssl_stapling on;
    ssl_stapling_verify on;
    ssl_trusted_certificate /etc/letsencrypt/live/yourdomain.com/chain.pem;
    resolver 8.8.8.8 8.8.4.4 valid=300s;
    resolver_timeout 5s;
    
    # Security Headers
    add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline' 'unsafe-eval'" always;
    
    charset utf-8;
    
    # Logging
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log error;
    
    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
    
    # Laravel public directory
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # Deny access to hidden files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }
    
    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
        fastcgi_connect_timeout 300;
        
        # Add HTTPS param
        fastcgi_param HTTPS on;
    }
    
    # Deny access to .php files in storage and bootstrap/cache
    location ~* ^/(storage|bootstrap\/cache)\/.*\.php$ {
        deny all;
    }
    
    # Health check endpoint
    location /health {
        access_log off;
        return 200 "OK\n";
        add_header Content-Type text/plain;
    }
}
```

### Шаг 6: Удалите старую конфигурацию

```bash
rm docker/nginx/conf.d/default.conf
```

### Шаг 7: Перезапустите Nginx

```bash
docker compose -f compose.prod.yaml up -d nginx
```

### Шаг 8: Проверьте конфигурацию

```bash
# Проверьте конфигурацию nginx
docker compose -f compose.prod.yaml exec nginx nginx -t

# Проверьте SSL
curl -I https://yourdomain.com
```

## Автоматическое обновление сертификата

### Создайте cron job для автоматического обновления:

```bash
sudo crontab -e
```

Добавьте:

```cron
# Renew Let's Encrypt certificates twice daily
0 0,12 * * * certbot renew --quiet --post-hook "cd /var/www/alarmstyle && docker compose -f compose.prod.yaml restart nginx"
```

Или создайте systemd timer:

```bash
# /etc/systemd/system/certbot-renewal.service
[Unit]
Description=Certbot Renewal

[Service]
Type=oneshot
ExecStart=/usr/bin/certbot renew --quiet --post-hook "cd /var/www/alarmstyle && docker compose -f compose.prod.yaml restart nginx"
```

```bash
# /etc/systemd/system/certbot-renewal.timer
[Unit]
Description=Certbot Renewal Timer

[Timer]
OnCalendar=daily
RandomizedDelaySec=1h
Persistent=true

[Install]
WantedBy=timers.target
```

Включите timer:

```bash
sudo systemctl enable certbot-renewal.timer
sudo systemctl start certbot-renewal.timer
```

## Тестирование SSL

### Online tools:
- https://www.ssllabs.com/ssltest/
- https://securityheaders.com/

### Локально:
```bash
# Проверка сертификата
openssl s_client -connect yourdomain.com:443 -servername yourdomain.com

# Проверка HTTP -> HTTPS redirect
curl -I http://yourdomain.com
```

## Troubleshooting

### Сертификат не работает:

```bash
# Проверьте права доступа
ls -la /etc/letsencrypt/live/yourdomain.com/

# Проверьте логи nginx
docker compose -f compose.prod.yaml logs nginx
```

### Обновление не работает:

```bash
# Тест обновления
sudo certbot renew --dry-run

# Проверьте порт 80 доступен
sudo netstat -tlnp | grep :80
```

### Mixed content warnings:

Обновите `APP_URL` в `.env`:
```env
APP_URL=https://yourdomain.com
```

И пересоздайте кеш:
```bash
docker compose -f compose.prod.yaml exec app php artisan config:cache
```

## Alternative: Cloudflare SSL

Если используете Cloudflare:

1. Включите "Full (strict)" SSL mode в Cloudflare
2. Создайте Origin Certificate в Cloudflare
3. Используйте этот сертификат вместо Let's Encrypt
4. Cloudflare автоматически обрабатывает HTTPS

## Быстрая настройка (скрипт)

Создайте файл `setup-ssl.sh`:

```bash
#!/bin/bash
DOMAIN=$1
EMAIL=$2

if [ -z "$DOMAIN" ] || [ -z "$EMAIL" ]; then
    echo "Usage: ./setup-ssl.sh yourdomain.com your-email@example.com"
    exit 1
fi

# Stop nginx
docker compose -f compose.prod.yaml stop nginx

# Get certificate
sudo certbot certonly --standalone \
  -d $DOMAIN \
  -d www.$DOMAIN \
  --email $EMAIL \
  --agree-tos \
  --no-eff-email

# Copy SSL config template
cp docker/nginx/conf.d/ssl.conf.example docker/nginx/conf.d/ssl.conf
sed -i "s/yourdomain.com/$DOMAIN/g" docker/nginx/conf.d/ssl.conf

# Remove old config
rm -f docker/nginx/conf.d/default.conf

# Start nginx
docker compose -f compose.prod.yaml start nginx

echo "SSL configured for $DOMAIN"
```

Используйте:
```bash
chmod +x setup-ssl.sh
./setup-ssl.sh yourdomain.com your-email@example.com
```
