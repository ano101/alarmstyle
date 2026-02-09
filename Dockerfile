# ===============================
# Stage 1: builder
# ===============================
FROM php:8.5-fpm-alpine AS builder

WORKDIR /var/www/html

# ----------------------------
# Build deps + PHP extensions
# ----------------------------
RUN apk add --no-cache \
    bash curl git \
    nodejs npm \
    mysql-client \
    $PHPIZE_DEPS \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    libzip-dev oniguruma-dev icu-dev zlib-dev libsodium-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) \
      pdo_mysql mysqli mbstring exif pcntl bcmath gd zip intl sodium \
  && pecl install redis \
  && docker-php-ext-enable redis \
  && apk del $PHPIZE_DEPS \
  && rm -rf /var/cache/apk/*

# ----------------------------
# Composer
# ----------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-scripts

# ----------------------------
# Node deps
# ----------------------------
COPY package.json package-lock.json ./
RUN npm ci

# ----------------------------
# App sources
# ----------------------------
COPY . .

# ----------------------------
# Build-time env (НЕ runtime)
# ----------------------------
ENV APP_ENV=production
ENV APP_KEY=base64:temporary_build_key_only_do_not_use_in_prod=

# ----------------------------
# Build steps
# ----------------------------
RUN composer dump-autoload --optimize \
 && php artisan ziggy:generate resources/js/ziggy.js \
 && php artisan vendor:publish --tag=livewire:assets --force \
 && php artisan filament:assets \
 && npm run build

# ❗ НИЧЕГО не чистим — runtime сам лёгкий


# ===============================
# Stage 2: runtime
# ===============================
FROM php:8.5-fpm-alpine AS runtime

WORKDIR /var/www/html

# ----------------------------
# Runtime deps ONLY
# ----------------------------
RUN apk add --no-cache \
    bash curl \
    mysql-client \
    nodejs \
    libpng libjpeg-turbo freetype \
    libzip oniguruma icu-libs libsodium \
  && rm -rf /var/cache/apk/*

# ----------------------------
# PHP extensions from builder
# ----------------------------
COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# ----------------------------
# Application (FULLY baked)
# ----------------------------
COPY --from=builder /var/www/html /var/www/html

# ----------------------------
# PHP configs
# ----------------------------
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# ----------------------------
# Entrypoint
# ----------------------------
COPY docker/php/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# ----------------------------
# Permissions
# ----------------------------
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 775 storage bootstrap/cache

USER www-data

EXPOSE 9000

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
