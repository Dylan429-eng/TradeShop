# Étape 1 : Build assets avec Node
FROM node:18 AS frontend
WORKDIR /app
COPY package*.json vite.config.js ./
COPY resources ./resources
RUN npm install && npm run build

# Étape 2 : PHP + Nginx
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev unzip git curl nginx \
    && docker-php-ext-install pdo pdo_mysql zip bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader

# Copier uniquement les assets générés par Node
COPY --from=frontend /app/public/build ./public/build

COPY ./docker/nginx.conf /etc/nginx/conf.d/default.conf

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache \
    && php artisan config:cache && php artisan route:cache && php artisan view:cache

ENV PORT=10000
EXPOSE 10000

CMD php-fpm -D && nginx -g "daemon off;"
