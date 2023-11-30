# Use the official Ubuntu 20.04 image
FROM ubuntu:20.04
 
# Install necessary packages
RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y \
    apache2 \
    git \
    software-properties-common \
    postgresql \
    postgresql-contrib \
    && rm -rf /var/lib/apt/lists/*
 
# Add PHP repository
RUN add-apt-repository ppa:ondrej/php && apt-get update
 
# Install PHP 7.4 and required extensions
RUN apt-get install -y \
    php7.4 \
    php7.4-pgsql \
    libapache2-mod-php7.4 \
    graphviz \
    aspell \
    ghostscript \
    clamav \
    php7.4-pspell \
    php7.4-curl \
    php7.4-gd \
    php7.4-intl \
    php7.4-mysql \
    php7.4-xml \
    php7.4-xmlrpc \
    php7.4-ldap \
    php7.4-zip \
    php7.4-soap \
    php7.4-mbstring \
    && rm -rf /var/lib/apt/lists/*
 
# Enable Apache modules
RUN a2enmod rewrite
 
# Copy everything to /var/www/html/moodle
COPY . /var/www/html/moodle
 
# Create moodledata directory
RUN mkdir /var/moodledata && chown -R www-data /var/moodledata && chmod -R 777 /var/moodledata
 
# Set permissions for Moodle directory
RUN chmod -R 0755 /var/www/html/moodle
 
# Fix deprecated string syntax
RUN find /var/www/html/moodle -type f -name '*.php' -exec sed -i 's/\${\([^}]*\)}/{$\1}/g' {} +
 
# Enable PHP error reporting and display errors for debugging
RUN echo "display_errors = On" >> /etc/php/7.4/apache2/php.ini
RUN echo "error_reporting = E_ALL" >> /etc/php/7.4/apache2/php.ini
 
# Restart Apache
RUN service apache2 restart
 
# Expose ports
EXPOSE 80
 
# Start Apache in the foreground
CMD ["apache2ctl", "-D", "FOREGROUND"]
