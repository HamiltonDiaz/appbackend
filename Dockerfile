FROM php:8.2-cli

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-install pdo_mysql mbstring zip gd

# Instalar Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Configuración del directorio de trabajo
WORKDIR /var/www/html