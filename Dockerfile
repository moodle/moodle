# Use the official Moodle image as a base image
FROM moodlehq/moodle-php-apache:7.4

# Set up path to Moodle code
ENV MOODLE_DOCKER_WWWROOT /var/www/html

# Choose a db server
ENV MOODLE_DOCKER_DB pgsql

# Copy the existing Moodle code into the container
COPY www/html/ .

# Copy the moodledata directory
COPY moodledata /var/moodledata

# Set up Apache configuration
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Give ownership to www-data user and group
RUN chown -R www-data:www-data /var/www/html
RUN chown -R www-data:www-data /var/moodledata

# Set read and execute permissions for the web server user
RUN chmod -R 755 /var/www/html
RUN chmod -R 755 /var/moodledata

# Set up the required Moodle directories
RUN mkdir -p /var/moodledata/cache /var/moodledata/localcache
RUN chown -R www-data:www-data /var/moodledata/cache /var/moodledata/localcache

# Expose ports
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
