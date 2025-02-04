FROM php:8.2.5-apache

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt update 
RUN  apt install libpq-dev git zip -y
RUN docker-php-ext-install pdo_pgsql
RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

CMD bash -c "composer --no-interaction update && composer --no-interaction install && php -S 0.0.0.0:80 -t public"

EXPOSE 80