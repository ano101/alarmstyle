Existing deploy: only recreate app containers
    docker compose -f compose.prod.yaml up -d --no-deps --force-recreate nginx app horizon scheduler
    # Ensure database services are running (without recreating)
    docker compose -f compose.prod.yaml up -d --no-recreate mysql redis meilisearch
fi

# Wait for services to be healthy
echo -e "${GREEN}⏳ Waiting for services to be healthy...${NC}"
sleep 10

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
