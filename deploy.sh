#!/bin/bash
set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}🚀 Starting deployment...${NC}"

# Check required environment variables
if [ -z "$GHCR_PAT" ] || [ -z "$GHCR_USERNAME" ] || [ -z "$GITHUB_REPOSITORY" ]; then
    echo -e "${RED}❌ Missing required environment variables!${NC}"
    echo "Required: GHCR_PAT, GHCR_USERNAME, GITHUB_REPOSITORY"
    exit 1
fi

# Check APP_URL in .env
if [ -f .env ]; then
    APP_URL=$(grep "^APP_URL=" .env | cut -d '=' -f2)
    if [[ ! $APP_URL =~ ^https:// ]]; then
        echo -e "${YELLOW}⚠️  WARNING: APP_URL in .env does not start with https://${NC}"
        echo "Current APP_URL: $APP_URL"
        echo "This may cause Mixed Content errors!"
        echo "Press Ctrl+C to abort or wait 5 seconds to continue..."
        sleep 5
    fi
fi

# Login to GitHub Container Registry
echo -e "${GREEN}🔐 Logging in to GitHub Container Registry...${NC}"
echo "$GHCR_PAT" | docker login ghcr.io -u "$GHCR_USERNAME" --password-stdin

# Pull latest image
echo -e "${GREEN}📥 Pulling latest image...${NC}"
docker compose -f compose.prod.yaml pull

# Check existing volumes before starting services
echo -e "${GREEN}🔍 Checking existing volumes...${NC}"
EXISTING_VOLUMES=$(docker volume ls --format "{{.Name}}" | grep -E "(mysql-data|redis-data|meilisearch-data)" || true)
if [ -n "$EXISTING_VOLUMES" ]; then
    echo -e "${GREEN}Found existing volumes:${NC}"
    echo "$EXISTING_VOLUMES"
else
    echo -e "${YELLOW}No existing data volumes found - this appears to be first deployment${NC}"
fi

# Start services
echo -e "${GREEN}🔄 Starting services...${NC}"
docker compose -f compose.prod.yaml up -d

# Wait for services to be healthy
echo -e "${GREEN}⏳ Waiting for services to be healthy...${NC}"
sleep 10

# Check if this is first deployment (check if migrations table exists)
echo -e "${GREEN}🔍 Checking deployment status...${NC}"
TABLE_COUNT=$(docker compose -f compose.prod.yaml exec -T app php artisan tinker --execute="echo \DB::table('migrations')->count();" 2>/dev/null || echo "0")
IS_FIRST_DEPLOY=false

if [ "$TABLE_COUNT" = "0" ] || [ -z "$TABLE_COUNT" ]; then
    IS_FIRST_DEPLOY=true
    echo -e "${YELLOW}📦 First deployment detected - will run full setup${NC}"
else
    echo -e "${GREEN}♻️  Existing database found ($TABLE_COUNT migrations applied) - will preserve data${NC}"
fi

# Run migrations
echo -e "${GREEN}📊 Running migrations...${NC}"
docker compose -f compose.prod.yaml exec -T app php artisan migrate --force

# Run seeders only on first deploy
if [ "$IS_FIRST_DEPLOY" = true ]; then
    echo -e "${GREEN}🌱 Running seeders...${NC}"
    docker compose -f compose.prod.yaml exec -T app php artisan db:seed --force
fi

# Clear and optimize cache
echo -e "${GREEN}🗑️  Clearing cache...${NC}"
docker compose -f compose.prod.yaml exec -T app php artisan config:clear
docker compose -f compose.prod.yaml exec -T app php artisan cache:clear

echo -e "${GREEN}⚡ Optimizing...${NC}"
docker compose -f compose.prod.yaml exec -T app php artisan config:cache
docker compose -f compose.prod.yaml exec -T app php artisan route:cache
docker compose -f compose.prod.yaml exec -T app php artisan view:cache
docker compose -f compose.prod.yaml exec -T app php artisan filament:cache-components

# Restart Horizon
echo -e "${GREEN}🔄 Restarting Horizon...${NC}"
docker compose -f compose.prod.yaml exec -T app php artisan horizon:terminate

echo -e "${GREEN}✅ Deployment completed successfully!${NC}"

# Show important info
if [ "$IS_FIRST_DEPLOY" = true ]; then
    echo -e "${YELLOW}"
    echo "============================================"
    echo "⚠️  FIRST DEPLOYMENT COMPLETED"
    echo "============================================"
    echo "Admin user created from DatabaseSeeder"
    echo "Email: ano101@mail.ru"
    echo "Password: 963852"
    echo ""
    echo "Don't forget to:"
    echo "1. Check APP_URL in .env is set to https://..."
    echo "2. Configure native nginx as per docs/NGINX_PROXY_SETUP.md"
    echo "============================================"
    echo -e "${NC}"
fi
