FROM php:8.2-fpm

# Install system dependencies
RUN apt update && apt install -y unzip curl git nodejs npm libzip-dev

RUN apt update && apt install -y libpng-dev libjpeg-dev libfreetype6-dev libonig-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www