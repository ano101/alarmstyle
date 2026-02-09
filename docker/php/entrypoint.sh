#!/bin/sh
set -e

APP_DIR="/var/www/html"

mkdir -p \
  $APP_DIR/storage/logs \
  $APP_DIR/storage/framework/cache/data \
  $APP_DIR/storage/framework/sessions \
  $APP_DIR/storage/framework/views \
  $APP_DIR/storage/app/public \
  $APP_DIR/bootstrap/cache

chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache || true

exec "$@"
