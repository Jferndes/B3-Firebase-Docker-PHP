FROM php:8.3-apache

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

WORKDIR /var/www

RUN apt-get update

RUN apt-get install -y --no-install-recommends apt-utils g++ libonig-dev libzip-dev zip unzip git

RUN curl -sS https://getcomposer.org/installer | php -- --disable-tls && mv composer.phar /usr/local/bin/composer

RUN docker-php-ext-install mbstring zip