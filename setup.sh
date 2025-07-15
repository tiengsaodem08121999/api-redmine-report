#!/bin/bash

# 🚀 Automated Docker Setup Script for Laravel Redmine API
# This script will setup the entire development environment

set -e  # Exit on any error

echo "🐳 Starting Docker Setup for Laravel Redmine API..."
echo "=================================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check prerequisites
print_status "Checking prerequisites..."

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed. Please install Docker first."
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker compose &> /dev/null; then
    print_error "Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Check if .env file exists
if [ ! -f ".env" ]; then
    print_error ".env file not found. Please create .env file first."
    exit 1
fi

print_success "Prerequisites check passed!"

# Stop any existing containers
print_status "Stopping any existing containers..."
docker compose -f docker-compose.dev.yml down 2>/dev/null || true
print_success "Existing containers stopped."

# Build containers
print_status "Building Docker containers..."
docker compose -f docker-compose.dev.yml build
print_success "Containers built successfully."

# Start containers
print_status "Starting containers..."
docker compose -f docker-compose.dev.yml up -d
print_success "Containers started successfully."

# Wait for containers to be ready
print_status "Waiting for containers to be ready..."
sleep 15

# Check if containers are running
if ! docker compose -f docker-compose.dev.yml ps | grep -q "Up"; then
    print_error "Containers failed to start. Check logs with: docker compose -f docker-compose.dev.yml logs"
    exit 1
fi

print_success "Containers are running!"

# Install composer dependencies
print_status "Installing Composer dependencies..."
docker compose -f docker-compose.dev.yml exec app composer install
print_success "Composer dependencies installed."

# Generate Laravel key
print_status "Generating Laravel application key..."
docker compose -f docker-compose.dev.yml exec app php artisan key:generate
print_success "Laravel key generated."

# Run migrations
print_status "Running database migrations..."
docker compose -f docker-compose.dev.yml exec app php artisan migrate
print_success "Database migrations completed."

# Run seeders
print_status "Running database seeders..."
docker compose -f docker-compose.dev.yml exec app php artisan db:seed
print_success "Database seeders completed."

# Clear caches
print_status "Clearing Laravel caches..."
docker compose -f docker-compose.dev.yml exec app php artisan config:clear
docker compose -f docker-compose.dev.yml exec app php artisan cache:clear
docker compose -f docker-compose.dev.yml exec app php artisan route:clear
docker compose -f docker-compose.dev.yml exec app php artisan view:clear
print_success "Laravel caches cleared."

# Test the application
print_status "Testing application..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8081 | grep -q "302\|200"; then
    print_success "Application is accessible!"
else
    print_warning "Application might not be ready yet. Please wait a moment and try again."
fi

echo ""
echo "🎉 Setup completed successfully!"
echo "=================================="
echo "🌐 Application URL: http://localhost:8081"
echo "🗄️  Database: localhost:3309"
echo "📝 Useful commands:"
echo "  - View logs: docker compose -f docker-compose.dev.yml logs -f app"
echo "  - Stop: docker compose -f docker-compose.dev.yml down"
echo "  - Restart: ./dev.sh"
echo ""
echo "🔄 Hot reload is enabled - your changes will be reflected automatically!"
echo ""
print_success "Setup completed! Happy coding! 🚀" 