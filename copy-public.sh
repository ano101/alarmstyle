#!/bin/bash
# Скрипт для копирования public из Docker образа в хост
set -e

PUBLIC_SRC="/var/www/html/public"
PUBLIC_DST="./public"

echo "📦 Copying public assets from Docker image to host..."

# Удаляем старую папку если есть
rm -rf "$PUBLIC_DST"

# Создаём временный контейнер из образа
TEMP_CONTAINER=$(docker create ghcr.io/${GITHUB_REPOSITORY:-alarmstyle}:latest)

# Копируем public из контейнера (без завершающего слеша, копирует саму папку)
docker cp "$TEMP_CONTAINER:$PUBLIC_SRC" .

# Удаляем временный контейнер
docker rm "$TEMP_CONTAINER" > /dev/null

# Устанавливаем правильные права
chmod -R 755 "$PUBLIC_DST"

echo "✅ Public assets copied successfully"
