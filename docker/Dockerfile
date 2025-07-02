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
    mysql-client \
    nodejs \
    npm

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

EXPOSE 8080

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
