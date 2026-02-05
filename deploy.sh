#!/bin/bash

# Production deployment script
# Run this on your production server after initial setup

set -e

echo "🚀 Starting deployment..."

# Pull latest code
echo "📦 Pulling latest changes..."
git pull origin main

# Pull latest Docker images
echo "🐳 Pulling Docker images..."
docker compose -f compose.prod.yaml pull

# Stop and remove old containers
echo "🔄 Restarting containers..."
docker compose -f compose.prod.yaml up -d --force-recreate

# Wait for services to be ready
echo "⏳ Waiting for services to start..."
sleep 10

# Run migrations
echo "🗄️ Running migrations..."
docker compose -f compose.prod.yaml exec -T app php artisan migrate --force

# Clear and cache configuration
echo "🔧 Optimizing application..."
docker compose -f compose.prod.yaml exec -T app php artisan config:cache
docker compose -f compose.prod.yaml exec -T app php artisan route:cache
docker compose -f compose.prod.yaml exec -T app php artisan view:cache
docker compose -f compose.prod.yaml exec -T app php artisan filament:cache-components

# Restart Horizon and Scheduler
echo "🌅 Restarting Horizon and Scheduler..."
docker compose -f compose.prod.yaml restart horizon scheduler

# Clean up old images
echo "🧹 Cleaning up..."
docker image prune -af

# Health check
echo "🏥 Running health check..."
sleep 5
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/health)
if [ "$HTTP_CODE" -eq 200 ]; then
    echo "✅ Deployment successful! Application is healthy."
else
    echo "❌ Health check failed with HTTP code: $HTTP_CODE"
    exit 1
fi

echo "🎉 Deployment completed successfully!"
