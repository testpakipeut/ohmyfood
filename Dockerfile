FROM php:8.2-apache

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Configuration d'Apache
RUN a2enmod rewrite

# Copie des fichiers du projet
COPY . /var/www/html/

# Copie du script d'initialisation de la base de données
COPY config/init.sql /docker-entrypoint-initdb.d/

# Configuration des permissions
RUN chown -R www-data:www-data /var/www/html

# Exposition du port 80
EXPOSE 80 