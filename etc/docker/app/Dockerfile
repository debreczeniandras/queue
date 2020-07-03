FROM php:7.4-fpm

ARG PHP_TIMEZONE
ARG PHP_MEMORY_LIMIT
ARG UID

RUN apt-get update && apt-get install -y \
    openssl \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    iproute2 \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        gd \
        intl \
        mysqli \
        pdo \
        pdo_mysql \
        opcache \
        zip

RUN pecl install xdebug && docker-php-ext-enable xdebug

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${PHP_TIMEZONE} /etc/localtime && echo ${PHP_TIMEZONE} > /etc/timezone

# Install composer and put binary into $PATH
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/ \
    && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

ENV COMPOSER_HOME /var/www/.composer/
ENV COMPOSER_MEMORY_LIMIT -1

# Copy xdebug and php config.
COPY conf.d/* /usr/local/etc/php/conf.d/

USER ${UID}

COPY entrypoint.sh /docker-entrypoint.sh

ENTRYPOINT ["/docker-entrypoint.sh"]

WORKDIR /var/www/project
