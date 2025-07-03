#!/bin/sh

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground (container stays alive)
nginx -g "daemon off;"
