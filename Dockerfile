FROM moodlehq/moodle-php-apache:8.3

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

EXPOSE 80
