# Production Setup Checklist

## 🎯 Pre-deployment

- [ ] Сервер настроен (запущен `server-setup.sh`)
- [ ] Docker и Docker Compose установлены
- [ ] Пользователь `deploy` создан с правами Docker
- [ ] SSH ключ добавлен в `~/.ssh/authorized_keys`
- [ ] Firewall настроен (порты 22, 80, 443)
- [ ] Репозиторий склонирован в `/var/www/alarmstyle`

## 🔐 Configuration

- [ ] Файл `.env` создан из `.env.production.example`
- [ ] `APP_KEY` сгенерирован
- [ ] `APP_URL` настроен на production домен
- [ ] `DB_PASSWORD` установлен (сильный пароль)
- [ ] `REDIS_PASSWORD` установлен (сильный пароль)
- [ ] `MEILISEARCH_KEY` установлен (сильный пароль)
- [ ] Настройки почты (MAIL_*) заполнены
- [ ] Все секретные ключи уникальны и безопасны

## 🚀 Deployment

- [ ] Контейнеры запущены: `docker compose -f compose.prod.yaml up -d --build`
- [ ] Все контейнеры в статусе "healthy": `docker compose -f compose.prod.yaml ps`
- [ ] Миграции выполнены: `docker compose -f compose.prod.yaml exec app php artisan migrate --force`
- [ ] Кеши сгенерированы (config, route, view, filament)
- [ ] Scout индексы синхронизированы
- [ ] Данные импортированы (если есть сиды)

## 🔍 Testing

- [ ] Health check работает: `curl http://localhost/health` → 200 OK
- [ ] Главная страница загружается
- [ ] Административная панель доступна: `/admin`
- [ ] Horizon dashboard работает: `/horizon`
- [ ] Поиск работает корректно
- [ ] Очереди обрабатываются (проверить Horizon)
- [ ] Scheduler выполняется

## 🔒 Security

- [ ] SSL/TLS сертификат установлен (Let's Encrypt или другой)
- [ ] HTTPS редирект настроен
- [ ] Nginx security headers добавлены
- [ ] Database credentials сильные и уникальные
- [ ] `.env` файл не доступен через веб (проверить)
- [ ] Fail2ban активен для защиты SSH
- [ ] Регулярные бэкапы настроены

## 📊 Monitoring

- [ ] `./monitor.sh` работает и показывает статус
- [ ] Логи доступны и корректны
- [ ] Health checks всех сервисов проходят
- [ ] Horizon показывает активность воркеров
- [ ] Disk space мониторится
- [ ] Memory usage в норме

## 🔄 CI/CD (GitHub Actions)

- [ ] GitHub Secrets добавлены:
  - [ ] `PROD_HOST`
  - [ ] `PROD_USERNAME`
  - [ ] `PROD_SSH_KEY`
  - [ ] `PROD_APP_PATH`
  - [ ] `PROD_APP_URL`
- [ ] SSH подключение из GitHub Actions работает
- [ ] Тестовый деплой через push в `main` выполнен успешно
- [ ] Workflow "Tests and Code Quality" проходит

## 📦 Performance

- [ ] OPcache включен и настроен
- [ ] Nginx gzip compression работает
- [ ] Static assets кешируются с правильными headers
- [ ] Database indexes оптимизированы
- [ ] Redis используется для sessions и cache
- [ ] Horizon workers достаточно для нагрузки

## 🔧 Maintenance

- [ ] Cron для бэкапов настроен
- [ ] Log rotation настроен
- [ ] Disk cleanup автоматизирован
- [ ] Monitoring alerts настроены (если есть)
- [ ] Документация доступна команде

## 📋 Post-deployment

- [ ] DNS записи обновлены (A/AAAA/CNAME)
- [ ] Email notifications работают
- [ ] Error reporting настроен (Sentry/Bugsnag/etc)
- [ ] Analytics добавлен (если нужен)
- [ ] Robots.txt настроен
- [ ] Sitemap.xml сгенерирован

## 🎉 Launch

- [ ] Final smoke tests пройдены
- [ ] Команда уведомлена о запуске
- [ ] Monitoring dashboard открыт
- [ ] Support team готов
- [ ] Rollback plan подготовлен

---

## 🆘 Emergency Contacts

- **Infrastructure**: [contact info]
- **DevOps**: [contact info]
- **Backend Team**: [contact info]
- **Support**: [contact info]

## 📞 Useful Commands

```bash
# Проверить статус
./monitor.sh dashboard

# Создать бэкап
make backup

# Restart приложения
docker compose -f compose.prod.yaml restart app

# Просмотр логов
docker compose -f compose.prod.yaml logs -f app

# Очистить кеши
docker compose -f compose.prod.yaml exec app php artisan cache:clear
docker compose -f compose.prod.yaml exec app php artisan config:clear

# Перезапустить Horizon
docker compose -f compose.prod.yaml exec app php artisan horizon:terminate
```

## 🔄 Rollback Procedure

Если что-то пошло не так:

1. Откатить Docker image:
```bash
docker compose -f compose.prod.yaml down
# Поменять тег image в compose.prod.yaml на предыдущий
docker compose -f compose.prod.yaml up -d
```

2. Откатить миграции (если нужно):
```bash
docker compose -f compose.prod.yaml exec app php artisan migrate:rollback
```

3. Восстановить из бэкапа:
```bash
docker compose -f compose.prod.yaml exec -T mysql mysql -u root -p${DB_PASSWORD} ${DB_DATABASE} < backups/backup-YYYYMMDD.sql
```
