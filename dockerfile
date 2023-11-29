[5:11 PM] Daisy Jabbour
FROM php:7.4-apache
 
# Install necessary packages

RUN apt-get update && apt-get install -y \

  unzip \

  rsync \

  postgresql-client
 
# Install necessary PHP extensions

RUN docker-php-ext-install pdo_pgsql
 
# Download and extract Moodle

WORKDIR /var/www/html

RUN wget https://download.moodle.org/download.php/direct/stable310/moodle-latest-310.tgz && \

  tar -xvf moodle-latest-310.tgz && \

  rsync -av --progress moodle/* . && \

  rm -rf moodle moodle-latest-310.tgz
 
# Expose port 80

EXPOSE 80
