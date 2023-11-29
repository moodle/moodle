# Use PHP with Apache as the base image
FROM php:7.4-apache
 
# Install system dependencies
RUN apt-get update && apt-get install -y \
        libpng-dev \
        libjpeg-dev \
        libpq-dev \
        libxml2-dev \
        libzip-dev \
        unzip \
        curl \
&& rm -rf /var/lib/apt/lists/*
 
# Configure PHP extensions
RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ \
&& docker-php-ext-install -j$(nproc) gd pgsql pdo pdo_pgsql mysqli xml zip intl
 
# Enable Apache mod_rewrite
RUN a2enmod rewrite
 
# Copy your Moodle code into the image
COPY . /var/www/html
 
# Set necessary permissions
RUN chown -R www-data:www-data /var/www/html \
&& chmod -R 755 /var/www/html
 
# Expose port 80
EXPOSE 80
