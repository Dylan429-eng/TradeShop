# --------------------------------------
# Étape 1 : Build frontend avec Node/Vite
# --------------------------------------
FROM node:18 AS frontend
WORKDIR /app

COPY package*.json vite.config.js ./
COPY resources ./resources

RUN npm install && npm run build

# --------------------------------------
# Étape 2 : PHP + Laravel + Nginx
# --------------------------------------
FROM php:8.2-fpm

# Installer les extensions nécessaires
RUN apt-get update && apt-get install -y \
    nginx git unzip libzip-dev libpq-dev curl \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier le projet Laravel
COPY . .

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Copier les assets générés par Node
COPY --from=frontend /app/public/build ./public/build

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Cacher config/routes/views
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Config Nginx pour Laravel
COPY ./docker/nginx.conf /etc/nginx/conf.d/default.conf

# Exposer le port
EXPOSE 80

# Lancer PHP-FPM + Nginx
CMD ["sh", "-c", "php-fpm -R && nginx -g 'daemon off;'"]
