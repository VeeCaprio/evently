#!/bin/sh

set -eu

cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
    echo "[evently] .env created from .env.example"
fi

if [ ! -d vendor ]; then
    echo "[evently] Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if ! grep -qE '^APP_KEY=base64:' .env; then
    echo "[evently] Generating application key..."
    php artisan key:generate --force
fi

mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

if [ ! -e public/storage ]; then
    php artisan storage:link || true
fi

echo "[evently] Starting PHP-FPM..."

exec "$@"
