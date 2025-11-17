FROM php:8.2-apache

# Instala extensões necessárias do Laravel
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Habilita mod_rewrite
RUN a2enmod rewrite

# Copia código para o container
COPY . /var/www/html

# Define diretório padrão
WORKDIR /var/www/html

# Instala composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instala dependências PHP
RUN composer install --no-dev --optimize-autoloader

# Define permissões corretas para storage e cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expor porta do Apache
EXPOSE 80
