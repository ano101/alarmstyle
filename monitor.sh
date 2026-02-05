#!/bin/bash

# Production monitoring and maintenance script

COMPOSE_FILE="compose.prod.yaml"

function show_status() {
    echo "=== Container Status ==="
    docker compose -f $COMPOSE_FILE ps
    echo ""
}

function show_logs() {
    local service=${1:-app}
    local lines=${2:-50}
    echo "=== Logs for $service (last $lines lines) ==="
    docker compose -f $COMPOSE_FILE logs --tail=$lines $service
}

function show_resources() {
    echo "=== Resource Usage ==="
    docker stats --no-stream $(docker compose -f $COMPOSE_FILE ps -q)
    echo ""
}

function horizon_status() {
    echo "=== Horizon Status ==="
    docker compose -f $COMPOSE_FILE exec app php artisan horizon:status
    echo ""
}

function queue_stats() {
    echo "=== Queue Statistics ==="
    docker compose -f $COMPOSE_FILE exec app php artisan queue:monitor
    echo ""
}

function cache_status() {
    echo "=== Cache Status ==="
    docker compose -f $COMPOSE_FILE exec app php artisan cache:table 2>/dev/null || echo "Cache table not set up"
    echo ""
}

function backup_db() {
    local backup_name="backup-$(date +%Y%m%d-%H%M%S).sql"
    echo "Creating database backup: $backup_name"
    docker compose -f $COMPOSE_FILE exec -T mysql mysqldump -u root -p${DB_PASSWORD} ${DB_DATABASE} > "backups/$backup_name"
    echo "Backup created successfully!"
}

function restart_services() {
    echo "Restarting services..."
    docker compose -f $COMPOSE_FILE restart
    echo "Services restarted!"
}

function clear_cache() {
    echo "Clearing application cache..."
    docker compose -f $COMPOSE_FILE exec app php artisan cache:clear
    docker compose -f $COMPOSE_FILE exec app php artisan config:clear
    docker compose -f $COMPOSE_FILE exec app php artisan route:clear
    docker compose -f $COMPOSE_FILE exec app php artisan view:clear
    echo "Cache cleared!"
}

function optimize() {
    echo "Optimizing application..."
    docker compose -f $COMPOSE_FILE exec app php artisan config:cache
    docker compose -f $COMPOSE_FILE exec app php artisan route:cache
    docker compose -f $COMPOSE_FILE exec app php artisan view:cache
    docker compose -f $COMPOSE_FILE exec app php artisan filament:cache-components
    echo "Optimization complete!"
}

function health_check() {
    echo "=== Health Check ==="

    # Application
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/health)
    if [ "$HTTP_CODE" -eq 200 ]; then
        echo "✅ Application: Healthy"
    else
        echo "❌ Application: Unhealthy (HTTP $HTTP_CODE)"
    fi

    # Database
    DB_STATUS=$(docker compose -f $COMPOSE_FILE exec -T mysql mysqladmin ping -h localhost 2>&1)
    if [[ $DB_STATUS == *"mysqld is alive"* ]]; then
        echo "✅ Database: Healthy"
    else
        echo "❌ Database: Unhealthy"
    fi

    # Redis
    REDIS_STATUS=$(docker compose -f $COMPOSE_FILE exec -T redis redis-cli ping 2>&1)
    if [[ $REDIS_STATUS == *"PONG"* ]]; then
        echo "✅ Redis: Healthy"
    else
        echo "❌ Redis: Unhealthy"
    fi

    # Meilisearch
    MEILI_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:7700/health)
    if [ "$MEILI_STATUS" -eq 200 ]; then
        echo "✅ Meilisearch: Healthy"
    else
        echo "❌ Meilisearch: Unhealthy"
    fi

    echo ""
}

function show_help() {
    echo "Production Monitoring and Maintenance Script"
    echo ""
    echo "Usage: ./monitor.sh [command]"
    echo ""
    echo "Commands:"
    echo "  status          Show container status"
    echo "  logs [service]  Show logs for a service (default: app)"
    echo "  resources       Show resource usage"
    echo "  horizon         Show Horizon status"
    echo "  queue           Show queue statistics"
    echo "  cache           Show cache status"
    echo "  health          Run health checks"
    echo "  backup          Create database backup"
    echo "  restart         Restart all services"
    echo "  clear-cache     Clear application cache"
    echo "  optimize        Optimize application"
    echo "  dashboard       Show full dashboard"
    echo "  help            Show this help message"
    echo ""
}

function dashboard() {
    clear
    echo "════════════════════════════════════════════"
    echo "     AlarmStyle Production Dashboard"
    echo "════════════════════════════════════════════"
    echo ""
    health_check
    show_status
    horizon_status
    show_resources
    echo ""
    echo "Run './monitor.sh help' for more commands"
}

# Main
case "${1}" in
    status)
        show_status
        ;;
    logs)
        show_logs "${2:-app}" "${3:-50}"
        ;;
    resources)
        show_resources
        ;;
    horizon)
        horizon_status
        ;;
    queue)
        queue_stats
        ;;
    cache)
        cache_status
        ;;
    health)
        health_check
        ;;
    backup)
        backup_db
        ;;
    restart)
        restart_services
        ;;
    clear-cache)
        clear_cache
        ;;
    optimize)
        optimize
        ;;
    dashboard)
        dashboard
        ;;
    help|--help|-h)
        show_help
        ;;
    *)
        dashboard
        ;;
esac
