FROM php:7.1.8-apache
MAINTAINER Riyas Kp

COPY . /var/www/html/transcoder
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN apt-get update && apt-get install -y \
        libmcrypt-dev \
    && docker-php-ext-install mcrypt bcmath mbstring && apt-get install -y lsb-release \
    && echo "deb http://deb.debian.org/debian $(lsb_release -sc)-backports main" > /etc/apt/sources.list.d/backports.list \
    && apt-get update && apt-get install -y ffmpeg \
    && apt-get update \
    && apt-get  install -y python-setuptools \
    && easy_install supervisor \
    && apt-get clean && chown -R www-data:www-data /var/www/html/transcoder \
    && a2enmod rewrite && service apache2 restart && supervisord -c /var/www/html/transcoder/supervisord.conf


