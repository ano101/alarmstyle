#!/bin/bash
set -e

PROJECT="alarmstyle-prod"
COMPOSE="docker compose -p $PROJECT -f compose.prod.yaml"

echo "🚀 Deploy start"

echo "$GHCR_PAT" | docker login ghcr.io -u "$GHCR_USERNAME" --password-stdin

$COMPOSE pull
$COMPOSE up -d

echo "⏳ Waiting for app..."
sleep 5

$COMPOSE exec -T app php artisan migrate --force
$COMPOSE exec -T app php artisan optimize:clear
$COMPOSE exec -T app php artisan optimize
$COMPOSE exec -T app php artisan horizon:terminate


echo "✅ Deploy done"
