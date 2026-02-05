# Решение проблемы с пересозданием базы данных при деплое

## Проблема

При каждом деплое база данных создавалась заново, все данные терялись.

## Причины

1. **`docker compose up -d` пересоздаёт контейнеры** если изменился образ
2. **Volumes НЕ удаляются автоматически** при `docker compose up -d`, НО:
   - Если использовать `docker compose down`, volumes удаляются (если не указан флаг `-v`)
   - Если использовать `docker compose down -v`, volumes ГАРАНТИРОВАННО удаляются

## Решение в deploy.sh

### 1. Проверка существующих volumes (строка 40-47)
```bash
EXISTING_VOLUMES=$(docker volume ls --format "{{.Name}}" | grep -E "(mysql-data|redis-data|meilisearch-data)" || true)
```
Показывает, какие volumes существуют перед деплоем.

### 2. Определение первого деплоя через проверку таблиц (строка 55-61)
```bash
TABLE_COUNT=$(docker compose -f compose.prod.yaml exec -T app php artisan tinker --execute="echo \DB::table('migrations')->count();" 2>/dev/null || echo "0")
```
Вместо проверки volumes, проверяем реальное состояние базы данных.

### 3. Условный запуск сидеров (строка 68-71)
```bash
if [ "$IS_FIRST_DEPLOY" = true ]; then
    echo -e "${GREEN}🌱 Running seeders...${NC}"
    docker compose -f compose.prod.yaml exec -T app php artisan db:seed --force
fi
```
Сидеры запускаются **ТОЛЬКО** при первом деплое.

## Важные правила

### ✅ Правильно
```bash
# Для деплоя
docker compose -f compose.prod.yaml up -d

# Для остановки без удаления volumes
docker compose -f compose.prod.yaml stop

# Для перезапуска
docker compose -f compose.prod.yaml restart
```

### ❌ НИКОГДА не используйте
```bash
# Это удалит volumes!
docker compose -f compose.prod.yaml down -v

# Это может удалить volumes в зависимости от настроек
docker compose -f compose.prod.yaml down
```

## Диагностика

### Проверить существующие volumes:
```bash
docker volume ls | grep -E "(mysql|redis|meilisearch)"
```

### Проверить содержимое MySQL volume:
```bash
docker compose -f compose.prod.yaml exec mysql ls -la /var/lib/mysql
```

### Проверить таблицы в базе:
```bash
docker compose -f compose.prod.yaml exec app php artisan tinker --execute="echo \DB::table('migrations')->count();"
```

### Проверить данные в базе:
```bash
docker compose -f compose.prod.yaml exec mysql mysql -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} -e "SHOW TABLES;"
```

## Если база всё равно пересоздаётся

1. **Проверьте, не используется ли `docker compose down`** в каких-то скриптах или вручную
2. **Проверьте логи деплоя** - в них должно быть сообщение "Found existing volumes"
3. **Проверьте название проекта** - Docker Compose создаёт volumes с префиксом имени проекта
4. **Убедитесь, что volumes определены в compose.prod.yaml** (строки 198-202)

## Структура volumes в compose.prod.yaml

```yaml
volumes:
  mysql-data:
    driver: local
  redis-data:
    driver: local
  meilisearch-data:
    driver: local
```

Эти volumes **НЕ УДАЛЯЮТСЯ** при `docker compose up -d` или `docker compose restart`.

## Что делать, если данные потеряны

### Восстановление из бэкапа (если есть):
```bash
# Импорт SQL дампа
docker compose -f compose.prod.yaml exec -T mysql mysql -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} < backup.sql
```

### Создание нового пользователя:
```bash
docker compose -f compose.prod.yaml exec app php artisan make:filament-user
```

### Повторный запуск сидеров:
```bash
docker compose -f compose.prod.yaml exec app php artisan db:seed --force
```

## Рекомендации

1. **Настройте автоматические бэкапы MySQL**
2. **Используйте внешний volume** для критичных данных
3. **Никогда не используйте `docker compose down`** без явной необходимости
4. **Мониторьте размер volumes** для предотвращения проблем с диском
