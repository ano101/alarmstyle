#!/bin/bash
# Скрипт для копирования public из Docker образа в хост
set -e

PUBLIC_SRC="/var/www/html/public"
PUBLIC_DST="./public"

# Имя образа (используем переменную окружения или дефолт)
IMAGE_NAME="ghcr.io/${GITHUB_REPOSITORY:-ano101/alarmstyle}:latest"

echo "📦 Copying public assets from Docker image to host..."
echo "   Using image: $IMAGE_NAME"

# Удаляем старую папку если есть
rm -rf "$PUBLIC_DST"

# Создаём временный контейнер из образа
TEMP_CONTAINER=$(docker create "$IMAGE_NAME")

# Копируем public из контейнера (без завершающего слеша, копирует саму папку)
docker cp "$TEMP_CONTAINER:$PUBLIC_SRC" .

# Удаляем временный контейнер
docker rm "$TEMP_CONTAINER" > /dev/null

# Устанавливаем правильные права
chmod -R 755 "$PUBLIC_DST"

echo "✅ Public assets copied successfully"
