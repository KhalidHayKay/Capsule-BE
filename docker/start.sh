#!/bin/sh

# Run Laravel migrations + seed
php artisan migrate --force && php artisan db:seed --force

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground (container stays alive)
nginx -g "daemon off;"
