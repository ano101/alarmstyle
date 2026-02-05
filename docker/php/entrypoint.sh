#!/bin/sh
set -e

echo "Running as UID:GID = $(id -u):$(id -g)"

APP_DIR="/var/www/html"
PUBLIC_DIR="$APP_DIR/public"
PUBLIC_VOLUME_MARKER="$PUBLIC_DIR/.initialized"

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

chmod -R 775 \
  $APP_DIR/storage \
  $APP_DIR/bootstrap/cache || true

# ----------------------------
# Init public-data volume ONCE
# ----------------------------
if [ -d "$PUBLIC_DIR" ] && [ ! -f "$PUBLIC_VOLUME_MARKER" ]; then
  echo "🟡 public-data not initialized, populating from image..."

  # временная директория с public из image
  TMP_PUBLIC="/tmp/image-public"

  mkdir -p "$TMP_PUBLIC"

  # копируем public ИЗ IMAGE (слой container FS)
  cp -a "$PUBLIC_DIR/." "$TMP_PUBLIC/" || true

  # чистим volume public
  rm -rf "$PUBLIC_DIR"/*

  # переносим данные в volume
  cp -a "$TMP_PUBLIC/." "$PUBLIC_DIR/"

  # маркер инициализации
  touch "$PUBLIC_VOLUME_MARKER"

  echo "✅ public-data initialized"
else
  echo "🟢 public-data already initialized"
fi

# ----------------------------
# Run main process
# ----------------------------
exec "$@"
