# Étape 1 : Image PHP avec FPM
FROM php:8.2-fpm

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    libzip-dev unzip git curl nginx \
    && docker-php-ext-install pdo pdo_mysql zip bcmath

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Créer le dossier de travail
WORKDIR /var/www/html

# Copier le code Laravel
COPY . .

# Installer dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Construire assets frontend si package.json existe
RUN if [ -f package.json ]; then npm install && npm run build; fi

# Copier config Nginx
COPY ./docker/nginx.conf /etc/nginx/conf.d/default.conf

# Mettre les bons droits
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Générer caches Laravel
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# Render définit $PORT automatiquement
ENV PORT=10000

# Exposer le port
EXPOSE 10000

# Démarrer Nginx + PHP-FPM
CMD service php8.2-fpm start && nginx -g 'daemon off;'
