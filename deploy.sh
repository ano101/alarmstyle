#!/bin/bash
set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}🚀 Deploy start${NC}"

# Login GHCR
echo "$GHCR_PAT" | docker login ghcr.io -u "$GHCR_USERNAME" --password-stdin

# Pull images
docker compose -f compose.prod.yaml pull

# Start containers (БЕЗ down!)
docker compose -f compose.prod.yaml up -d

echo -e "${GREEN}⏳ Waiting services...${NC}"
sleep 15

# МИГРАЦИИ — ТОЛЬКО ДОПОЛНЕНИЕ
echo -e "${GREEN}📊 Running migrations${NC}"
docker compose -f compose.prod.yaml exec -T app php artisan migrate --force

# Cache
docker compose -f compose.prod.yaml exec -T app php artisan optimize:clear
docker compose -f compose.prod.yaml exec -T app php artisan optimize
docker compose -f compose.prod.yaml exec -T app php artisan filament:cache-components

# Horizon
docker compose -f compose.prod.yaml exec -T app php artisan horizon:terminate

# SSR
docker compose -f compose.prod.yaml restart ssr

echo -e "${GREEN}✅ Deploy done${NC}"
