#!/bin/bash
set -e

# Set proper permissions for storage and bootstrap/cache
if [ -d "/var/www/html/storage" ]; then
    chown -R www-data:www-data /var/www/html/storage
    chmod -R 775 /var/www/html/storage
fi

if [ -d "/var/www/html/bootstrap/cache" ]; then
    chown -R www-data:www-data /var/www/html/bootstrap/cache
    chmod -R 775 /var/www/html/bootstrap/cache
fi

# Install composer dependencies if vendor doesn't exist
if [ ! -d "/var/www/html/vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist
fi

# Start PHP-FPM in background
php-fpm -D

# Wait a moment for PHP-FPM to start
sleep 2

# Start Nginx in foreground
nginx -g 'daemon off;'
