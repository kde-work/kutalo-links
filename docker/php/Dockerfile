FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    curl \
    git \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-install \
        intl \
        mbstring \
        opcache \
        pdo_mysql \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY backend/ ./

RUN composer dump-autoload --optimize --no-dev \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint-links.sh
RUN chmod +x /usr/local/bin/docker-entrypoint-links.sh

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint-links.sh"]
CMD ["php-fpm"]
