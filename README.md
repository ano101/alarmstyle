# AlarmStyle

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?style=flat&logo=php)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat&logo=docker)](https://docker.com)
[![GitHub Actions](https://img.shields.io/badge/CI%2FCD-GitHub%20Actions-2088FF?style=flat&logo=github-actions)](https://github.com/features/actions)

Современная платформа электронной коммерции для продажи товаров безопасности, построенная на Laravel 12.

## 🚀 Технологический стек

- **Backend**: Laravel 12 (PHP 8.5)
- **Frontend**: Inertia.js + Vue 3 + Tailwind CSS 4
- **Admin Panel**: Filament 4
- **Database**: MySQL 8.4
- **Cache & Queues**: Redis
- **Search**: Meilisearch (Laravel Scout)
- **Queue Management**: Laravel Horizon
- **Container**: Docker + Docker Compose

## 📋 Возможности

- 🛍️ Полнофункциональный интернет-магазин
- 📦 Управление каталогом продуктов с атрибутами
- 🔍 Быстрый полнотекстовый поиск (Meilisearch)
- 📊 Административная панель (Filament)
- 🎨 Современный responsive UI (Vue 3 + Tailwind)
- ⚡ Server-Side Rendering (SSR) с Inertia
- 📱 Адаптивный дизайн
- 🔄 Фоновая обработка задач (Horizon)
- 📈 SEO оптимизация

## 🏃 Быстрый старт

### Локальная разработка (Laravel Sail)

```bash
# Клонирование репозитория
git clone https://github.com/your-username/alarmstyle.git
cd alarmstyle

# Установка зависимостей (первый раз без Sail)
composer install
npm install

# Настройка окружения
cp .env.example .env
php artisan key:generate

# Запуск через Sail
vendor/bin/sail up -d

# Миграции и сиды
vendor/bin/sail artisan migrate --seed

# Сборка фронтенда
vendor/bin/sail npm run dev
```

Приложение будет доступно по адресу: http://localhost

### Production деплой

Подробная инструкция: [QUICKSTART.md](QUICKSTART.md)

```bash
# Быстрый старт на production сервере
docker compose -f compose.prod.yaml up -d --build
docker compose -f compose.prod.yaml exec app php artisan migrate --force

# Или используйте скрипт деплоя
./deploy.sh
```

## 📚 Документация

- [Быстрый старт (Production)](QUICKSTART.md) - Пошаговая инструкция для запуска на production
- [Полная документация деплоя](DEPLOYMENT.md) - Подробное руководство по настройке и обслуживанию
- [GitHub Actions секреты](.github/SECRETS.md) - Настройка автоматического деплоя

### Дополнительная документация

- [Поиск](docs/SEARCH.md) - Настройка и работа с Meilisearch
- [Атрибуты](docs/ATTRIBUTE_MAPPING.md) - Система атрибутов продуктов
- [Популярные поиски](docs/POPULAR_SEARCHES.md) - Управление популярными запросами
- [Редиректы](docs/REDIRECTS.md) - Управление редиректами

## 🛠️ Полезные команды

### Локальная разработка

```bash
# Запуск окружения
vendor/bin/sail up -d

# Остановка
vendor/bin/sail stop

# Запуск тестов
vendor/bin/sail artisan test

# Форматирование кода
vendor/bin/sail bin pint

# Открыть в браузере
vendor/bin/sail open
```

### Production (используйте Makefile)

```bash
make help           # Показать все команды
make prod-up        # Запустить production
make prod-logs      # Показать логи
make monitor        # Открыть dashboard мониторинга
make backup         # Создать бэкап базы данных
make deploy         # Деплой приложения
make optimize       # Оптимизировать приложение
```

## 🔧 Конфигурация

### Переменные окружения

Основные переменные для настройки:

```env
APP_NAME=AlarmStyle
APP_ENV=production
APP_URL=https://yourdomain.com

DB_DATABASE=alarmstyle
DB_USERNAME=alarmstyle
DB_PASSWORD=secure_password

REDIS_PASSWORD=secure_redis_password
MEILISEARCH_KEY=secure_meilisearch_key
```

Полный пример: [.env.production.example](.env.production.example)

## 🧪 Тестирование

```bash
# Запуск всех тестов
vendor/bin/sail artisan test

# Запуск конкретного теста
vendor/bin/sail artisan test --filter=TestName

# С coverage
vendor/bin/sail artisan test --coverage
```

## 📊 Мониторинг

### Horizon Dashboard
Управление очередями: `/horizon`

### Health Check
Проверка работоспособности: `/health`

### Мониторинг через CLI
```bash
./monitor.sh          # Полный dashboard
./monitor.sh health   # Health check всех сервисов
./monitor.sh horizon  # Статус Horizon
./monitor.sh logs     # Просмотр логов
```

## 🚢 CI/CD

Проект использует GitHub Actions для автоматического тестирования и деплоя:

- **Tests & Code Quality** - Запускается при каждом push/PR
- **Deploy to Production** - Автоматический деплой при push в `main`

Настройка: [.github/workflows/](.github/workflows/)

## 🤝 Разработка

### Структура проекта

```
app/
├── Filament/        # Административная панель
├── Http/            # Controllers, Middleware, Requests
├── Models/          # Eloquent модели
├── Services/        # Бизнес-логика
└── Support/         # Вспомогательные классы

resources/
├── js/              # Vue компоненты и логика
│   └── Pages/       # Inertia страницы
├── views/           # Blade шаблоны
└── css/             # Стили

docker/              # Production Docker конфигурация
├── nginx/           # Nginx конфигурация
├── php/             # PHP-FPM настройки
├── supervisor/      # Supervisor конфигурация
└── mysql/           # MySQL конфигурация
```

### Соглашения о коде

- Следуйте [PSR-12](https://www.php-fig.org/psr/psr-12/)
- Используйте Laravel Pint для форматирования
- Пишите тесты для нового функционала
- Используйте meaningful commit messages

## 📝 License

Проект использует открытую лицензию. Детали в файле LICENSE.

## 🆘 Поддержка

При возникновении проблем:

1. Проверьте [DEPLOYMENT.md](DEPLOYMENT.md) для troubleshooting
2. Проверьте логи: `./monitor.sh logs`
3. Создайте issue в репозитории

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Meilisearch Синхронизация

Для синхронизации настроек индексов Meilisearch и импорта данных используйте команду:

```bash
vendor/bin/sail artisan meilisearch:sync
```

Доступные опции:
- `--no-import` - только синхронизация настроек без импорта данных
- `--flush` - полная очистка индекса перед импортом

Подробную документацию смотрите в [docs/MEILISEARCH_SYNC.md](docs/MEILISEARCH_SYNC.md)

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
