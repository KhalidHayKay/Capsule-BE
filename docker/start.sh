#!/bin/sh

# Exit immediately on error
set -e

echo "Running migrations against DB..."
php artisan migrate --force

echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx..."
nginx -g "daemon off;"
