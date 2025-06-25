FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip


# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy source code
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Use Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf