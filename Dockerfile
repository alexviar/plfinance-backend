# Estágio de construção
FROM php:8.2-fpm AS builder

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && docker-php-ext-configure gd

# Instalar Node.js 18.x
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www

# Copiar archivos de dependencias
COPY composer.json composer.lock package.json package-lock.json ./

# Instalar dependencias PHP
RUN composer install --no-interaction --no-scripts --no-autoloader

# Instalar dependencias Node.js
RUN npm install

# Copiar todo el proyecto
COPY . .

# Generar autoload y optimizar
RUN composer dump-autoload --optimize \
    && composer install --no-interaction \
    && npm run build

# Establecer permisos
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Estágio final
FROM php:8.2-fpm

# Copiar desde el builder
COPY --from=builder /var/www /var/www

# Instalar dependencias runtime
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www