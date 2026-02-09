# ===============================
# Node build stage
# ===============================
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build


# ===============================
# Composer build stage
# ===============================
FROM composer:2 AS composer-builder

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-scripts

COPY . .
RUN composer dump-autoload --optimize


# ===============================
# App runtime
# ===============================
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# System deps
RUN apk add --no-cache \
    bash \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    supervisor \
    $PHPIZE_DEPS \
    && docker-php-ext-install \
        pdo_mysql \
        intl \
        zip \
        opcache \
    && apk del $PHPIZE_DEPS

# PHP config
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Copy app
COPY --from=composer-builder /app /var/www/html
COPY --from=node-builder /app/public /var/www/html/public
COPY --from=node-builder /app/resources /var/www/html/resources

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

USER www-data

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
