FROM moodlehq/moodle-php-apache:8.3-bookworm

RUN a2enmod rewrite expires headers

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY docker/php.ini /usr/local/etc/php/conf.d/aflex.ini
COPY docker/apache-moodle.conf /etc/apache2/conf-available/aflex.conf
RUN a2enconf aflex

WORKDIR /var/www/html
