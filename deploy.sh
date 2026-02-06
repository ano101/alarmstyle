#!/usr/bin/env bash
set -e

PROJECT=alarmstyle-prod
COMPOSE="docker compose -p $PROJECT -f compose.prod.yaml"

echo "🚀 Deploy started for $PROJECT"

# 1️⃣ Обновляем код
echo "📦 Updating code"
git fetch origin
git reset --hard origin/master

# 2️⃣ Логинимся в GHCR
echo "🔐 Login to GHCR"
echo "$GITHUB_TOKEN" | docker login ghcr.io -u "$GITHUB_ACTOR" --password-stdin

# 3️⃣ Поднимаем ИНФРАСТРУКТУРУ (один раз, без пересоздания)
echo "🧱 Starting infrastructure (mysql, redis, meili)"
$COMPOSE up -d --no-recreate mysql redis meilisearch

# 4️⃣ Ждём MySQL
echo "⏳ Waiting for MySQL to be ready..."
until $COMPOSE exec -T mysql mysqladmin ping -h localhost -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent; do
  sleep 2
done
echo "✅ MySQL is ready"

# 5️⃣ Собираем app-образы
echo "🏗️ Building app images"
$COMPOSE build app horizon scheduler ssr

# 6️⃣ Останавливаем старые app-контейнеры
echo "🛑 Stopping old app containers"
$COMPOSE stop app horizon scheduler ssr nginx

# 7️⃣ Запускаем app (БЕЗ mysql / redis / meili)
echo "▶️ Starting app containers"
$COMPOSE up -d --no-deps app horizon scheduler ssr nginx

# 8️⃣ Прогреваем кеш
echo "🧠 Warming cache"
$COMPOSE exec -T app php artisan key:generate --force || true
$COMPOSE exec -T app php artisan config:clear
$COMPOSE exec -T app php artisan config:cache
$COMPOSE exec -T app php artisan route:cache
$COMPOSE exec -T app php artisan view:clear

# 9️⃣ Миграции (ОДИН РАЗ, безопасно)
echo "🗄️ Running migrations"
$COMPOSE exec -T app php artisan migrate --force

# 🔟 Перезапуск horizon
echo "🔄 Restarting Horizon"
$COMPOSE exec -T app php artisan horizon:terminate || true

echo "✅ Deploy finished successfully 🎉"
