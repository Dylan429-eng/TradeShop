# --------------------------------------
# Étape 1 : Build frontend avec Node/Vite
# --------------------------------------
FROM node:18 AS frontend
WORKDIR /app

# Copier seulement ce qui est nécessaire pour npm install
COPY package*.json vite.config.js ./
COPY resources ./resources

# Installer les dépendances Node et build assets
RUN npm install && npm run build

# --------------------------------------
# Étape 2 : PHP + Laravel
# --------------------------------------
FROM php:8.2

# Installer les extensions nécessaires pour Laravel + PostgreSQL
RUN apt-get update && apt-get install -y \
    unzip git curl libzip-dev libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier tout le projet Laravel
COPY . .

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Copier les assets générés par Node
COPY --from=frontend /app/public/build ./public/build

# Permissions pour storage et bootstrap/cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Cacher config/routes/views
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Définir le port exposé pour Render
ENV PORT=10000
EXPOSE 10000

# Lancer Laravel via le serveur PHP intégré (HTTP)
CMD php -S 0.0.0.0:$PORT -t public
