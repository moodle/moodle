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

# Install PHP and required extensions
RUN apt-get install -y \
    php7.4 \
    php7.4-pgsql \
    libapache2-mod-php \
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

# Clone Moodle repository
RUN git clone -b MOODLE_400_STABLE --single-branch git://git.moodle.org/moodle.git /opt/moodle

# Copy Moodle to Apache's web directory
RUN cp -R /opt/moodle /var/www/html/

# Create moodledata directory
RUN mkdir /var/moodledata && chown -R www-data /var/moodledata && chmod -R 777 /var/moodledata

# Set permissions for Moodle directory
RUN chmod -R 0755 /var/www/html/moodle

# Configure PostgreSQL
#USER postgres
#RUN echo "CREATE USER moodleuser WITH PASSWORD '123';" | \
 #  echo "CREATE DATABASE moodle WITH OWNER moodleuser;" | \
  #  psql && \
   # echo "host       moodle     moodleuser     0.0.0.0/32       md5" >> /etc/postgresql/14/main/pg_hba.conf && \
    #echo "host       moodle     moodleuser     3.70.247.132/32   md5" >> /etc/postgresql/14/main/pg_hba.conf && \
    #echo "listen_addresses = '*'" >> /etc/postgresql/14/main/postgresql.conf

# Restart PostgreSQL
#USER root
#RUN service postgresql restart

# Restart Apache
RUN service apache2 restart

# Expose ports
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2ctl", "-D", "FOREGROUND"]

