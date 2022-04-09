FROM php:7.3-apache
# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql
COPY php.inc /var/www/php.inc
COPY html /var/www/html
COPY pdf /var/www/html/pdf
