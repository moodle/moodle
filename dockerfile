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
        graphviz \
        aspell \
        ghostscript \
        clamav \
        software-properties-common

# Add Ondrej PHP repository
RUN add-apt-repository ppa:ondrej/php

# Update package list
RUN apt-get update

# Install PHP 7.4 and additional modules
RUN apt-get install -y php7.4 \
    && docker-php-ext-install -j$(nproc) pgsql pdo pdo_pgsql mysqli xml zip intl

# Install Apache and other dependencies
RUN apt-get install -y apache2 libapache2-mod-php

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy your Moodle code into the image
COPY . /var/www/html

# Set necessary permissions and expose port 80
RUN mkdir /var/moodledata \
    && chown -R www-data /var/moodledata \
    && chmod -R 777 /var/moodledata \
    && chmod -R 0755 /var/www/html/moodle \
    && chmod -R 777 /var/www/html/moodle \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

EXPOSE 80
