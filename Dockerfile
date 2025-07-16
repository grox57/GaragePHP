
# Docker est un système d'exploitation pour conteneurs. De la même manière qu'une machine virtuelle virtualise
# le matériel serveur (càd. qu'il n'est plus nécessaire de le gérer directement), les conteneurs virtualisent
# le système d'exploitation d'un serveur.


# Utiliser une image php officielle avec Apache
FROM php:8.3-apache

# Installer
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Ajouter ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Activer le a2enmod rewrite d'Apache pour les url
RUN a2enmod rewrite

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de dépendances et les installer
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist

# Copier le reste du code de l'appli
COPY . .

# Exécuter le dump de l'autoloader de composer (pr les performances)
RUN composer dump-autoload --optimize

# Changer propriétaire des fichiers afin de donner le droit au serveur d'écrire dans les fichiers (ex: logs)
RUN mkdir -p storage/logs && \
    chown -R www-data:www-data /var/www/html/storage