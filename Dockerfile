FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor

RUN docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --optimize-autoloader --no-dev

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

COPY ./docker/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]