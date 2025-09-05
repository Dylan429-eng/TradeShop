# -------------------------------
# Dockerfile pour Laravel + PHP 8.2 + PostgreSQL
# -------------------------------

# 1️⃣ Base PHP avec FPM
FROM php:8.2-fpm

# 2️⃣ Installer les dépendances système
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    npm \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip \
    && rm -rf /var/lib/apt/lists/*

# 3️⃣ Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4️⃣ Définir le répertoire de travail
WORKDIR /var/www/html

# 5️⃣ Copier le projet Laravel
COPY . .

# 6️⃣ Installer les dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# 7️⃣ Installer Node.js et builder les assets si nécessaire
RUN if [ -f package.json ]; then npm install && npm run build; fi


# 9️⃣ Permissions pour storage et cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

#  🔟 Exposer le port pour Render
EXPOSE 9000

# 1️⃣1️⃣ Commande pour démarrer Laravel via PHP-FPM
CMD php artisan config:cache && php artisan route:cache && php artisan view:cache && php-fpm
