# GitHub Actions Secrets Configuration Guide

Для настройки автоматического деплоя необходимо добавить следующие секреты в GitHub:

## Обязательные секреты

### Server Access
- **PROD_HOST**: IP адрес или домен production сервера
  Пример: `123.45.67.89` или `server.yourdomain.com`

- **PROD_USERNAME**: SSH пользователь на production сервере
  Пример: `deploy` или `root`

- **PROD_SSH_KEY**: Приватный SSH ключ для доступа к серверу
  Генерация ключа:
  ```bash
  ssh-keygen -t ed25519 -C "github-actions@yourdomain.com" -f ~/.ssh/github_deploy
  cat ~/.ssh/github_deploy  # Скопируйте содержимое в секрет
  cat ~/.ssh/github_deploy.pub  # Добавьте на сервер в ~/.ssh/authorized_keys
  ```

- **PROD_APP_PATH**: Полный путь к приложению на сервере
  Пример: `/var/www/alarmstyle` или `/home/deploy/alarmstyle`

- **PROD_APP_URL**: URL для health check после деплоя
  Пример: `https://alarmstyle.com` или `http://123.45.67.89`

### Optional
- **PROD_SSH_PORT**: SSH порт (если отличается от 22)
  По умолчанию: `22`

## Как добавить секреты в GitHub:

1. Откройте ваш репозиторий на GitHub
2. Перейдите в Settings → Secrets and variables → Actions
3. Нажмите "New repository secret"
4. Введите имя секрета и его значение
5. Повторите для всех необходимых секретов

## Настройка сервера для GitHub Actions

На production сервере выполните:

```bash
# Создайте пользователя для деплоя (если нужно)
sudo adduser deploy
sudo usermod -aG docker deploy

# Добавьте публичный SSH ключ
mkdir -p ~/.ssh
nano ~/.ssh/authorized_keys
# Вставьте содержимое github_deploy.pub
chmod 600 ~/.ssh/authorized_keys
chmod 700 ~/.ssh

# Настройте Git для доступа к приватным репозиториям (если нужно)
# Используйте SSH ключ или Personal Access Token
```

## Тестирование SSH подключения

Локально проверьте подключение:
```bash
ssh -i ~/.ssh/github_deploy deploy@your-server-ip
```

## Container Registry Authentication

GitHub Actions автоматически использует `GITHUB_TOKEN` для публикации образов в GitHub Container Registry (ghcr.io).
Никаких дополнительных секретов не требуется.

### Для приватных репозиториев (опционально)

Если ваш репозиторий приватный и образы в GHCR также приватные, на production сервере необходимо настроить аутентификацию:

1. Создайте Personal Access Token (PAT) на GitHub:
   - Settings → Developer settings → Personal access tokens → Tokens (classic)
   - Создайте новый токен с правами: `read:packages`
   - Скопируйте токен

2. Добавьте переменные окружения на production сервере:
   ```bash
   # В .env на сервере или в переменных окружения
   export GHCR_PAT="ghp_your_personal_access_token"
   export GHCR_USERNAME="your_github_username"
   ```

3. Скрипт `deploy.sh` автоматически использует эти переменные для логина в GHCR

**Важно**: PAT должен быть доступен при запуске deploy.sh на сервере.

## Проверка workflow

После настройки секретов:
1. Сделайте commit и push в ветку `main`
2. Перейдите в Actions в вашем репозитории
3. Следите за выполнением workflow "Deploy to Production"

## Безопасность

- **НЕ** коммитьте приватные ключи в репозиторий
- Используйте отдельного пользователя с ограниченными правами для деплоя
- Регулярно ротируйте SSH ключи
- Ограничьте доступ к серверу через firewall (разрешите только GitHub Actions IP)
- Используйте сильные пароли для баз данных

## GitHub Actions IP адреса

Для дополнительной безопасности можно ограничить SSH доступ только с IP GitHub Actions:
https://api.github.com/meta (ищите "actions" в JSON)

Или используйте VPN/Bastion host для доступа к production серверу.
