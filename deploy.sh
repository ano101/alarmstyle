#!/bin/bash
set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

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

# Check if this is first deployment (no volumes exist)
MYSQL_VOLUME=$(docker volume ls -q | grep "$(basename $(pwd))_mysql-data" || true)
IS_FIRST_DEPLOY=false

if [ -z "$MYSQL_VOLUME" ]; then
    IS_FIRST_DEPLOY=true
    echo -e "${YELLOW}📦 First deployment detected - will run full migration${NC}"
else
    echo -e "${GREEN}♻️  Existing database found - will run incremental migrations${NC}"
fi

# Restart services (without recreating database containers if they exist)
echo -e "${GREEN}🔄 Restarting services...${NC}"
if [ "$IS_FIRST_DEPLOY" = true ]; then
    # First deploy: create everything
    docker compose -f compose.prod.yaml up -d --force-recreate
else
    # Existing deploy: only recreate app containers, not databases
    docker compose -f compose.prod.yaml up -d --no-deps --force-recreate nginx app horizon scheduler
    # Ensure database services are running
    docker compose -f compose.prod.yaml up -d mysql redis meilisearch
fi

# Wait for services to be healthy
echo -e "${GREEN}⏳ Waiting for services to be healthy...${NC}"
docker compose -f compose.prod.yaml exec -T app php -v > /dev/null 2>&1 || sleep 5

# Run migrations
echo -e "${GREEN}📊 Running migrations...${NC}"
if [ "$IS_FIRST_DEPLOY" = true ]; then
    docker compose -f compose.prod.yaml exec -T app php artisan migrate --force
else
    docker compose -f compose.prod.yaml exec -T app php artisan migrate --force --no-interaction
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
    echo "Don't forget to:"
    echo "1. Create admin user: docker compose -f compose.prod.yaml exec app php artisan make:filament-user"
    echo "2. Check APP_URL in .env is set to https://..."
    echo "3. Configure native nginx as per docs/NGINX_PROXY_SETUP.md"
    echo "============================================"
    echo -e "${NC}"
fi
