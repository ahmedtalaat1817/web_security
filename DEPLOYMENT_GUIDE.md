# Deployment Guide

## Overview

This guide covers deploying Quickbite to production environments. The platform supports multiple deployment strategies including traditional servers, containerized environments, and cloud platforms.

---

## Prerequisites

Before deploying, ensure you have:

- [ ] Server with SSH access
- [ ] Domain name configured
- [ ] SSL certificate (Let's Encrypt recommended)
- [ ] Redis server
- [ ] MySQL 8.0+ database
- [ ] PHP 8.2+ with required extensions
- [ ] Composer installed
- [ ] Node.js 18+ for asset building

---

## Server Requirements

### System Requirements

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| OS | Ubuntu 20.04 LTS | Ubuntu 22.04 LTS |
| RAM | 2 GB | 4 GB |
| CPU | 2 cores | 4 cores |
| Storage | 20 GB | 50 GB SSD |

### PHP Extensions Required
- `php8.2-mysql` or `php8.2-sqlite3`
- `php8.2-mbstring`
- `php8.2-xml`
- `php8.2-curl`
- `php8.2-zip`
- `php8.2-gd`
- `php8.2-redis`
- `php8.2-bcmath`

---

## Environment Configuration

### Production `.env` File

```env
APP_NAME=Quickbite
APP_ENV=production
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database (MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quickbite_prod
DB_USERNAME=quickbite_user
DB_PASSWORD=secure_password_here

# Redis
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue
QUEUE_CONNECTION=redis

# Session
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Cache
CACHE_STORE=redis

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

# Pusher (Real-Time)
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=xxxxx
PUSHER_APP_KEY=xxxxxxxxxxxxxx
PUSHER_APP_SECRET=xxxxxxxxxxxxxx
PUSHER_APP_CLUSTER=mt1

# Stripe
STRIPE_KEY=pk_live_xxxxx
STRIPE_SECRET=sk_live_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxx
STRIPE_PLATFORM_ACCOUNT_ID=acct_xxxxx

# PayPal
PAYPAL_CLIENT_ID=xxxxx
PAYPAL_CLIENT_SECRET=xxxxx
PAYPAL_MODE=live

# Google Maps
GOOGLE_MAPS_API_KEY=xxxxx
TOMTOM_API_KEY=xxxxx

# FastAPI
FASTAPI_DISPATCH_URL=http://localhost:8088
FASTAPI_INTERNAL_TOKEN=xxxxx

# Surge Pricing
SURGE_STRATEGY=multiplier
SURGE_MAX_MULTIPLIER=2.5
SURGE_MAX_EXTRA_DELIVERY=8
```

---

## Manual Deployment (Traditional Server)

### Step 1: Server Setup

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo add-apt-repository ppa:ondrej/php
sudo apt install php8.2 php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd php8.2-redis php8.2-bcmath

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs

# Install MySQL
sudo apt install mysql-server
sudo mysql_sec_installation

# Install Redis
sudo apt install redis-server
```

### Step 2: Configure MySQL

```bash
# Create database and user
sudo mysql
CREATE DATABASE quickbite_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'quickbite_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON quickbite_prod.* TO 'quickbite_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Deploy Application

```bash
# Create deployment directory
sudo mkdir -p /var/www/quickbite
cd /var/www/quickbite

# Clone repository (or upload via FTP)
git clone https://github.com/your-org/quickbite.git .

# Install dependencies
composer install --optimize-autoloader --no-dev

# Generate application key
php artisan key:generate

# Configure environment
cp .env.example .env
# Edit .env with production settings

# Run migrations
php artisan migrate --force

# Build assets
npm install
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Step 4: Set Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/quickbite

# Storage permissions
sudo chmod -R 775 /var/www/quickbite/storage
sudo chmod -R 775 /var/www/quickbite/bootstrap/cache
```

---

## Nginx Configuration

### Server Block Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com www.your-domain.com;

    # SSL certificates (Let's Encrypt)
    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256;

    root /var/www/quickbite/public;
    index index.php index.html;

    # Laravel routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Static files caching
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
}
```

### Enable Site

```bash
# Create symlink
sudo ln -s /etc/nginx/sites-available/quickbite /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Reload nginx
sudo systemctl reload nginx
```

---

## Queue Worker Setup

### Using Supervisor (Recommended)

```bash
# Install Supervisor
sudo apt install supervisor

