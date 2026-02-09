# Partir de l'image officielle de PHP comme base
# https://hub.docker.com/_/php
FROM php:8.2-apache

# Installer les dépendances Composer
RUN apt-get update && apt-get install -y unzip git \
    && rm -rf /var/lib/apt/lists/*
# curl déjà présent ?

# Installer Composer (nécessite curl, git et unzip)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installer et activer XDebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Activer le module de réécriture d'URL
RUN a2enmod rewrite

# Ajouter configuration PHP custom
COPY custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini

# # Installer composer
# COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Désactiver le vhost par défaut
RUN a2dissite 000-default.conf

# Définir et activer notre vhost custom
COPY site.conf /etc/apache2/sites-available/site.conf
RUN a2ensite site

# S'assurer de la propriété des fichiers dans l'instance
# RUN chown -R root:www-data /var/www
