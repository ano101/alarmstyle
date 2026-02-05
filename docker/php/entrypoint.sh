#!/bin/sh
set -e

echo "Running as UID:GID = $(id -u):$(id -g)"

APP_DIR="/var/www/html"

# ----------------------------
# Runtime directories
# ----------------------------
mkdir -p \
  $APP_DIR/storage/logs \
  $APP_DIR/storage/framework/cache/data \
  $APP_DIR/storage/framework/sessions \
  $APP_DIR/storage/framework/views \
  $APP_DIR/storage/app/public \
  $APP_DIR/bootstrap/cache

# Исправляем права только для app-related директорий
# Игнорируем nginx логи (принадлежат nginx контейнеру)
find $APP_DIR/storage -type d -not -path "*/logs/nginx*" -exec chmod 775 {} \; 2>/dev/null || true
find $APP_DIR/storage -type f -not -path "*/logs/nginx*" -exec chmod 664 {} \; 2>/dev/null || true
chmod -R 775 $APP_DIR/bootstrap/cache 2>/dev/null || true

# ----------------------------
# Run main process
# ----------------------------
exec "$@"
