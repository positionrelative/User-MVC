FROM php:8.4-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN a2enmod rewrite

COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint.sh
COPY docker/migrate.php /var/www/html/docker/migrate.php
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
