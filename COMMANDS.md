# Production Deployment - Quick Reference

## 🚀 Шпаргалка команд

### Первичная настройка сервера

```bash
# 1. Настройка сервера (как root)
sudo bash server-setup.sh

# 2. Переключение на пользователя deploy
su - deploy

# 3. Клонирование репозитория
cd /var/www/alarmstyle
git clone https://github.com/your-username/alarmstyle.git .

# 4. Настройка окружения
cp .env.production.example .env
nano .env  # Отредактируйте переменные

# 5. Создание необходимых директорий
mkdir -p storage/logs/nginx backups
chmod -R 775 storage bootstrap/cache

# 6. Проверка готовности
./preflight-check.sh

# 7. Запуск приложения
make prod-up

# 8. Выполнение миграций
make deploy
```

### Ежедневные команды

```bash
# Проверка статуса
make monitor
./monitor.sh status

# Просмотр логов
make prod-logs
./monitor.sh logs app

# Деплой обновлений
make deploy
./deploy.sh

# Создание бэкапа
make backup

# Оптимизация
make optimize

# Health check
make health
./monitor.sh health
```

### Docker команды

```bash
# Запуск всех сервисов
make prod-up
docker compose -f compose.prod.yaml up -d

# Остановка
make prod-down
docker compose -f compose.prod.yaml down

# Перезапуск
make prod-restart
docker compose -f compose.prod.yaml restart

# Перезапуск конкретного сервиса
docker compose -f compose.prod.yaml restart app
docker compose -f compose.prod.yaml restart nginx

# Пересборка образов
make prod-build
docker compose -f compose.prod.yaml build --no-cache

# Открыть shell в контейнере
make prod-shell
docker compose -f compose.prod.yaml exec app sh

# Просмотр логов
docker compose -f compose.prod.yaml logs -f
docker compose -f compose.prod.yaml logs -f app
docker compose -f compose.prod.yaml logs --tail=100 app

# Статус контейнеров
docker compose -f compose.prod.yaml ps

# Использование ресурсов
docker stats
```

### Laravel Artisan команды

```bash
# Миграции
docker compose -f compose.prod.yaml exec app php artisan migrate --force
docker compose -f compose.prod.yaml exec app php artisan migrate:rollback
docker compose -f compose.prod.yaml exec app php artisan migrate:status

# Очистка кешей
docker compose -f compose.prod.yaml exec app php artisan cache:clear
docker compose -f compose.prod.yaml exec app php artisan config:clear
docker compose -f compose.prod.yaml exec app php artisan route:clear
docker compose -f compose.prod.yaml exec app php artisan view:clear

# Кеширование
docker compose -f compose.prod.yaml exec app php artisan config:cache
docker compose -f compose.prod.yaml exec app php artisan route:cache
docker compose -f compose.prod.yaml exec app php artisan view:cache
docker compose -f compose.prod.yaml exec app php artisan filament:cache-components

# Horizon
docker compose -f compose.prod.yaml exec app php artisan horizon:status
docker compose -f compose.prod.yaml exec app php artisan horizon:terminate
docker compose -f compose.prod.yaml exec app php artisan horizon:pause
docker compose -f compose.prod.yaml exec app php artisan horizon:continue

# Queue
docker compose -f compose.prod.yaml exec app php artisan queue:work --once
docker compose -f compose.prod.yaml exec app php artisan queue:restart
docker compose -f compose.prod.yaml exec app php artisan queue:failed
docker compose -f compose.prod.yaml exec app php artisan queue:retry all

# Scout (Meilisearch)
docker compose -f compose.prod.yaml exec app php artisan scout:sync-index-settings
docker compose -f compose.prod.yaml exec app php artisan scout:import "App\Models\Product"
docker compose -f compose.prod.yaml exec app php artisan scout:flush "App\Models\Product"

# Tinker
docker compose -f compose.prod.yaml exec app php artisan tinker

# Maintenance mode
docker compose -f compose.prod.yaml exec app php artisan down --refresh=15
docker compose -f compose.prod.yaml exec app php artisan up
```

### Database команды

```bash
# Подключение к MySQL
docker compose -f compose.prod.yaml exec mysql mysql -u root -p

# Бэкап
docker compose -f compose.prod.yaml exec mysql mysqldump -u root -p${DB_PASSWORD} ${DB_DATABASE} > backup.sql
# Или через скрипт
./monitor.sh backup
make backup

# Восстановление
docker compose -f compose.prod.yaml exec -T mysql mysql -u root -p${DB_PASSWORD} ${DB_DATABASE} < backup.sql

# Проверка подключения
docker compose -f compose.prod.yaml exec mysql mysqladmin ping -h localhost
```

### Redis команды

```bash
# Подключение к Redis
docker compose -f compose.prod.yaml exec redis redis-cli

# В Redis CLI:
AUTH yourpassword
PING
INFO
KEYS *
FLUSHALL  # Осторожно! Очистит весь кеш
```

### Meilisearch команды

```bash
# Health check
curl http://localhost:7700/health

# Статистика
curl http://localhost:7700/stats -H "Authorization: Bearer ${MEILISEARCH_KEY}"

# Список индексов
curl http://localhost:7700/indexes -H "Authorization: Bearer ${MEILISEARCH_KEY}"
```

### Nginx команды

