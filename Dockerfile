FROM composer:2.2.5 as composer

FROM php:8.1-fpm as base

RUN apt update --no-install-recommends && apt install -y \
    git \
    openssh-client \
    unzip && \
    rm -r /var/lib/apt/lists/*

RUN pecl install pcov && \
    docker-php-ext-enable pcov

RUN docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-install pdo_mysql opcache bcmath && \
    docker-php-ext-enable opcache bcmath

WORKDIR /src

# Script that will wait on the availability of a host and TCP port
# for synchronizing the spin-up of linked docker containers in CI (https://github.com/vishnubob/wait-for-it)
COPY contrib/wait-for-it.sh /usr/local/bin/

COPY contrib/php.ini $PHP_INI_DIR/conf.d/php.ini

COPY composer.json composer.lock ./
ENV PATH="$PATH:/src/vendor/bin"
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY composer.* ./
RUN composer install --no-scripts --no-autoloader --no-interaction --no-dev

FROM base as prod
COPY . ./
RUN composer dump-autoload --optimize

FROM base as dev
COPY . ./
RUN composer install --no-scripts --no-autoloader --no-interaction --dev && \
    composer dump-autoload --optimize
