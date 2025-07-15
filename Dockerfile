FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip


# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache to listen on all interfaces
# RUN echo "Listen 0.0.0.0:80" >> /etc/apache2/ports.conf

# Set working directory
WORKDIR /var/www/html

# Copy source code
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Install development dependencies for hot reload
RUN apt-get update && apt-get install -y \
    inotify-tools \
    && rm -rf /var/lib/apt/lists/*

# Use Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Set proper permissions for Laravel storage and cache directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage/framework \
    && chmod -R 775 /var/www/html/storage/logs \
    && chmod -R 775 /var/www/html/storage/app

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Copy development script
COPY docker/start-dev.sh /usr/local/bin/start-dev.sh
RUN chmod +x /usr/local/bin/start-dev.sh

# Set development as default command
CMD ["/usr/local/bin/start-dev.sh"]