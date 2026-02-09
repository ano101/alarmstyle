#!/bin/bash
set -e
docker compose -p alarmstyle-prod down --remove-orphans || true

PROJECT="alarmstyle-prod"
COMPOSE="docker compose -p $PROJECT -f compose.prod.yaml"

echo "🚀 Deploy start"

echo "$GHCR_PAT" | docker login ghcr.io -u "$GHCR_USERNAME" --password-stdin

$COMPOSE pull
$COMPOSE up -d

echo "⏳ Waiting for MySQL..."

until $COMPOSE exec -T mysql mysqladmin ping -h "127.0.0.1" --silent; do
  sleep 2
done

echo "✅ MySQL is ready"

$COMPOSE exec -T app php artisan migrate --force
$COMPOSE exec -T app php artisan storage:link

# Генерация Ziggy routes с корректным APP_URL из .env
echo "🔄 Generating Ziggy routes..."
$COMPOSE exec -T app php artisan ziggy:generate resources/js/ziggy.js

$COMPOSE exec -T app php artisan optimize:clear
$COMPOSE exec -T app php artisan optimize
$COMPOSE exec -T app php artisan horizon:terminate

# Проверка APP_URL
APP_URL=$($COMPOSE exec -T app php artisan tinker --execute="echo config('app.url');" 2>/dev/null | tail -1)
if [[ ! "$APP_URL" =~ ^https:// ]]; then
    echo "⚠️  WARNING: APP_URL не начинается с https://"
    echo "   Текущее значение: $APP_URL"
    echo "   Установите в .env: APP_URL=https://your-domain.com"
fi

echo "✅ Deploy done"