# Create configuration
sudo nano /etc/supervisor/conf.d/quickbite-worker.conf
```

```ini
[program:quickbite-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/quickbite/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/quickbite-worker.log
stopwaitsecs=3600
```

```bash
# Update and start
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start quickbite-worker
```

### Laravel Horizon (Alternative)

```bash
# Install Horizon
composer require laravel/horizon

# Publish assets
php artisan horizon:install

# Configure in config/horizon.php

# Run Horizon
php artisan horizon
```

---

## Docker Deployment

### Dockerfile (PHP)

```dockerfile
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    redis-tools \
    supervisor

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Expose port
EXPOSE 9000

# Start supervisor
CMD ["supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
```

### Docker Compose

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: docker/php/Dockerfile
    container_name: quickbite-app
    volumes:
      - .:/var/www
    depends_on:
      - redis
      - mysql
    networks:
      - quickbite

  webserver:
    image: nginx:alpine
    container_name: quickbite-web
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - .:/var/www
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app
    networks:
      - quickbite

  mysql:
    image: mysql:8.0
    container_name: quickbite-mysql
    environment:
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_DATABASE: quickbite
      MYSQL_USER: quickbite
      MYSQL_PASSWORD: quickbite
    volumes:
      - mysql-data:/var/lib/mysql
    networks:
      - quickbite

  redis:
    image: redis:alpine
    container_name: quickbite-redis
    networks:
      - quickbite

  queue:
    build:
      context: .
      dockerfile: docker/php/Dockerfile
    command: php artisan queue:work --sleep=3
    volumes:
      - .:/var/www
    depends_on:
      - redis
      - mysql
    networks:
      - quickbite

volumes:
  mysql-data:

networks:
  quickbite:
    driver: bridge
```

### Build & Run

```bash
# Build images
docker compose build

# Start containers
docker compose up -d

# Run migrations
docker compose exec app php artisan migrate --force

# View logs
docker compose logs -f
```

---

## Cloud Platform Deployment

### Laravel Forge

1. Connect GitHub repository
2. Select server provider (AWS, DigitalOcean, etc.)
3. Configure environment variables
4. Deploy with one click

### Vapor

```bash
# Install Vapor CLI
composer require laravel/vapor-cli --dev

# Configure vapor.yml
vapor init quickbite

# Deploy
vapor deploy production
```

### Heroku

```bash
# Create Procfile
echo "web: vendor/bin/heroku-php-apache2 public/" > Procfile

# Deploy
git push heroku main
```

---

## SSL Configuration (Let's Encrypt)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Generate certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal
sudo certbot renew --dry-run
```

---

## Monitoring & Health Checks

### Health Check Endpoint

```php
// routes/api.php
Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'version' => app()->version(),
    'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
    'redis' => Redis::connection()->ping() ? 'connected' : 'disconnected'
]));
```

### Monitoring Tools

| Tool | Purpose |
|-----|---------|
| Laravel Telescope | Development debugging |
| Laravel Horizon | Queue monitoring |
| Sentry | Error tracking |
| Grafana + Prometheus | Metrics |
| Papertrail | Log management |

---

## Post-Deployment Checklist

- [ ] Verify `APP_ENV=production`
- [ ] Verify `APP_DEBUG=false`
- [ ] SSL working properly
- [ ] Queue workers running
- [ ] Cron jobs configured
- [ ] Backups configured
- [ ] Health endpoint responding
- [ ] Email notifications working
- [ ] Stripe webhooks configured

---

## Cron Job Setup

```bash
# Add to crontab
* * * * * cd /var/www/quickbite && php artisan schedule:run >> /dev/null 2>&1
```

### Laravel Scheduler Commands
- `schedule:run` - Run scheduled tasks
- `horizon:snapshot` - Take metrics snapshot
- `orders:expire` - Expire unpaid orders

---

## Backup Strategy

### Database Backup

```bash
# Daily backup script
mysqldump -u quickbite_user -p quickbite_prod > backup_$(date +%Y%m%d).sql
```

### File Backup

```bash
# Backup storage and public
tar -czf backup_files_$(date +%Y%m%d).tar.gz storage/app public/uploads
```

---

## Performance Optimization

### PHP-FPM Configuration

```ini
; www.conf
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

### Redis Configuration

```redis
# redis.conf
maxmemory 256mb
maxmemory-policy allkeys-lru
save ""
```

---

## Troubleshooting

### Common Issues

| Issue | Solution |
|-------|----------|
| 500 Error | Check `storage/logs/laravel.log` |
| Queue not working | Verify Redis connection |
| Assets not loading | Run `npm run build` |
| SSL not working | Check certificate paths |
| Database connection | Verify credentials in `.env` |

---

## Rollback Procedure

```bash
# Revert to previous version
git checkout previous-tag

# Rollback database
php artisan migrate:rollback

# Clear cache
php artisan optimize:clear
```

---

## Security Hardening

1. Disable `APP_DEBUG` in production
2. Use strong database passwords
3. Rotate secrets regularly
4. Keep dependencies updated
5. Enable firewall rules
6. Review logs regularly

---

## Support

For deployment assistance, contact: devops@quickbite.com