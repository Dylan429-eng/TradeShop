# --------------------------------------
# Étape 1 : Build frontend avec Node/Vite
# --------------------------------------
FROM node:18 AS frontend
WORKDIR /app

COPY package*.json vite.config.js ./
COPY resources ./resources

RUN npm install && npm run build

# --------------------------------------
# Étape 2 : PHP + Laravel (serveur intégré)
# --------------------------------------
FROM php:8.2-cli

# Installer extensions nécessaires
RUN apt-get update && apt-get install -y \
    unzip git curl libzip-dev libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader

COPY --from=frontend /app/public/build ./public/build

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Render impose PORT comme variable d'env
ENV PORT=10000
EXPOSE 10000

# Lancer Laravel via serveur intégré PHP
CMD php -S 0.0.0.0:$PORT -t public
