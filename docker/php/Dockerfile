FROM php:7.1.5-fpm

COPY ./php.ini /usr/local/etc/php

RUN apt-get update && apt-get install -y libicu-dev

RUN docker-php-ext-install intl
RUN docker-php-ext-install opcache
RUN docker-php-ext-install pdo_mysql
