#!/bin/bash
set -e

echo "🚀 Starting deployment..."

# Логин в GitHub Container Registry
if [ -n "$GHCR_PAT" ] && [ -n "$GHCR_USERNAME" ]; then
    echo "🔐 Logging in to GitHub Container Registry..."
    echo "$GHCR_PAT" | docker login ghcr.io -u "$GHCR_USERNAME" --password-stdin
else
    echo "⚠️  GHCR credentials not provided, skipping login..."
fi

echo "📦 Updating code (reset to origin/master)..."
git fetch origin master
git reset --hard origin/master

echo "🐳 Pulling Docker images..."
docker compose -f compose.prod.yaml pull

echo "🔄 Restarting services..."
docker compose -f compose.prod.yaml up -d --remove-orphans

echo "⏳ Waiting for services to be healthy..."
sleep 10

echo "📊 Running migrations..."
docker compose -f compose.prod.yaml exec -T app php artisan migrate --force

echo "🗑️  Clearing cache..."
docker compose -f compose.prod.yaml exec -T app php artisan config:clear
docker compose -f compose.prod.yaml exec -T app php artisan cache:clear

echo "⚡ Optimizing..."
docker compose -f compose.prod.yaml exec -T app php artisan config:cache
docker compose -f compose.prod.yaml exec -T app php artisan route:cache
docker compose -f compose.prod.yaml exec -T app php artisan view:cache
docker compose -f compose.prod.yaml exec -T app php artisan filament:cache-components

echo "🔄 Restarting Horizon..."
docker compose -f compose.prod.yaml exec -T app php artisan horizon:terminate

echo "✅ Deployment completed successfully!"
