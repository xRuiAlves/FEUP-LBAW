#!/bin/bash
set -e

env >> /var/www/.env

# Clear cache
php artisan config:clear
php artisan config:cache

php-fpm7.2 -D
nginx -g "daemon off;"

