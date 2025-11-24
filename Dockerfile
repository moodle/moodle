FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    unzip \
    git \
    && docker-php-ext-install -j$(nproc) \
    pgsql \
    gd \
    intl \
    zip \
    soap \
    exif \
    opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure PHP
RUN echo "max_input_vars = 5000" >> /usr/local/etc/php/conf.d/moodle.ini \
    && echo "zend.exception_ignore_args = On" >> /usr/local/etc/php/conf.d/moodle.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/moodle.ini

# Set working directory
WORKDIR /var/www/html

# Create moodledata directory
RUN mkdir -p /var/www/moodledata && chown -R www-data:www-data /var/www/moodledata && chmod 777 /var/www/moodledata
