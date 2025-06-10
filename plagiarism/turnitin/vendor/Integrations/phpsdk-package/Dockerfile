FROM php:7.1.3

RUN apt-get -y update && \
    apt-get -y install --no-install-recommends \
        zlib1g-dev \
        libxml2-dev \
        libmcrypt-dev \
        libcurl4-openssl-dev \
    && docker-php-ext-install -j"$(nproc)" mcrypt soap zip \
    && apt-get -y clean

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/bin/composer

WORKDIR /root/phpsdk
