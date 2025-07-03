FROM php:8.3-fpm-alpine

# Update & install system deps
RUN apk update && apk add --no-cache \
    git \
    zip \
    unzip \
    curl \
    libpng-dev \
    libxml2-dev \
    icu-dev \
    oniguruma-dev \
    bash \
    mysql-client

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql intl mbstring xml

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy Laravel source
COPY ./src /var/www

# Permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Laravel dependencies
RUN composer install --no-dev --optimize-autoloader \
    && php artisan config:clear \
    && php artisan route:clear

FROM nginx:alpine

# Copy built app from backend stage
COPY --from=backend /var/www /var/www

# Copy PHP-FPM binaries
COPY --from=backend /usr/local/etc/php-fpm.d /usr/local/etc/php-fpm.d
COPY --from=backend /usr/local/sbin/php-fpm /usr/local/sbin/php-fpm
COPY --from=backend /usr/local/bin/php /usr/local/bin/php

# Copy nginx config
COPY ./nginx/nginx.conf /etc/nginx/conf.d/default.conf

# Copy entrypoint to start both php-fpm and nginx
COPY ./start.sh /start.sh
RUN chmod +x /start.sh

# Set working directory
WORKDIR /var/www

EXPOSE 80

CMD ["/start.sh"]

