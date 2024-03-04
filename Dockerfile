FROM php:8.1-cli

# Installer les dépendances nécessaires pour le projet
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql

# Copier l'application dans le conteneur
COPY . /var/www/symfony

# Définir le répertoire de travail
WORKDIR /var/www/symfony

# Utiliser le serveur web PHP intégré pour servir l'application
CMD php -S 0.0.0.0:${PORT:-8080} -t public
