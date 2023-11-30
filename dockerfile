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
 
# Install PHP 7.3 and required extensions
RUN apt-get install -y \
    php7.3 \
    php7.3-pgsql \
    libapache2-mod-php7.3 \
    graphviz \
    aspell \
    ghostscript \
    clamav \
    php7.3-pspell \
    php7.3-curl \
    php7.3-gd \
    php7.3-intl \
    php7.3-mysql \
    php7.3-xml \
    php7.3-xmlrpc \
    php7.3-ldap \
    php7.3-zip \
    php7.3-soap \
    php7.3-mbstring \
&& rm -rf /var/lib/apt/lists/*
 
# Enable Apache modules
RUN a2enmod rewrite
 
 
# Copy Moodle to Apache's web directory
COPY . /var/www/html
 
# Create moodledata directory
RUN mkdir /var/moodledata && chown -R www-data /var/moodledata && chmod -R 777 /var/moodledata
 
# Set permissions for Moodle directory
RUN chmod -R 0755 /var/www/html/moodle
 
# Fix deprecated string syntax
RUN find /var/www/html/moodle -type f -name '*.php' -exec sed -i 's/\${\([^}]*\)}/{$\1}/g' {} +
 
 
 
# Restart Apache
RUN service apache2 restart
 
# Expose ports
EXPOSE 80
 
# Start Apache in the foreground
CMD ["apache2ctl", "-D", "FOREGROUND"]
