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
# Ждём app
wait_for_app

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

wait_for_app() {
  echo -e "${YELLOW}⏳ Waiting for app container to be running...${NC}"

  while true; do
    STATUS=$(docker inspect -f '{{.State.Status}}' alarmstyle-prod-app-1 2>/dev/null || echo "starting")

    if [ "$STATUS" = "running" ]; then
      echo -e "${GREEN}✅ App container is running${NC}"
      break
    fi

    echo "Current status: $STATUS"
    sleep 2
  done
}
