#!/usr/bin/env bash

set -euo pipefail

echo "🚀 Starting deployment..."

COMPOSE_FILE="compose.prod.yaml"
APP_PORT="${APP_PORT:-8080}"
HEALTHCHECK_PATH="${HEALTHCHECK_PATH:-/}"

cleanup() {
    docker logout ghcr.io >/dev/null 2>&1 || true
}
trap cleanup EXIT

# --- GHCR login ---
if [ -n "${GHCR_PAT:-}" ]; then
    echo "🔐 Logging in to ghcr.io..."
    printf '%s' "$GHCR_PAT" | docker login ghcr.io -u "$GHCR_USERNAME" --password-stdin
fi

echo "📦 Updating code (reset to origin/master)..."
git fetch origin master
git checkout master  # ← Добавьте эту строку
git reset --hard origin/master

# --- Docker ---
echo "🐳 Pulling Docker images..."
docker compose -f "$COMPOSE_FILE" pull

echo "🔄 Restarting containers..."
docker compose -f "$COMPOSE_FILE" up -d --force-recreate

# --- Wait for health ---
echo "⏳ Waiting for containers..."
DEADLINE=120
START=$(date +%s)

while true; do
    UNHEALTHY=$(docker compose -f "$COMPOSE_FILE" ps -q \
      | xargs -r docker inspect -f '{{.State.Health.Status}}' \
      | grep -v healthy || true)

    [ -z "$UNHEALTHY" ] && break

    (( $(date +%s) - START > DEADLINE )) && {
        echo "❌ Healthcheck timeout"
        docker compose -f "$COMPOSE_FILE" ps
        exit 1
    }

    sleep 3
done

# --- Laravel ---
echo "🗄️ Running migrations..."
docker compose -f "$COMPOSE_FILE" exec -T app php artisan migrate --force

echo "🔧 Optimizing..."
docker compose -f "$COMPOSE_FILE" exec -T app php artisan config:clear
docker compose -f "$COMPOSE_FILE" exec -T app php artisan config:cache
docker compose -f "$COMPOSE_FILE" exec -T app php artisan route:cache
docker compose -f "$COMPOSE_FILE" exec -T app php artisan view:cache

# --- Workers ---
echo "🌅 Restarting Horizon & Scheduler..."
docker compose -f "$COMPOSE_FILE" restart horizon scheduler

# --- HTTP health ---
echo "🏥 HTTP health check..."
CODE=$(curl -s -o /dev/null -w "%{http_code}" "http://127.0.0.1:${APP_PORT}${HEALTHCHECK_PATH}")

[ "$CODE" = "200" ] || {
  echo "❌ Health check failed: $CODE"
  exit 1
}

echo "🧹 Cleaning images..."
docker image prune -f

echo "🎉 Deploy completed successfully!"
