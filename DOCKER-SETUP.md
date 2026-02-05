# Production Docker Setup - Overview

## 📦 Созданные файлы

### Docker & Конфигурация

#### `Dockerfile`
Production Docker образ с PHP 8.5-FPM, Supervisor, Node.js
- Оптимизирован для production
- Multi-stage build для меньшего размера
- Включает все необходимые PHP расширения
- Автоматическая сборка фронтенда

#### `compose.prod.yaml`
Docker Compose для production окружения:
- **nginx** - веб-сервер (порты 80, 443)
- **app** - PHP-FPM + Supervisor (Horizon, Scheduler)
- **mysql** - база данных MySQL 8.4
- **redis** - кеш и очереди
- **meilisearch** - поисковый движок
- Health checks для всех сервисов
- Persistent volumes для данных

#### `.dockerignore`
Оптимизация Docker образа - исключение ненужных файлов

### Конфигурационные файлы

#### `docker/supervisor/supervisord.conf`
Supervisor управляет процессами:
- **php-fpm** - обработка PHP запросов
- **horizon** - Laravel Horizon для очередей
- **schedule-work** - Laravel Scheduler (cron)

#### `docker/nginx/nginx.conf`
Основная конфигурация Nginx:
- Оптимизация производительности
- Gzip compression
- Worker processes настройки

#### `docker/nginx/conf.d/default.conf`
Виртуальный хост для приложения:
- Static files caching
- PHP-FPM integration
- Health check endpoint
- Security headers

#### `docker/php/php.ini`
PHP настройки для production:
- OPcache включен
- Memory limits
- Upload limits
- Security settings

#### `docker/php/www.conf`
PHP-FPM pool конфигурация:
- Dynamic process manager
- Worker настройки
- Logging

#### `docker/mysql/my.cnf`
MySQL оптимизация:
- InnoDB buffer pool
- Character set utf8mb4
- Performance tuning

### CI/CD - GitHub Actions

#### `.github/workflows/deploy.yml`
Автоматический деплой при push в `main`:
1. Сборка Docker образа
2. Публикация в GitHub Container Registry
3. Деплой на production сервер через SSH
4. Выполнение миграций
5. Кеширование конфигурации
6. Health check

#### `.github/workflows/tests.yml`
Автоматическое тестирование:
- Запуск PHPUnit тестов
- Laravel Pint code quality
- Composer security audit
- Запускается при каждом push/PR

#### `.github/SECRETS.md`
Инструкция по настройке GitHub Secrets для CI/CD

### Скрипты управления

#### `deploy.sh`
Скрипт для ручного деплоя на production:
- Pull последних изменений
- Обновление Docker образов
- Миграции
- Оптимизация
- Health check

#### `monitor.sh`
Мониторинг и управление production:
- `./monitor.sh` - полный dashboard
- `./monitor.sh status` - статус контейнеров
- `./monitor.sh logs [service]` - просмотр логов
- `./monitor.sh health` - health check всех сервисов
- `./monitor.sh horizon` - статус Horizon
- `./monitor.sh backup` - создание бэкапа БД
- `./monitor.sh optimize` - оптимизация приложения

#### `server-setup.sh`
Первичная настройка сервера:
- Установка Docker
- Создание пользователя deploy
- Настройка firewall (UFW)
- Установка fail2ban
- SSH настройка

#### `Makefile`
Упрощенные команды для разработки и деплоя:
- `make dev` - запуск локальной разработки (Sail)
- `make prod-up` - запуск production
- `make deploy` - деплой на production
- `make monitor` - мониторинг
- `make backup` - бэкап базы данных
- `make help` - список всех команд

### Документация

#### `QUICKSTART.md`
Пошаговая инструкция быстрого запуска на production:
- Подготовка сервера
- Клонирование репозитория
- Настройка окружения
- Запуск приложения
- GitHub Actions настройка
- Troubleshooting

#### `DEPLOYMENT.md`
Полное руководство по деплою и обслуживанию:
- Архитектура системы
- Детальная настройка
- Команды управления
- Мониторинг
- Бэкапы
- Масштабирование
- Безопасность

#### `CHECKLIST.md`
Production readiness checklist:
- Pre-deployment проверки
- Конфигурация
- Тестирование
- Безопасность
- CI/CD
- Performance
- Rollback процедура

