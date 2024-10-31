FROM php:fpm

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libxpm-dev \
    libzip-dev \
    zlib1g-dev \
    libonig-dev \
    && docker-php-ext-install pdo pdo_mysql gd mbstring zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer require mpdf/mpdf