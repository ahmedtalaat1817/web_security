FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    icu-dev libzip-dev oniguruma-dev linux-headers $PHPIZE_DEPS \
    git unzip bash \
    && docker-php-ext-install intl pdo_mysql bcmath pcntl opcache \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
