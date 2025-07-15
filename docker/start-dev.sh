#!/bin/bash

# Development startup script for Laravel with hot reload

echo "Starting Laravel development server..."

# Function to clear Laravel caches
clear_caches() {
    echo "Clearing Laravel caches..."
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    php artisan view:clear
}

# Function to check if .env file changed
check_env_changes() {
    if [ -f /tmp/env_hash ]; then
        current_hash=$(md5sum /var/www/html/.env | cut -d' ' -f1)
        stored_hash=$(cat /tmp/env_hash)
        
        if [ "$current_hash" != "$stored_hash" ]; then
            echo "Environment file changed, clearing caches..."
            clear_caches
            echo "$current_hash" > /tmp/env_hash
        fi
    else
        md5sum /var/www/html/.env | cut -d' ' -f1 > /tmp/env_hash
    fi
}

# Fix permissions for Laravel storage directories
echo "Fixing Laravel storage permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage/framework
chmod -R 775 /var/www/html/storage/logs
chmod -R 775 /var/www/html/storage/app

# Initial cache clear
clear_caches

# Start Apache in background
apache2-foreground &

# Store Apache PID
APACHE_PID=$!

# Function to monitor file changes
monitor_changes() {
    echo "Monitoring for file changes..."
    
    # Watch for PHP file changes
    inotifywait -m -r /var/www/html/app -e modify,create,delete,move --format '%w%f %e' | while read file event; do
        echo "File change detected: $file ($event)"
        clear_caches
    done &
    
    # Watch for config file changes
    inotifywait -m -r /var/www/html/config -e modify,create,delete,move --format '%w%f %e' | while read file event; do
        echo "Config change detected: $file ($event)"
        clear_caches
    done &
    
    # Watch for .env file changes
    inotifywait -m /var/www/html/.env -e modify --format '%w%f %e' | while read file event; do
        echo "Environment file change detected: $file ($event)"
        clear_caches
    done &
}

# Start monitoring
monitor_changes

# Wait for Apache process
wait $APACHE_PID 