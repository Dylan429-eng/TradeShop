# --------------------------------------
# Étape 1 : Build frontend avec Node/Vite
# --------------------------------------
FROM node:18 AS frontend
WORKDIR /app

# Copier uniquement les fichiers nécessaires pour npm install
COPY package*.json vite.config.js ./
COPY resources ./resources

# Installer les dépendances Node et build assets
RUN npm install && npm run build

# --------------------------------------
# Étape 2 : PHP + Laravel
# --------------------------------------
FROM php:8.2-cli

# Installer extensions nécessaires pour Laravel + PostgreSQL
RUN apt-get update && apt-get install -y \
    unzip git curl libzip-dev libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier tout le projet Laravel
COPY . .

# Installer les dépendances PHP (production only)
RUN composer install --no-dev --optimize-autoloader

# Copier les assets générés par Node
COPY --from=frontend /app/public/build ./public/build

# Donner les bonnes permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Cacher la config Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Exposer le port fourni par Railway
EXPOSE $PORT

# Lancer Laravel via serveur PHP intégré
CMD php -S 0.0.0.0:$PORT -t public
