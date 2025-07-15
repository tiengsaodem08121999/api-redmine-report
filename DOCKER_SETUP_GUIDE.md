# 🐳 Docker Setup Guide - Laravel Redmine API

## 📋 Prerequisites

- Docker và Docker Compose đã cài đặt
- Git đã cài đặt
- Port 8081 và 3309 available trên máy

## 🚀 Quick Start

### 1. Clone và Setup Project
```bash
# Clone project (nếu chưa có)
git clone <your-repo-url>
cd api-redmine-report

# Đảm bảo file .env tồn tại
ls -la .env
```

### 2. Chạy Development Environment
```bash
# Cách 1: Sử dụng script helper (khuyến nghị)
./dev.sh

# Cách 2: Chạy trực tiếp
docker compose -f docker-compose.dev.yml up -d
```

### 3. Cài đặt Dependencies
```bash
# Cài composer dependencies
docker compose -f docker-compose.dev.yml exec app composer install

# Generate Laravel key
docker compose -f docker-compose.dev.yml exec app php artisan key:generate

# Chạy migrations
docker compose -f docker-compose.dev.yml exec app php artisan migrate

# Chạy seeders
docker compose -f docker-compose.dev.yml exec app php artisan db:seed
```

### 4. Truy cập ứng dụng
- **URL**: http://localhost:8081
- **Database**: localhost:3309
- **Admin Login**: (check AdminUserSeeder để biết credentials)

## 📁 File Structure

```
api-redmine-report/
├── docker-compose.dev.yml      # Development environment
├── docker-compose.yml          # Production environment  
├── Dockerfile                  # Container configuration
├── 000-default.conf           # Apache virtual host
├── docker/
│   └── start-dev.sh           # Development startup script
├── dev.sh                     # Helper script
└── .env                       # Environment variables
```

## 🔧 Configuration Files

### Dockerfile
```dockerfile
FROM php:8.2-apache

# Install dependencies
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

# Install development tools
RUN apt-get update && apt-get install -y \
    inotify-tools \
    && rm -rf /var/lib/apt/lists/*

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set Laravel permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage/framework \
    && chmod -R 775 /var/www/html/storage/logs \
    && chmod -R 775 /var/www/html/storage/app

# Copy Apache config
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Copy development script
COPY docker/start-dev.sh /usr/local/bin/start-dev.sh
RUN chmod +x /usr/local/bin/start-dev.sh

# Set development as default command
CMD ["/usr/local/bin/start-dev.sh"]
```

### docker-compose.dev.yml
```yaml
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: redmine-app-dev
    ports:
      - "0.0.0.0:8081:80"
    volumes:
      - .:/var/www/html
      - ./.env:/var/www/html/.env
    environment:
      - APP_ENV=local
      - APP_DEBUG=true
      - DB_HOST=mysql
      - DB_DATABASE=redmine-api
      - DB_USERNAME=root
      - DB_PASSWORD=123456
    depends_on:
      - mysql
    networks:
      - laravel
    stdin_open: true
    tty: true

  mysql:
    image: mysql:8
    container_name: redmine-db-dev
    ports:
      - "3309:3306"
    environment:
      MYSQL_ROOT_PASSWORD: 123456
      MYSQL_DATABASE: redmine-api
    volumes:
      - dbdata:/var/lib/mysql
    networks:
      - laravel

volumes:
  dbdata:

networks:
  laravel:
    driver: bridge
```

### 000-default.conf
```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
        Options Indexes FollowSymLinks
    </Directory>

    # Enable access from all hosts
    <Directory />
        Require all granted
    </Directory>
</VirtualHost>
```

## 🛠️ Development Commands

### Start/Stop
```bash
# Start development environment
./dev.sh
# hoặc
docker compose -f docker-compose.dev.yml up -d

# Stop containers
docker compose -f docker-compose.dev.yml down

# View logs
docker compose -f docker-compose.dev.yml logs -f app
```

### Laravel Commands
```bash
# Artisan commands
docker compose -f docker-compose.dev.yml exec app php artisan migrate
docker compose -f docker-compose.dev.yml exec app php artisan db:seed
docker compose -f docker-compose.dev.yml exec app php artisan cache:clear

# Composer commands
docker compose -f docker-compose.dev.yml exec app composer install
docker compose -f docker-compose.dev.yml exec app composer update
```

### Database
```bash
# Access MySQL
docker compose -f docker-compose.dev.yml exec mysql mysql -u root -p123456

# Backup database
docker compose -f docker-compose.dev.yml exec mysql mysqldump -u root -p123456 redmine-api > backup.sql
```

## 🔄 Hot Reload Features

### Automatic Features
- ✅ **Code Changes**: Tự động clear cache khi thay đổi file trong `app/`, `config/`
- ✅ **Environment Changes**: Tự động reload khi thay đổi `.env`
- ✅ **File Monitoring**: Monitor real-time với inotify-tools

### Manual Cache Clear
```bash
docker compose -f docker-compose.dev.yml exec app php artisan config:clear
docker compose -f docker-compose.dev.yml exec app php artisan cache:clear
docker compose -f docker-compose.dev.yml exec app php artisan route:clear
docker compose -f docker-compose.dev.yml exec app php artisan view:clear
```

## 🐛 Troubleshooting

### Permission Issues
```bash
# Fix storage permissions
docker compose -f docker-compose.dev.yml exec app chown -R www-data:www-data /var/www/html/storage
docker compose -f docker-compose.dev.yml exec app chmod -R 775 /var/www/html/storage
```

### Port Conflicts
```bash
# Check what's using port 8081
sudo lsof -i :8081

# Stop conflicting services
sudo systemctl stop apache2
sudo systemctl stop nginx
```

### Database Issues
```bash
# Restart MySQL container
docker compose -f docker-compose.dev.yml restart mysql

# Check MySQL logs
docker compose -f docker-compose.dev.yml logs mysql
```

### Rebuild Everything
```bash
# Complete rebuild
docker compose -f docker-compose.dev.yml down -v
docker compose -f docker-compose.dev.yml build --no-cache
docker compose -f docker-compose.dev.yml up -d
```

## 📊 Environment Variables

### Required .env Variables
```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:your-key-here
APP_DEBUG=true
APP_URL=http://localhost:8081/

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=redmine-api
DB_USERNAME=root
DB_PASSWORD=123456

REDMINE_API_URL=https://tools.splus-software.com/redmine
REDMINE_API_KEY=your-api-key
REDMINE_PROJECT=s7-ec-cube
```

## 🚀 Production Setup

### Switch to Production
```bash
# Use production compose file
docker compose up -d

# Build for production
docker compose build --no-cache
```

## 📝 Notes

- **Port Mapping**: 8081:80 (app), 3309:3306 (database)
- **Hot Reload**: Enabled by default in development
- **Permissions**: Automatically fixed in Dockerfile
- **Database**: MySQL 8 with persistent volume
- **PHP Version**: 8.2 with Apache

## 🎯 Quick Reference

| Command | Description |
|---------|-------------|
| `./dev.sh` | Start development environment |
| `docker compose -f docker-compose.dev.yml logs -f app` | View app logs |
| `docker compose -f docker-compose.dev.yml exec app php artisan migrate` | Run migrations |
| `docker compose -f docker-compose.dev.yml down` | Stop containers |
| `docker compose -f docker-compose.dev.yml build` | Rebuild containers |

---
**Happy Coding! 🚀** 