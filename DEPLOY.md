# Production Deploy

## Содержание

1. [Настройка сервера](#настройка-сервера)
2. [Быстрый старт](#быстрый-старт)
3. [GitHub Actions деплой](#github-actions-деплой)
4. [Настройка Nginx (нативный)](#настройка-nginx-нативный)
5. [Полезные команды](#полезные-команды)
6. [Troubleshooting](#troubleshooting)

---

## Настройка сервера

### Требования
- Ubuntu 20.04+ (или другой Linux)
- Docker и Docker Compose
- Git
- Nginx (нативный, уже установлен)
- Свободные порты: 8080 (HTTP), 8443 (HTTPS) для Docker-контейнеров

### Первичная настройка

```bash
# Установить Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Добавить пользователя в группу docker
sudo usermod -aG docker $USER
newgrp docker

# Установить Docker Compose (если еще не установлен)
sudo apt install docker-compose-plugin

# Клонировать репозиторий
cd /var/www
sudo git clone https://github.com/yourusername/alarmstyle.git
sudo chown -R $USER:$USER alarmstyle
cd alarmstyle
```

## Быстрый старт

### 1. Настройка .env
```bash
cp .env.production.example .env
nano .env

# ВАЖНО: Установите UID и GID вашего пользователя для правильной работы с правами доступа
echo "UID=$(id -u)" >> .env
echo "GID=$(id -g)" >> .env
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

---

## GitHub Actions деплой

### Настройка SSH на сервере

```bash
# Создать нового пользователя для деплоя (опционально, но рекомендуется)
sudo adduser deploy
sudo usermod -aG docker deploy
sudo usermod -aG www-data deploy

# Переключиться на пользователя deploy
sudo su - deploy

# Создать SSH ключ для GitHub Actions
ssh-keygen -t ed25519 -C "github-actions@alarmstyle" -f ~/.ssh/github_deploy
# Не устанавливайте пароль (просто нажмите Enter)

# Добавить публичный ключ в authorized_keys
cat ~/.ssh/github_deploy.pub >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys

# Показать приватный ключ для добавления в GitHub Secrets
cat ~/.ssh/github_deploy
# Скопируйте весь вывод, включая BEGIN и END строки
```

### Настройка GitHub Secrets

Перейдите в репозиторий на GitHub:  
**Settings** → **Secrets and variables** → **Actions** → **New repository secret**

Добавьте следующие секреты:

| Имя секрета | Значение | Описание |
|-------------|----------|----------|
| `PROD_SSH_KEY` | Приватный ключ из `~/.ssh/github_deploy` | SSH ключ для подключения к серверу |
| `PROD_HOST` | IP адрес или домен сервера | Например: `192.168.1.100` или `alarmstyle.ru` |
| `PROD_USERNAME` | `deploy` | Имя пользователя на сервере |
| `PROD_SSH_PORT` | `22` | SSH порт (обычно 22, необязательный) |
| `PROD_APP_PATH` | `/var/www/alarmstyle` | Путь к приложению на сервере |
| `PROD_APP_URL` | `http://alarmstyle.ru` | URL приложения для health check |

### Workflow файл

В проекте уже есть файл `.github/workflows/deploy.yml`, который:
- Автоматически собирает Docker образ и публикует его в GitHub Container Registry
- Подключается к серверу по SSH
- Разворачивает приложение на сервере
- Запускает миграции и оптимизацию
- Проверяет работоспособность приложения


### Дополнительные секреты для продвинутой настройки

Если хотите деплоить .env файл или использовать другие секреты:

| Имя секрета | Описание |
|-------------|----------|
| `APP_KEY` | Laravel APP_KEY |
| `DB_PASSWORD` | Пароль базы данных |
| `REDIS_PASSWORD` | Пароль Redis |
| `MEILISEARCH_KEY` | Ключ Meilisearch |

Пример использования в workflow для создания .env:

```yaml
- name: 📝 Create .env file
  run: |
    ssh ${{ secrets.PROD_USERNAME }}@${{ secrets.PROD_HOST }} << 'EOF'
      cd ${{ secrets.PROD_APP_PATH }}
      cat > .env << 'ENVEOF'
      APP_KEY=${{ secrets.APP_KEY }}
      DB_PASSWORD=${{ secrets.DB_PASSWORD }}
      REDIS_PASSWORD=${{ secrets.REDIS_PASSWORD }}
      # ... другие переменные
    ENVEOF
    EOF
```

### Ручной запуск деплоя

Workflow настроен на автоматический деплой при push в main, но можно запустить вручную:

1. Перейдите на GitHub: **Actions** → **Deploy to Production**
2. Нажмите **Run workflow** → **Run workflow**

### Просмотр логов деплоя

На GitHub: **Actions** → выберите последний запуск → смотрите логи каждого шага

---

## Настройка Nginx (нативный)

Если на сервере уже работает Nginx на стандартных портах 80/443, используйте его как reverse proxy для Docker-контейнеров.

### Создание конфигурации

```bash
sudo nano /etc/nginx/sites-available/alarmstyle
```

**Базовая конфигурация (HTTP):**

```nginx
server {
    listen 80;
    server_name alarmstyle.ru www.alarmstyle.ru;
    
    # Логи
    access_log /var/log/nginx/alarmstyle_access.log;
    error_log /var/log/nginx/alarmstyle_error.log;
    
    # Proxy настройки
    location / {
        proxy_pass http://localhost:8080;
        proxy_http_version 1.1;
        
        # Headers
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header X-Forwarded-Host $host;
        proxy_set_header X-Forwarded-Port $server_port;
        
        # WebSocket support (для Livewire/Horizon)
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        
        # Timeouts
        proxy_connect_timeout 60s;
        proxy_send_timeout 60s;
        proxy_read_timeout 60s;
    }
    
    # Оптимизация для статики (опционально)
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        proxy_pass http://localhost:8080;
        proxy_cache_valid 200 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

**Полная конфигурация с HTTPS (Let's Encrypt):**

```nginx
# HTTP → HTTPS redirect
server {
    listen 80;
    server_name alarmstyle.ru www.alarmstyle.ru;
    
    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }
    
    location / {
        return 301 https://$server_name$request_uri;
    }
}

# HTTPS
server {
    listen 443 ssl http2;
    server_name alarmstyle.ru www.alarmstyle.ru;
    
    # SSL сертификаты
    ssl_certificate /etc/letsencrypt/live/alarmstyle.ru/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/alarmstyle.ru/privkey.pem;
    
    # SSL настройки
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    
    # Логи
    access_log /var/log/nginx/alarmstyle_access.log;
    error_log /var/log/nginx/alarmstyle_error.log;
    
    # Proxy настройки
    location / {
        proxy_pass http://localhost:8080;
        proxy_http_version 1.1;
        
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header X-Forwarded-Host $host;
        proxy_set_header X-Forwarded-Port $server_port;
        
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        
        proxy_connect_timeout 60s;
        proxy_send_timeout 60s;
        proxy_read_timeout 60s;
        
        # Размер загружаемых файлов
        client_max_body_size 100M;
    }
    
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        proxy_pass http://localhost:8080;
        proxy_cache_valid 200 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

### Активация конфигурации

```bash
# Создать симлинк
sudo ln -s /etc/nginx/sites-available/alarmstyle /etc/nginx/sites-enabled/

# Проверить конфигурацию
sudo nginx -t

# Перезагрузить Nginx
sudo systemctl reload nginx
```

### Установка SSL сертификата (Let's Encrypt)

```bash
# Установить certbot
sudo apt install certbot python3-certbot-nginx

# Получить сертификат
sudo certbot --nginx -d alarmstyle.ru -d www.alarmstyle.ru

# Проверить автообновление
sudo certbot renew --dry-run
```

### Проверка работы

```bash
# Проверить статус Nginx
sudo systemctl status nginx

# Проверить слушающие порты
sudo netstat -tlnp | grep nginx

# Проверить логи
sudo tail -f /var/log/nginx/alarmstyle_access.log
sudo tail -f /var/log/nginx/alarmstyle_error.log

# Тест доступности
curl -I http://alarmstyle.ru
curl -I https://alarmstyle.ru
```

---

---

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

#### Permission denied в storage/logs или bootstrap/cache

Начиная с версии с PHP 8.5, приложение использует UID/GID пользователя хоста для запуска контейнеров.

**Решение:**

1. Убедитесь, что в `.env` установлены правильные UID и GID:
```bash
echo "UID=$(id -u)" >> .env
echo "GID=$(id -g)" >> .env
```

2. Пересоздайте контейнеры:
```bash
docker compose -f compose.prod.yaml down
docker compose -f compose.prod.yaml up -d --build
```

3. Если проблема сохраняется, исправьте права вручную:
```bash
chmod -R 775 storage bootstrap/cache
```

**Старый способ (не рекомендуется):**
```bash
# Работает, но нарушает работу с файлами от имени хост-пользователя
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### PHP 8.5 Deprecation Warnings

Если вы видите предупреждения типа:
```
PHP Deprecated: Constant PDO::MYSQL_ATTR_SSL_CA is deprecated since 8.5
```

Это нормально для PHP 8.5. Приложение уже обновлено для использования новых констант `\Pdo\Mysql::ATTR_SSL_CA`.

Если предупреждения мешают, можно отключить вывод deprecated warnings в `docker/php/php.ini`:
```ini
error_reporting = E_ALL & ~E_DEPRECATED
```

Затем пересобрать образ:
```bash
docker compose -f compose.prod.yaml up -d --build
```

### Horizon не работает
```bash
# Проверить логи Horizon
docker compose -f compose.prod.yaml logs horizon

# Перезапустить Horizon
docker compose -f compose.prod.yaml restart horizon
```

### Scheduler не работает
```bash
# Проверить логи планировщика
docker compose -f compose.prod.yaml logs scheduler

# Перезапустить планировщик
docker compose -f compose.prod.yaml restart scheduler
```

### Firewall
```bash
# Открыть порты
sudo ufw allow 8080/tcp
sudo ufw allow 8443/tcp
sudo ufw status
```
