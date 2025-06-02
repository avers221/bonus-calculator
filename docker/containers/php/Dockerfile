FROM php:8.4-fpm

ENV TIMEZONE            Europe/Moscow
ENV TZ                  $TIMEZONE

RUN apt-get update && apt-get install -y tzdata && \
    rm /etc/localtime && \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata

RUN apt-get update && apt-get install -y locales wget nano curl supervisor libpq-dev libzip-dev zip unzip git libonig-dev libxml2-dev libcurl4-openssl-dev libpng-dev libc-client-dev libkrb5-dev libldap2-dev libedit-dev build-essential libtool libxslt-dev libmcrypt-dev\
        && CFLAGS="-I/usr/src/php"
RUN pecl install imap && \
    docker-php-ext-enable imap

RUN echo 'ru_RU.UTF-8 UTF-8' >> /etc/locale.gen && \
    locale-gen
ENV LANG ru_RU.UTF-8
ENV LANGUAGE ru_RU:en
ENV LC_ALL ru_RU.UTF-8

RUN  docker-php-ext-install pdo \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-install xsl \
    && docker-php-ext-install gd \
    && docker-php-ext-install curl \
    && docker-php-ext-install zip \
    && docker-php-ext-install opcache


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl channel-update pecl.php.net
RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

WORKDIR /var/www/html