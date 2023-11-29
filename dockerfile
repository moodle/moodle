# Use an official PHP runtime as a parent images
FROM php:7.2-apache

# Set the working directory in the container to /var/www/html
WORKDIR /var/www/html

# Copy the current directory contents into the container at /var/www/html
COPY . /var/www/html

# Install any needed packages specified in requirements.txt
RUN apt-get update && apt-get install -y \
   libpng-dev \
   libjpeg-dev \
   libfreetype6-dev \
   libzip-dev \
   libonig-dev \
   libxml2-dev \
   libmcrypt-dev \
   libcurl4-openssl-dev \
   libssl-dev \
   libicu-dev \
   libltdl-dev \
   libxslt-dev \
   libgd-dev \
   libreadline-dev \
   libxslt1-dev \
   && docker-php-ext-install -j$(nproc) iconv pdo_mysql mbstring zip gd xml curl json intl \
   && docker-php-ext-configure gd --with-freetype --with-jpeg \
   && docker-php-ext-install -j$(nproc) gd

# Make port 80 available to the world outside this container
EXPOSE 80

# Run the application when the container launches
CMD ["apache2-foreground"]
