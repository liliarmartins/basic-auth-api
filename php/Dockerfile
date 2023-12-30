FROM php:8.2-fpm

RUN apt-get update

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

RUN install-php-extensions intl pdo pdo_pgsql pgsql zip xml xdebug gd

RUN curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer && /usr/bin/composer self-update 2.5.5

WORKDIR /var/www/symfony

EXPOSE 9003