#### `SSL-SETUP.md`
Настройка HTTPS с Let's Encrypt:
- Автоматическая установка Certbot
- Nginx SSL конфигурация
- Автообновление сертификатов
- Troubleshooting
- Тестирование SSL

#### `README.md`
Обновленный главный README с:
- Описанием проекта
- Технологическим стеком
- Инструкциями по запуску
- Ссылками на документацию

### Environment файлы

#### `.env.production.example`
Пример production переменных окружения:
- Базовые настройки приложения
- Database credentials
- Redis настройки
- Meilisearch конфигурация
- Mail настройки
- Horizon конфигурация

## 🚀 Как использовать

### Для первичной настройки сервера:

```bash
# На свежем Ubuntu/Debian сервере
sudo bash server-setup.sh
```

### Для деплоя приложения:

```bash
# Следуйте QUICKSTART.md
# Или используйте Makefile:
make prod-up
make deploy
```

### Для мониторинга:

```bash
./monitor.sh          # Dashboard
make monitor          # То же через Makefile
```

### Для локальной разработки:

```bash
# Используйте Laravel Sail (без изменений)
vendor/bin/sail up -d
# Или через Makefile:
make dev
```

## 📊 Архитектура

```
┌─────────────────────────────────────────────────┐
│                   Internet                       │
└────────────────────┬────────────────────────────┘
                     │
                     ▼
          ┌──────────────────────┐
          │   Nginx (Port 80/443)│
          │   - Static files     │
          │   - SSL termination  │
          │   - Reverse proxy    │
          └──────────┬───────────┘
                     │
                     ▼
          ┌──────────────────────┐
          │    App Container     │
          │  ┌─────────────────┐ │
          │  │  PHP-FPM        │ │
          │  └─────────────────┘ │
          │  ┌─────────────────┐ │
          │  │  Supervisor     │ │
          │  │  - Horizon      │ │
          │  │  - Scheduler    │ │
          │  └─────────────────┘ │
          └──┬──────────┬────┬──┘
             │          │    │
    ┌────────┘          │    └────────┐
    ▼                   ▼             ▼
┌────────┐        ┌─────────┐   ┌──────────────┐
│ MySQL  │        │  Redis  │   │ Meilisearch  │
│ (3306) │        │ (6379)  │   │   (7700)     │
└────────┘        └─────────┘   └──────────────┘
```

## 🔄 Workflow

### Development → Production:

```
Local Dev (Sail)
    ↓
Git commit & push
    ↓
GitHub Actions
    ├─ Run tests
    ├─ Build Docker image
    ├─ Push to GHCR
    └─ Deploy via SSH
        ↓
Production Server
    ├─ Pull new image
    ├─ Run migrations
    ├─ Cache configs
    └─ Restart services
```

## 🎯 Основные преимущества

✅ **Изолированные окружения** - Sail для разработки, отдельные контейнеры для production
✅ **Автоматический деплой** - GitHub Actions CI/CD
✅ **Легкое масштабирование** - Docker Compose
✅ **Полный мониторинг** - Встроенные скрипты и Horizon
✅ **Безопасность** - SSL, firewall, fail2ban, security headers
✅ **Производительность** - OPcache, Redis, Nginx caching
✅ **Надежность** - Health checks, supervisor, автоперезапуск
✅ **Простота управления** - Makefile и bash скрипты

## 📝 Следующие шаги

1. **Настройте сервер**: Запустите `server-setup.sh` на production сервере
2. **Настройте GitHub Secrets**: Следуйте `.github/SECRETS.md`
3. **Клонируйте репозиторий**: На production сервере
4. **Настройте .env**: Используйте `.env.production.example` как шаблон
5. **Запустите приложение**: `make prod-up` или следуйте `QUICKSTART.md`
6. **Настройте SSL**: Следуйте `SSL-SETUP.md` для HTTPS
7. **Проверьте деплой**: Push в `main` и наблюдайте GitHub Actions

## 🆘 Помощь

- **Быстрый старт**: [QUICKSTART.md](QUICKSTART.md)
- **Полная документация**: [DEPLOYMENT.md](DEPLOYMENT.md)
- **Checklist**: [CHECKLIST.md](CHECKLIST.md)
- **SSL**: [SSL-SETUP.md](SSL-SETUP.md)
- **GitHub Secrets**: [.github/SECRETS.md](.github/SECRETS.md)

## ✨ Удачного деплоя!
