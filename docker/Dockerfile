FROM php:8.4-fpm-alpine

ARG USER
ARG USER_ID
ARG GROUP_ID

RUN apk add --no-cache \
    git \
    zip \
    unzip \
    curl \
    libzip-dev \
    icu-dev \
    bash \
    shadow \
    su-exec

RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_mysql intl

RUN docker-php-ext-install pcntl

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create user
RUN groupadd --force -g $GROUP_ID $USER
RUN useradd -ms /bin/bash --no-user-group -g $GROUP_ID -u 1337 $USER
RUN usermod -u $USER_ID $USER

WORKDIR /var/www
USER $USER
