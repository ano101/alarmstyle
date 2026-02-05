# 🚀 AlarmStyle Production - Краткая справка для команды

## Где что находится?

### 📁 Документация (в корне проекта)
- **QUICKSTART.md** - Начни здесь! Пошаговая инструкция (20 минут)
- **DEPLOYMENT.md** - Полное руководство по деплою и обслуживанию
- **COMMANDS.md** - Все команды в одном месте (твоя шпаргалка!)
- **CHECKLIST.md** - Чеклист перед запуском production
- **SSL-SETUP.md** - Настройка HTTPS/SSL

### 🐳 Docker файлы
- **Dockerfile** - Production образ
- **compose.prod.yaml** - Production Docker Compose
- **docker/** - Конфигурации (nginx, php, supervisor, mysql)

### 🛠️ Скрипты (в корне, все исполняемые)
- **deploy.sh** - Деплой приложения
- **monitor.sh** - Мониторинг (запусти без параметров для dashboard)
- **server-setup.sh** - Первичная настройка сервера
- **preflight-check.sh** - Проверка перед деплоем
- **Makefile** - Упрощенные команды (запусти `make help`)

### 🔄 CI/CD
- **.github/workflows/deploy.yml** - Автодеплой из GitHub
- **.github/workflows/tests.yml** - Автотесты

---

## ⚡ Быстрые команды

### Локальная разработка (как раньше)
```bash
vendor/bin/sail up -d       # Запуск
vendor/bin/sail stop        # Остановка
vendor/bin/sail artisan ... # Artisan команды
```

### Production (новое!)
```bash
make help           # Все команды
make preflight      # Проверить перед деплоем
make prod-up        # Запустить production
make deploy         # Деплой с миграциями
make monitor        # Открыть dashboard мониторинга
make backup         # Создать бэкап БД
make prod-logs      # Смотреть логи
```

---

## 📞 Частые вопросы

### Q: Как проверить, что всё работает?
```bash
./monitor.sh health
# или
make health
```

### Q: Как посмотреть логи?
```bash
./monitor.sh logs app       # Логи приложения
./monitor.sh logs nginx     # Логи Nginx
make prod-logs              # Все логи
```

### Q: Как сделать деплой?
```bash
# Вариант 1: Автоматически через GitHub
git push origin main  # GitHub Actions сделает всё сам

# Вариант 2: Вручную на сервере
cd /var/www/alarmstyle
./deploy.sh
# или
make deploy
```

### Q: Как откатить изменения?
Смотри раздел "Rollback Procedure" в **CHECKLIST.md**

### Q: Где смотреть статус очередей?
```bash
./monitor.sh horizon
# или зайди на https://yourdomain.com/horizon
```

### Q: Как создать бэкап?
```bash
make backup
# или
./monitor.sh backup
```

### Q: Что делать если что-то сломалось?
1. Проверь логи: `./monitor.sh logs`
2. Проверь здоровье сервисов: `./monitor.sh health`
3. Смотри раздел "Troubleshooting" в **DEPLOYMENT.md**
4. В крайнем случае: `make prod-restart`

---

## 🎯 Первый деплой (для DevOps)

```bash
# 1. На чистом сервере (Ubuntu/Debian)
sudo bash server-setup.sh

# 2. Как пользователь deploy
cd /var/www/alarmstyle
git clone https://github.com/your-username/alarmstyle.git .

# 3. Настройка
cp .env.production.example .env
nano .env  # Заполни переменные!

# 4. Проверка
./preflight-check.sh

# 5. Запуск
make prod-up
make deploy

# 6. Проверка работы
./monitor.sh health
curl http://localhost/health
```

Подробности в **QUICKSTART.md**!

---

## 🔐 GitHub Actions Setup (для CI/CD)

Добавь эти secrets в GitHub (Settings → Secrets → Actions):

- `PROD_HOST` - IP сервера
- `PROD_USERNAME` - SSH пользователь (обычно `deploy`)
- `PROD_SSH_KEY` - SSH приватный ключ
- `PROD_APP_PATH` - Путь к приложению (обычно `/var/www/alarmstyle`)
- `PROD_APP_URL` - URL приложения

Подробности в **.github/SECRETS.md**!

---

## 📊 Production URLs

- **Приложение**: https://yourdomain.com
- **Админка**: https://yourdomain.com/admin
- **Horizon**: https://yourdomain.com/horizon
- **Health Check**: https://yourdomain.com/health

---

## 💡 Советы

1. **Добавь алиасы** в `~/.bashrc` на production сервере:
   ```bash
   alias dcp='docker compose -f compose.prod.yaml'
   alias art='docker compose -f compose.prod.yaml exec app php artisan'
   alias monitor='cd /var/www/alarmstyle && ./monitor.sh'
   ```

2. **Используй `make` команды** - они короче и проще:
   ```bash
   make deploy     # вместо ./deploy.sh
   make monitor    # вместо ./monitor.sh
   make backup     # вместо ./monitor.sh backup
   ```

3. **Регулярно проверяй мониторинг**:
   ```bash
   ./monitor.sh  # Показывает dashboard с основной инфой
   ```

4. **Делай бэкапы!**
   ```bash
   make backup  # Еженедельно или перед большими изменениями
   ```

---

## 🆘 В случае проблем

1. Смотри логи: `./monitor.sh logs`
2. Проверь здоровье: `./monitor.sh health`
3. Читай документацию: **DEPLOYMENT.md**, **COMMANDS.md**
4. Проверь раздел Troubleshooting в документации

---

## ✅ Важно помнить

- ✨ Локальная разработка не изменилась - используй Sail как раньше
- 🐳 Production теперь на Docker - используй `compose.prod.yaml`
- 🤖 GitHub Actions деплоит автоматически при push в `main`
- 📊 Всегда проверяй мониторинг после деплоя
- 💾 Делай бэкапы перед важными изменениями
- 📖 Вся документация в корне проекта (*.md файлы)

---

**🎉 Успехов в работе!**

*Если что-то непонятно - читай QUICKSTART.md или DEPLOYMENT.md*
