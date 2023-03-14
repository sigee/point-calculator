FROM php:7.4-fpm

RUN apt-get update && \
    apt-get install -y git && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug-3.1.5

RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-enable xdebug

COPY conf/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN curl -sSL https://github.com/composer/composer/releases/download/2.0.11/composer.phar -o /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer && \
    curl -sSL https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh -o /usr/local/bin/wait-for-it.sh && \
    chmod +x /usr/local/bin/wait-for-it.sh