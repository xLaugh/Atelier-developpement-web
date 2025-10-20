FROM php:8.2-apache

# Installe l’extension PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Active le module de réécriture d’URL
RUN a2enmod rewrite

# Copie le code du backend dans le conteneur
COPY . /var/www/html

# Change le DocumentRoot pour Apache
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-enabled/000-default.conf

# Autorise les .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/sites-enabled/000-default.conf

# Définit le répertoire de travail
WORKDIR /var/www/html/public

# Corrige les permissions
RUN chown -R www-data:www-data /var/www/html
