# Dockerfile
FROM php:8.4.7-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    nodejs \
    npm \
    supervisor

# PHP Extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy custom php config
COPY ./.docker/php/php.ini /usr/local/etc/php/
COPY ./.docker/php/docker.conf /usr/local/etc/php-fpm.d/docker.conf

# Create app directory
WORKDIR /var/www