```bash
# Проверка конфигурации
docker compose -f compose.prod.yaml exec nginx nginx -t

# Перезагрузка конфигурации
docker compose -f compose.prod.yaml exec nginx nginx -s reload

# Просмотр access логов
tail -f storage/logs/nginx/access.log

# Просмотр error логов
tail -f storage/logs/nginx/error.log
```

### Мониторинг

```bash
# Полный dashboard
./monitor.sh
./monitor.sh dashboard

# Статус контейнеров
./monitor.sh status

# Логи
./monitor.sh logs app
./monitor.sh logs nginx 100

# Использование ресурсов
./monitor.sh resources

# Health check всех сервисов
./monitor.sh health

# Horizon статус
./monitor.sh horizon

# Очистка кешей
./monitor.sh clear-cache

# Оптимизация
./monitor.sh optimize

# Перезапуск сервисов
./monitor.sh restart
```

### Git & Деплой

```bash
# Обновление кода
git pull origin main

# Полный деплой
./deploy.sh
make deploy

# Просмотр истории
git log --oneline -10
```

### SSL/HTTPS

```bash
# Получение сертификата (первый раз)
sudo certbot certonly --standalone -d yourdomain.com -d www.yourdomain.com

# Проверка обновления
sudo certbot renew --dry-run

# Ручное обновление
sudo certbot renew
```

### Troubleshooting

```bash
# Проверка всех сервисов
./monitor.sh health
make health

# Проверка логов на ошибки
docker compose -f compose.prod.yaml logs | grep -i error
docker compose -f compose.prod.yaml logs app | grep -i exception

# Полный перезапуск
docker compose -f compose.prod.yaml down
docker compose -f compose.prod.yaml up -d --force-recreate

# Пересоздание с новой сборкой
docker compose -f compose.prod.yaml down
docker compose -f compose.prod.yaml build --no-cache
docker compose -f compose.prod.yaml up -d

# Очистка Docker
docker system prune -af
docker volume prune -f

# Проверка дискового пространства
df -h

# Проверка памяти
free -h

# Проверка процессов
docker compose -f compose.prod.yaml exec app ps aux
```

### Полезные алиасы (добавьте в ~/.bashrc)

```bash
alias dcp='docker compose -f compose.prod.yaml'
alias dcp-up='docker compose -f compose.prod.yaml up -d'
alias dcp-down='docker compose -f compose.prod.yaml down'
alias dcp-restart='docker compose -f compose.prod.yaml restart'
alias dcp-logs='docker compose -f compose.prod.yaml logs -f'
alias dcp-ps='docker compose -f compose.prod.yaml ps'
alias art='docker compose -f compose.prod.yaml exec app php artisan'
alias deploy='cd /var/www/alarmstyle && ./deploy.sh'
alias monitor='cd /var/www/alarmstyle && ./monitor.sh'
```

После добавления:
```bash
source ~/.bashrc

# Теперь можно использовать:
dcp-up
dcp-logs app
art migrate
art horizon:status
deploy
monitor
```

## 📱 Быстрый доступ к важным URL

- **Приложение**: `https://yourdomain.com`
- **Админка**: `https://yourdomain.com/admin`
- **Horizon**: `https://yourdomain.com/horizon`
- **Health Check**: `https://yourdomain.com/health`
- **Meilisearch**: `http://localhost:7700` (внутренний)

## 🔢 Порты по умолчанию

- **80**: HTTP (Nginx)
- **443**: HTTPS (Nginx)
- **3306**: MySQL (внутренний)
- **6379**: Redis (внутренний)
- **7700**: Meilisearch (внутренний)
- **9000**: PHP-FPM (внутренний)

## 📞 Экстренные процедуры

### Приложение не отвечает
```bash
# 1. Проверить статус
docker compose -f compose.prod.yaml ps

# 2. Проверить логи
docker compose -f compose.prod.yaml logs app --tail=50

# 3. Перезапустить app
docker compose -f compose.prod.yaml restart app

# 4. Если не помогло - полный перезапуск
docker compose -f compose.prod.yaml restart
```

### База данных недоступна
```bash
# 1. Проверить статус MySQL
docker compose -f compose.prod.yaml ps mysql

# 2. Проверить логи
docker compose -f compose.prod.yaml logs mysql

# 3. Перезапустить MySQL
docker compose -f compose.prod.yaml restart mysql

# 4. Проверить подключение
docker compose -f compose.prod.yaml exec mysql mysqladmin ping
```

### Очереди не обрабатываются
```bash
# 1. Проверить Horizon
docker compose -f compose.prod.yaml exec app php artisan horizon:status

# 2. Перезапустить Horizon
docker compose -f compose.prod.yaml exec app php artisan horizon:terminate

# 3. Проверить Redis
docker compose -f compose.prod.yaml exec redis redis-cli ping

# 4. Проверить логи Horizon
docker compose -f compose.prod.yaml exec app tail -f storage/logs/horizon.log
```

### Полное восстановление
```bash
# 1. Остановить всё
docker compose -f compose.prod.yaml down

# 2. Восстановить из бэкапа
cat backups/latest-backup.sql | docker compose -f compose.prod.yaml exec -T mysql mysql -u root -p${DB_PASSWORD} ${DB_DATABASE}

# 3. Запустить заново
docker compose -f compose.prod.yaml up -d

# 4. Проверить здоровье
./monitor.sh health
```

---

**💡 Совет**: Сохраните этот файл в закладки браузера или распечатайте для быстрого доступа!
