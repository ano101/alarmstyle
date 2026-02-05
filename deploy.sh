#!/bin/bash

# Production deployment script
# Run this on your production server after initial setup

set -euo pipefail

echo "🚀 Starting deployment..."

COMPOSE_FILE="compose.prod.yaml"
APP_PORT="${APP_PORT:-8080}"
HEALTHCHECK_PATH="${HEALTHCHECK_PATH:-/}"

cleanup() {
    if command -v docker >/dev/null 2>&1; then
        docker logout ghcr.io >/dev/null 2>&1 || true
    fi
}
trap cleanup EXIT

# Auth to GHCR (recommended for pulling private images)
if [ -n "${GHCR_PAT:-}" ]; then
    if [ -z "${GHCR_USERNAME:-}" ]; then
        echo "❌ GHCR_USERNAME is required when GHCR_PAT is set"
        exit 1
    fi

    echo "🔐 Logging in to ghcr.io..."
    printf '%s' "$GHCR_PAT" | docker login ghcr.io -u "$GHCR_USERNAME" --password-stdin
fi

# Pull latest Docker images
echo "🐳 Pulling Docker images..."
docker compose -f "$COMPOSE_FILE" pull

# Restart containers
echo "🔄 Restarting containers..."
docker compose -f "$COMPOSE_FILE" up -d --force-recreate

# Wait for services to be ready (prefer healthchecks over fixed sleep)
echo "⏳ Waiting for containers to become healthy..."
DEADLINE_SECONDS="${HEALTHCHECK_TIMEOUT_SECONDS:-120}"
START_TS="$(date +%s)"

while true; do
    UNHEALTHY_IDS=$(docker compose -f "$COMPOSE_FILE" ps -q | xargs -r docker inspect -f '{{if .State.Health}}{{if ne .State.Health.Status "healthy"}}{{.Id}}{{"\n"}}{{end}}{{end}}' 2>/dev/null || true)

    if [ -z "$UNHEALTHY_IDS" ]; then
        break
    fi

    NOW_TS="$(date +%s)"
    if [ $((NOW_TS - START_TS)) -ge "$DEADLINE_SECONDS" ]; then
        echo "❌ Timeout waiting for containers to be healthy"
        docker compose -f "$COMPOSE_FILE" ps
        exit 1
    fi

    sleep 3
done

# Run migrations
echo "🗄️ Running migrations..."
docker compose -f "$COMPOSE_FILE" exec -T app php artisan migrate --force

# Clear and cache configuration
echo "🔧 Optimizing application..."
docker compose -f "$COMPOSE_FILE" exec -T app php artisan config:cache
docker compose -f "$COMPOSE_FILE" exec -T app php artisan route:cache
docker compose -f "$COMPOSE_FILE" exec -T app php artisan view:cache
docker compose -f "$COMPOSE_FILE" exec -T app php artisan filament:cache-components

# Restart Horizon and Scheduler
echo "🌅 Restarting Horizon and Scheduler..."
docker compose -f "$COMPOSE_FILE" restart horizon scheduler

# Health check
echo "🏥 Running HTTP health check..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "http://127.0.0.1:${APP_PORT}${HEALTHCHECK_PATH}")
if [ "$HTTP_CODE" -eq 200 ]; then
    echo "✅ Deployment successful! Application is healthy."
else
    echo "❌ Health check failed with HTTP code: $HTTP_CODE"
    exit 1
fi

# Clean up old images (be conservative; avoid removing tagged images)
echo "🧹 Cleaning up dangling images..."
docker image prune -f

echo "🎉 Deployment completed successfully!"
