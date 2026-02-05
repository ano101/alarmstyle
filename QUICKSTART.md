# Quick Start Guide - Production Deployment

## 🚀 Быстрый старт на production сервере

### Шаг 1: Подготовка сервера

```bash
# Установка Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER

# Перелогиньтесь для применения изменений группы
exit
# Войдите снова по SSH

# Проверка Docker
docker --version
docker compose version
```

### Шаг 2: Клонирование репозитория

```bash
# Создайте директорию для приложения
sudo mkdir -p /var/www/alarmstyle
sudo chown $USER:$USER /var/www/alarmstyle
cd /var/www/alarmstyle

# Клонируйте репозиторий
git clone https://github.com/your-username/alarmstyle.git .
```

### Шаг 3: Настройка окружения

```bash
# Скопируйте example файл
cp .env.production.example .env

# Отредактируйте .env
nano .env
```

Обязательно настройте:
```env
APP_KEY=                          # Сгенерируйте через: docker run --rm php:8.5-cli php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
APP_URL=https://yourdomain.com
DB_PASSWORD=strongpassword123
REDIS_PASSWORD=strongredispass456
MEILISEARCH_KEY=strongmeilisearchkey789
```

### Шаг 4: Создание директорий

```bash
mkdir -p storage/logs/nginx backups
chmod -R 775 storage bootstrap/cache
```

### Шаг 5: Запуск приложения

```bash
# Сборка и запуск контейнеров
docker compose -f compose.prod.yaml up -d --build

# Дождитесь запуска всех сервисов (около 30-60 секунд)
docker compose -f compose.prod.yaml ps
```

### Шаг 6: Инициализация базы данных

```bash
# Выполните миграции
docker compose -f compose.prod.yaml exec app php artisan migrate --force

# Заполните базу начальными данными (если есть сиды)
docker compose -f compose.prod.yaml exec app php artisan db:seed --force
```

### Шаг 7: Оптимизация

```bash
# Кеширование конфигурации
docker compose -f compose.prod.yaml exec app php artisan config:cache
docker compose -f compose.prod.yaml exec app php artisan route:cache
docker compose -f compose.prod.yaml exec app php artisan view:cache
docker compose -f compose.prod.yaml exec app php artisan filament:cache-components

# Синхронизация поискового индекса
docker compose -f compose.prod.yaml exec app php artisan scout:sync-index-settings
docker compose -f compose.prod.yaml exec app php artisan scout:import "App\Models\Product"
```

### Шаг 8: Проверка работоспособности

```bash
# Проверка health endpoint
curl http://localhost/health

# Или используйте скрипт мониторинга
./monitor.sh health
```

## 📋 Использование Makefile

Для упрощения команд используйте Makefile:

```bash
# Показать все доступные команды
make help

# Примеры:
make prod-up          # Запустить production
make prod-logs        # Показать логи
make monitor          # Открыть dashboard
make backup           # Создать бэкап БД
make optimize         # Оптимизировать приложение
```

## 🔄 Настройка GitHub Actions для автодеплоя

### 1. Создайте SSH ключ для GitHub Actions:

```bash
ssh-keygen -t ed25519 -C "github-actions@yourdomain.com" -f ~/.ssh/github_deploy
cat ~/.ssh/github_deploy.pub >> ~/.ssh/authorized_keys
cat ~/.ssh/github_deploy  # Скопируйте приватный ключ
```

### 2. Добавьте секреты в GitHub:

Перейдите в: `Settings → Secrets and variables → Actions`

Добавьте секреты:
- `PROD_HOST`: IP сервера (например: `123.45.67.89`)
- `PROD_USERNAME`: SSH пользователь (например: `deploy`)
- `PROD_SSH_KEY`: Приватный SSH ключ (содержимое github_deploy)
- `PROD_APP_PATH`: `/var/www/alarmstyle`
- `PROD_APP_URL`: `http://your-domain.com` или `http://your-ip`

### 3. Тестирование деплоя:

```bash
# Сделайте любое изменение и закоммитьте
git add .
git commit -m "Test deployment"
git push origin main

# Следите за процессом в разделе Actions на GitHub
```

## 🔒 Настройка SSL (опционально)

### Используя Certbot:

```bash
# Установите Certbot
sudo apt-get update
sudo apt-get install certbot

# Получите сертификат
sudo certbot certonly --standalone -d yourdomain.com -d www.yourdomain.com

# Настройте nginx для использования SSL
# Отредактируйте docker/nginx/conf.d/default.conf
```

## 📊 Мониторинг

```bash
# Dashboard
./monitor.sh

# Или конкретные команды:
./monitor.sh status      # Статус контейнеров
./monitor.sh logs app    # Логи приложения
./monitor.sh resources   # Использование ресурсов
./monitor.sh horizon     # Статус Horizon
./monitor.sh health      # Health check всех сервисов
```

## 🛠️ Troubleshooting

### Проблемы с правами доступа:
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Контейнеры не запускаются:
```bash
# Проверьте логи
docker compose -f compose.prod.yaml logs

# Пересоздайте контейнеры
docker compose -f compose.prod.yaml down -v
docker compose -f compose.prod.yaml up -d --build
```

### Horizon не работает:
```bash
# Перезапустите Horizon
docker compose -f compose.prod.yaml exec app php artisan horizon:terminate

# Проверьте статус
docker compose -f compose.prod.yaml exec app php artisan horizon:status
```

### База данных не доступна:
```bash
# Проверьте статус MySQL
docker compose -f compose.prod.yaml exec mysql mysqladmin ping -h localhost

# Проверьте переменные окружения
docker compose -f compose.prod.yaml exec app env | grep DB_
```

## 📞 Поддержка

Подробная документация: [DEPLOYMENT.md](DEPLOYMENT.md)

Секреты GitHub Actions: [.github/SECRETS.md](.github/SECRETS.md)

## ✅ Checklist готовности к production

- [ ] Docker и Docker Compose установлены
- [ ] Репозиторий склонирован
- [ ] .env файл настроен с безопасными паролями
- [ ] Контейнеры запущены и работают
- [ ] Миграции выполнены
- [ ] Кеши сгенерированы
- [ ] Health check возвращает 200 OK
- [ ] SSL сертификат настроен (для HTTPS)
- [ ] GitHub Actions секреты добавлены
- [ ] Бэкапы базы данных настроены
- [ ] Мониторинг работает
- [ ] Horizon обрабатывает задачи

Поздравляем! Ваше приложение готово к работе! 🎉
