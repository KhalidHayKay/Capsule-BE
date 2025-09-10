FROM php:8.4-fpm-alpine

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
    nginx \
    supervisor

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql intl mbstring xml

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files (layer caching)
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy the rest of the source
COPY . .

# Permissions
RUN chown -R www-data:www-data . && chmod -R 755 .

# Artisan optimizations
RUN composer run-script post-autoload-dump \
    && php artisan config:clear \
    && php artisan route:clear

# Copy nginx config
COPY docker/nginx/production.conf /etc/nginx/nginx.conf

# Copy entrypoint to start both php-fpm and nginx
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
