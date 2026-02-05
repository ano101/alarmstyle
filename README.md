# AlarmStyle

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?style=flat&logo=php)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat&logo=docker)](https://docker.com)

Интернет-магазин товаров безопасности на Laravel 12.

## Стек

- Laravel 12 (PHP 8.5) + Inertia.js + Vue 3 + Tailwind CSS 4
- Filament 4, MySQL 8.4, Redis, Meilisearch, Horizon
- Docker + Docker Compose

## Локальная разработка

```bash
# Установка
composer install && npm install
cp .env.example .env
php artisan key:generate

# Запуск (Laravel Sail)
vendor/bin/sail up -d
vendor/bin/sail artisan migrate --seed
vendor/bin/sail npm run dev
```

Приложение: http://localhost

## Production

Инструкция: **[DEPLOY.md](DEPLOY.md)**

```bash
# Настройка
cp .env.production.example .env
nano .env  # настроить APP_KEY, пароли, домен

# Запуск
docker compose -f compose.prod.yaml up -d --build
docker compose -f compose.prod.yaml exec app php artisan migrate --force
docker compose -f compose.prod.yaml exec app php artisan config:cache
```

Доступ: http://your-server:8080 (порты 8080/8443 - не конфликтуют с нативным Nginx)

## Полезные команды

```bash
# Разработка
vendor/bin/sail up -d
vendor/bin/sail artisan test
vendor/bin/sail bin pint

# Production
docker compose -f compose.prod.yaml logs -f
docker compose -f compose.prod.yaml restart
docker compose -f compose.prod.yaml exec app php artisan cache:clear
```

## Дополнительно

- **Horizon**: `/horizon` - управление очередями
- **Health Check**: `/health` - проверка работоспособности
- **Документация**: [docs/](docs/) - техническая документация

