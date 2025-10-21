FROM php:8.2-apache

# Installer dépendances système et extensions PHP
RUN apt-get update && apt-get install -y \
    unzip libzip-dev git && \
    docker-php-ext-install pdo pdo_mysql

# Activer mod_rewrite
RUN a2enmod rewrite

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier tous les fichiers du backend dans le conteneur
COPY . .

# Installer Composer (à partir de l'image officielle)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Mettre à jour composer.lock et installer les dépendances
RUN composer update --no-interaction --no-dev --prefer-dist

# Configurer Apache pour pointer vers /public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN echo '<Directory /var/www/html/public>\nAllowOverride All\nRequire all granted\nFallbackResource /index.php\n</Directory>' >> /etc/apache2/apache2.conf

EXPOSE 80
CMD ["apache2-foreground"]
