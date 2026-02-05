# Stage 1: builder
FROM php:8.5-fpm-alpine AS builder

WORKDIR /var/www/html

# Build deps + php extensions
RUN apk add --no-cache \
    bash curl git mysql-client nodejs npm \
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

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Deps
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY package.json package-lock.json* ./
RUN npm ci

# App
COPY . .
RUN composer dump-autoload --optimize --no-dev \
 && php artisan ziggy:generate resources/js/ziggy.js \
 && npm run build


# Stage 2: runtime
FROM php:8.5-fpm-alpine AS runtime

WORKDIR /var/www/html

# Only runtime libs (без -dev)
RUN apk add --no-cache \
    bash curl mysql-client \
    libpng libjpeg-turbo freetype \
    libzip oniguruma icu-libs libsodium \
  && rm -rf /var/cache/apk/*

# Copy compiled extensions + ini files from builder
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Copy app
COPY --from=builder --chown=www-data:www-data /var/www/html /var/www/html

# Permissions
RUN chmod -R 755 /var/www/html/storage \
 && chmod -R 755 /var/www/html/bootstrap/cache

# Configs
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

EXPOSE 9000
USER www-data
CMD ["php-fpm"]
