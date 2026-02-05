#!/bin/bash
set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

PROJECT="alarmstyle-prod"
COMPOSE="docker compose -f compose.prod.yaml"

# ----------------------------
# Wait for app healthcheck
# ----------------------------
wait_for_app() {
  echo -e "${YELLOW}⏳ Waiting for app to become healthy...${NC}"

  MAX_ATTEMPTS=30
  ATTEMPT=0

  while [ $ATTEMPT -lt $MAX_ATTEMPTS ]; do
    ATTEMPT=$((ATTEMPT+1))

    # Проверяем состояние контейнера
    HEALTH=$(docker inspect -f '{{.State.Health.Status}}' ${PROJECT}-app-1 2>/dev/null)
    RUNNING=$(docker inspect -f '{{.State.Running}}' ${PROJECT}-app-1 2>/dev/null)

    # Если healthy - выходим
    if [ "$HEALTH" = "healthy" ]; then
      echo -e "${GREEN}✅ App container is healthy${NC}"
      return 0
    fi

    # Если нет healthcheck, но контейнер запущен - выходим
    if [ -z "$HEALTH" ] && [ "$RUNNING" = "true" ]; then
      echo -e "${GREEN}✅ App container is running (no healthcheck)${NC}"
      sleep 5
      return 0
    fi

    echo "Waiting... health=$HEALTH running=$RUNNING (attempt $ATTEMPT/$MAX_ATTEMPTS)"
    sleep 2
  done

  echo -e "${YELLOW}⚠️ Timeout waiting for app, continuing anyway...${NC}"
  return 0
}

echo -e "${GREEN}🚀 Deploy start${NC}"

# ----------------------------
# Login GHCR
# ----------------------------
echo "$GHCR_PAT" | docker login ghcr.io -u "$GHCR_USERNAME" --password-stdin

# ----------------------------
# Pull images
# ----------------------------
echo -e "${GREEN}📥 Pulling latest images...${NC}"
$COMPOSE pull

# ----------------------------
# Copy public from image to host
# ----------------------------
echo -e "${GREEN}📦 Copying public assets from image...${NC}"
chmod +x copy-public.sh
./copy-public.sh

# ----------------------------
# FULL recreate containers
# ----------------------------
echo -e "${GREEN}🔄 Recreating all containers...${NC}"
$COMPOSE down
$COMPOSE up -d --force-recreate

# ----------------------------
# Wait for app
# ----------------------------
wait_for_app

# ----------------------------
# Migrations
# ----------------------------
echo -e "${GREEN}📊 Running migrations${NC}"
$COMPOSE exec -T app php artisan migrate --force

# ----------------------------
# Cache
# ----------------------------
$COMPOSE exec -T app php artisan optimize:clear
$COMPOSE exec -T app php artisan optimize
$COMPOSE exec -T app php artisan filament:cache-components

# ----------------------------
# Horizon
# ----------------------------
$COMPOSE exec -T app php artisan horizon:terminate

# ----------------------------
# SSR
# ----------------------------
$COMPOSE restart ssr

echo -e "${GREEN}✅ Deploy done${NC}"
