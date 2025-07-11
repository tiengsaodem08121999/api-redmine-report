# 🐳 Docker Quick Start

## 🚀 One-Click Setup

```bash
# Chạy setup tự động (khuyến nghị)
./setup.sh

# Hoặc setup thủ công
./dev.sh
docker compose -f docker-compose.dev.yml exec app composer install
docker compose -f docker-compose.dev.yml exec app php artisan key:generate
docker compose -f docker-compose.dev.yml exec app php artisan migrate
docker compose -f docker-compose.dev.yml exec app php artisan db:seed
```

## 📋 Quick Commands

| Command | Description |
|---------|-------------|
| `./setup.sh` | Setup toàn bộ environment |
| `./dev.sh` | Start development environment |
| `docker compose -f docker-compose.dev.yml down` | Stop containers |
| `docker compose -f docker-compose.dev.yml logs -f app` | View logs |
| `docker compose -f docker-compose.dev.yml exec app php artisan migrate` | Run migrations |

## 🌐 Access

- **Application**: http://localhost:8081
- **Database**: localhost:3309
- **Admin Login**: Check `AdminUserSeeder` for credentials

## 🔄 Hot Reload

- ✅ Code changes → Auto clear cache
- ✅ .env changes → Auto reload
- ✅ File monitoring → Real-time updates

## 📁 Key Files

- `docker-compose.dev.yml` - Development environment
- `Dockerfile` - Container configuration  
- `docker/start-dev.sh` - Hot reload script
- `000-default.conf` - Apache config
- `.env` - Environment variables

## 🐛 Troubleshooting

```bash
# Permission issues
docker compose -f docker-compose.dev.yml exec app chown -R www-data:www-data /var/www/html/storage

# Port conflicts
sudo systemctl stop apache2

# Rebuild everything
docker compose -f docker-compose.dev.yml down -v
docker compose -f docker-compose.dev.yml build --no-cache
docker compose -f docker-compose.dev.yml up -d
```

---
**Xem chi tiết: `DOCKER_SETUP_GUIDE.md`** 