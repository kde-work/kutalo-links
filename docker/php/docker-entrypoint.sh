#!/bin/sh
set -e

cd /var/www/html

mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

if [ ! -f vendor/autoload.php ]; then
    echo "vendor не найден, выполняю composer install..."
    if grep -q '^APP_ENV=local' .env 2>/dev/null; then
        COMPOSER_ALLOW_SUPERUSER=1 composer install --prefer-dist --no-interaction
    else
        COMPOSER_ALLOW_SUPERUSER=1 composer install --prefer-dist --no-interaction --no-dev --optimize-autoloader
    fi
fi

chown -R www-data:www-data storage bootstrap/cache
if [ -d vendor ]; then
    chown -R www-data:www-data vendor
fi

exec docker-php-entrypoint "$@"
