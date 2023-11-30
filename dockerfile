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
        software-properties-common \
&& rm -rf /var/lib/apt/lists/*

# Add Ondrej PHP repository
RUN add-apt-repository ppa:ondrej/php

# Install additional PHP modules
RUN apt-get install -y php7.4-pgsql php7.4-curl php7.4-gd php7.4-intl php7.4-mysql \
                       php7.4-xml php7.4-xmlrpc php7.4-ldap php7.4-zip php7.4-soap \
                       php7.4-mbstring

# Install Apache and other dependencies
RUN apt-get install -y apache2 libapache2-mod-php

# Configure PHP extensions
RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ \
&& docker-php-ext-install -j$(nproc) gd pgsql pdo pdo_pgsql mysqli xml zip intl

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy your Moodle code into the image
COPY . /var/www/html

# Set necessary permissions
RUN mkdir /var/moodledata \
&& chown -R www-data /var/moodledata \
&& chmod -R 777 /var/moodledata \
&& chmod -R 0755 /var/www/html/moodle \
&& chmod -R 777 /var/www/html/moodle

# Expose port 80
EXPOSE 80
