#!/bin/bash
set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

PROJECT="alarmstyle-prod"
COMPOSE="docker compose -p $PROJECT -f compose.prod.yaml"

# ----------------------------
# Wait for app to start
# ----------------------------
wait_for_app() {
  echo -e "${YELLOW}⏳ Waiting for app container to start...${NC}"

  MAX_ATTEMPTS=15
  ATTEMPT=0

  while [ $ATTEMPT -lt $MAX_ATTEMPTS ]; do
    ATTEMPT=$((ATTEMPT+1))

    RUNNING=$(docker inspect -f '{{.State.Running}}' ${PROJECT}-app-1 2>/dev/null)

    if [ "$RUNNING" = "true" ]; then
      echo -e "${GREEN}✅ App container is running${NC}"
      sleep 3
      return 0
    fi

    echo "Waiting for app container... (attempt $ATTEMPT/$MAX_ATTEMPTS)"
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
# Ensure databases are running (НЕ пересоздаём их!)
# ----------------------------
echo -e "${GREEN}📦 Ensuring databases are running...${NC}"
# ВАЖНО: --no-recreate чтобы НЕ пересоздавать контейнеры (иначе потеряем данные!)
$COMPOSE up -d --no-recreate mysql redis meilisearch

# Ждём пока databases станут healthy
echo -e "${YELLOW}⏳ Waiting for databases...${NC}"
sleep 10

# ----------------------------
# Recreate app containers (keep databases running!)
# ----------------------------
echo -e "${GREEN}🔄 Recreating app containers...${NC}"
# Останавливаем только app-related контейнеры (НЕ mysql, redis, meilisearch!)
$COMPOSE stop app horizon scheduler ssr nginx || true
$COMPOSE rm -f app horizon scheduler ssr nginx || true

# Запускаем всё (databases уже запущены, app контейнеры пересоздадутся)
$COMPOSE up -d

# ----------------------------
# Wait for app
# ----------------------------
wait_for_app

# ----------------------------
# Migrations
# ----------------------------
$COMPOSE exec -T app cat .env | grep DB_
$COMPOSE exec -T app env | grep DB_
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
