#!/bin/bash

# Development helper script for Laravel Docker

echo "🚀 Starting Laravel Development Environment with Hot Reload..."

# Check if .env file exists
if [ ! -f ".env" ]; then
    echo "❌ Error: .env file not found!"
    echo "Please create .env file with your environment variables"
    exit 1
fi

# Stop any running containers
echo "🛑 Stopping existing containers..."
docker compose down

# Build and start development environment
echo "🔨 Building development containers..."
docker compose -f docker-compose.dev.yml build

echo "▶️  Starting development environment..."
docker compose -f docker-compose.dev.yml up -d

echo "⏳ Waiting for containers to be ready..."
sleep 10

# Show container logs
echo "📋 Container logs:"
docker compose -f docker-compose.dev.yml logs app

echo ""
echo "✅ Development environment is ready!"
echo "🌐 Application URL: http://localhost:8081"
echo "🗄️  Database: localhost:3309"
echo ""
echo "📝 Useful commands:"
echo "  - View logs: docker compose -f docker-compose.dev.yml logs -f app"
echo "  - Stop: docker compose -f docker-compose.dev.yml down"
echo "  - Rebuild: docker compose -f docker-compose.dev.yml build --no-cache"
echo ""
echo "🔄 Hot reload is enabled - your changes will be reflected automatically!" 