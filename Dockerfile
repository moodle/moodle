FROM php:8.4-fpm as php-base

RUN apt-get update && apt-get install -y \
    $PHPIZE_DEPS \
    libcurl4-openssl-dev \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libxml2-dev \
    libonig-dev \
    libsodium-dev \
    libpq-dev \
 && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install \
    curl \
    intl \
    mbstring \
    zip \
    gd \
    soap \
    sodium \
    pgsql \
    pdo_pgsql \
    exif 

WORKDIR /var/www/moodle

FROM php-base as builder

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/moodle

COPY . . 

RUN composer install --no-dev --classmap-authoritative

FROM php-base as php-release

WORKDIR /var/www/moodle

COPY --from=builder /var/www/moodle /var/www/moodle

RUN chown -R www-data:www-data /var/www/moodle

FROM nginx:alpine as nginx-release

WORKDIR /var/www/moodle

COPY --from=builder /var/www/moodle /var/www/moodle
