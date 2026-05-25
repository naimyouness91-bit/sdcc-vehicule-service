# 🚀 SDCC Réservation Véhicule - PRODUCTION DEPLOYMENT GUIDE

**Version:** 1.0  
**Date:** 4 mai 2026  
**Frameworks:** Laravel 10.10 | PHP 8.1+ | MySQL 8.0+

---

## 📋 TABLE OF CONTENTS

1. [Pre-Deployment Checklist](#pre-deployment-checklist)
2. [Server Requirements](#server-requirements)
3. [Installation Steps](#installation-steps)
4. [Environment Configuration](#environment-configuration)
5. [Database Setup](#database-setup)
6. [Caching & Queue](#caching--queue)
7. [SSL/HTTPS Configuration](#ssllhttps-configuration)
8. [Monitoring & Maintenance](#monitoring--maintenance)
9. [Troubleshooting](#troubleshooting)
10. [Rollback Procedures](#rollback-procedures)

---

## ✅ PRE-DEPLOYMENT CHECKLIST

### Code Quality
- [ ] `php artisan config:cache` - Verify no config errors
- [ ] `php artisan route:cache` - Verify no route errors
- [ ] `php artisan view:cache` - Pre-compile all views
- [ ] Run tests: `php artisan test`
- [ ] No `dd()`, `dump()` or `console.log` in code
- [ ] All migrations tested locally with MySQL

### Security
- [ ] `APP_DEBUG=false` in `.env`
- [ ] `APP_ENV=production` in `.env`
- [ ] Strong `APP_KEY` generated: `php artisan key:generate`
- [ ] All sensitive values in `.env` (never in code)
- [ ] HTTPS certificate obtained (SSL/TLS)
- [ ] CORS headers configured if needed
- [ ] CSRF tokens enabled

### Database
- [ ] Database backup taken
- [ ] Migrations tested in production-like environment
- [ ] All indexes created
- [ ] Foreign keys verified
- [ ] Backup strategy documented

### Performance
- [ ] Redis server deployed and running
- [ ] Cache and queue drivers configured
- [ ] Horizon installed (if using queues)
- [ ] Database queries optimized (eager loading)
- [ ] Assets compiled and minified

---

## 🖥️ SERVER REQUIREMENTS

### Minimum Specifications
```
CPU:       2 cores @ 2.0+ GHz
RAM:       2GB minimum (4GB recommended)
Disk:      20GB SSD for application + data
Network:   100 Mbps minimum
```

### Software Stack
```
OS:        Ubuntu 20.04 LTS / 22.04 LTS (or CentOS 8+)
PHP:       8.1+ with extensions:
           - php-fpm
           - php-mysql
           - php-redis
           - php-gd
           - php-curl
           - php-xml
           - php-zip
           - php-bcmath
           - php-mbstring
           - php-intl

Web Server: Nginx 1.20+
           (or Apache 2.4+ with mod_rewrite)

Database:  MySQL 8.0+ or MariaDB 10.5+
Cache:     Redis 6.0+
Composer:  2.0+
Node.js:   16+ (for Vite/npm)
```

### System Packages (Ubuntu)
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y curl wget git gnupg2 ca-certificates lsb-release \
  software-properties-common apt-transport-https build-essential \
  nginx mysql-server redis-server supervisor nodejs npm

# PHP 8.1
sudo add-apt-repository ppa:ondrej/php -y
sudo apt install -y php8.1-fpm php8.1-mysql php8.1-redis php8.1-gd \
  php8.1-curl php8.1-xml php8.1-zip php8.1-bcmath php8.1-mbstring \
  php8.1-intl php8.1-dev
```

---

## 📦 INSTALLATION STEPS

### 1. Clone Repository
```bash
cd /var/www
sudo git clone https://github.com/your-org/sdcc-reservation.git reservation-sdcc
cd reservation-sdcc
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 777 storage bootstrap/cache
```

### 2. Install Dependencies
```bash
# PHP dependencies
composer install --no-dev --optimize-autoloader

# Frontend dependencies (if using Vite)
npm install
npm run build
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Create Directories
```bash
# Storage directories
mkdir -p storage/logs
mkdir -p storage/app/public
mkdir -p bootstrap/cache

# Set permissions
sudo chown -R www-data:www-data storage bootstrap
sudo chmod -R 777 storage bootstrap/cache
```

### 5. Database Setup
```bash
# Run all migrations
php artisan migrate --force

# Seed initial data (super admin)
php artisan db:seed --class=DatabaseSeeder

# (Optional) Specific seeders
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### 6. Caching
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views (optional but recommended)
php artisan view:cache

# Generate API documentation (if applicable)
php artisan scribe:generate
```

---

## ⚙️ ENVIRONMENT CONFIGURATION

### Create `.env` from Template
```bash
cp .env.production .env
```

### Complete `.env` File
```env
# ═══════════════════════════════════════════════════════════════════════════
# APPLICATION CONFIGURATION
# ═══════════════════════════════════════════════════════════════════════════

APP_NAME="SDCC Réservation"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE_AFTER_key:generate
APP_DEBUG=false
APP_URL=https://reservation.sdcc.ma  # ← SET TO YOUR DOMAIN

# Timezone for all operations
APP_TIMEZONE=Africa/Casablanca

# ═══════════════════════════════════════════════════════════════════════════
# LOGGING & MONITORING
# ═══════════════════════════════════════════════════════════════════════════

LOG_CHANNEL=stack
LOG_LEVEL=warning  # Use: debug|info|notice|warning|error|critical|alert|emergency
LOG_DEPRECATIONS_CHANNEL=null
LOG_DAILY_DAYS=14  # Keep 14 days of logs

# ═══════════════════════════════════════════════════════════════════════════
# DATABASE CONFIGURATION
# ═══════════════════════════════════════════════════════════════════════════

DB_CONNECTION=mysql
DB_HOST=127.0.0.1          # Or your RDS/managed MySQL endpoint
DB_PORT=3306
DB_DATABASE=sdcc_reservation
DB_USERNAME=sdcc_app_user  # Strong, limited-privilege user
DB_PASSWORD=YOUR_STRONG_PASSWORD_HERE

# Connection pooling (if using)
DB_POOL_MIN=2
DB_POOL_MAX=10

# ═══════════════════════════════════════════════════════════════════════════
# CACHE DRIVER (Redis Recommended for Production)
# ═══════════════════════════════════════════════════════════════════════════

CACHE_DRIVER=redis
CACHE_TTL=3600  # Default cache duration in seconds (1 hour)

# ═══════════════════════════════════════════════════════════════════════════
# SESSION CONFIGURATION
# ═══════════════════════════════════════════════════════════════════════════

SESSION_DRIVER=cookie
SESSION_LIFETIME=1440           # 24 hours
SESSION_SECURE_COOKIE=true      # HTTPS only
SESSION_HTTP_ONLY=true          # No JS access
SESSION_SAME_SITE=lax           # CSRF protection
SESSION_DOMAIN=.sdcc.ma         # Cookie domain

# ═══════════════════════════════════════════════════════════════════════════
# QUEUE CONFIGURATION
# ═══════════════════════════════════════════════════════════════════════════

QUEUE_CONNECTION=redis
QUEUE_DRIVER=redis

# ═══════════════════════════════════════════════════════════════════════════
# BROADCAST CONFIGURATION
# ═══════════════════════════════════════════════════════════════════════════

BROADCAST_DRIVER=redis

# ═══════════════════════════════════════════════════════════════════════════
# REDIS CONFIGURATION
# ═══════════════════════════════════════════════════════════════════════════

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=YOUR_REDIS_PASSWORD_HERE
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
REDIS_QUEUE_DB=2

# ═══════════════════════════════════════════════════════════════════════════
# FILE SYSTEM
# ═══════════════════════════════════════════════════════════════════════════

FILESYSTEM_DISK=local
FILESYSTEM_VISIBILITY=private

# ═══════════════════════════════════════════════════════════════════════════
# EMAIL CONFIGURATION
# ═══════════════════════════════════════════════════════════════════════════

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io       # Or your email provider
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@sdcc.ma
MAIL_FROM_NAME="SDCC Réservation"

# ═══════════════════════════════════════════════════════════════════════════
# HR EXCEL CONFIGURATION
# ═══════════════════════════════════════════════════════════════════════════

HR_EXCEL_DISK=local
HR_EXCEL_JSON_PATH=hr/main_requests.json
HR_EXCEL_FILE_PATH=hr/main_requests.xlsx
HR_EXCEL_DOWNLOAD_NAME=main_requests.xlsx

# ═══════════════════════════════════════════════════════════════════════════
# SUPER ADMIN INITIAL SETUP
# ═══════════════════════════════════════════════════════════════════════════

SUPER_ADMIN_NAME="Super Administrator"
SUPER_ADMIN_EMAIL=superadmin@sdcc.ma
SUPER_ADMIN_PASSWORD=GENERATE_STRONG_PASSWORD_MIN_12_CHARS
SUPER_ADMIN_SERVICE="Direction Générale"

# ═══════════════════════════════════════════════════════════════════════════
# API RATE LIMITING (Optional)
# ═══════════════════════════════════════════════════════════════════════════

# API_RATE_LIMIT=60     # Requests per minute
# API_RATE_WINDOW=60    # Window in minutes

# ═══════════════════════════════════════════════════════════════════════════
# SENTRY ERROR TRACKING (Optional for production monitoring)
# ═══════════════════════════════════════════════════════════════════════════

# SENTRY_LARAVEL_DSN=https://your-key@sentry.io/your-project-id
# SENTRY_ENVIRONMENT=production
# SENTRY_TRACES_SAMPLE_RATE=0.1
```

---

## 🗄️ DATABASE SETUP

### 1. Create Database & User
```bash
mysql -u root -p

CREATE DATABASE sdcc_reservation 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

CREATE USER 'sdcc_app_user'@'localhost' 
  IDENTIFIED BY 'YOUR_STRONG_PASSWORD';

GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, REFERENCES 
  ON sdcc_reservation.* 
  TO 'sdcc_app_user'@'localhost';

FLUSH PRIVILEGES;
EXIT;
```

### 2. Verify Migrations
```bash
# Test migrations (dry run first)
php artisan migrate --force --dry-run

# Run actual migrations
php artisan migrate --force

# Verify all tables
php artisan tinker
>>> DB::table('migrations')->pluck('migration')
```

### 3. Seed Data
```bash
# Seed all data
php artisan db:seed --class=DatabaseSeeder

# Or specific seeds
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan db:seed --class=UserSeeder
```

### 4. Verify Indexes
```bash
mysql -u sdcc_app_user -p sdcc_reservation

SHOW INDEXES FROM demandes;
SHOW INDEXES FROM users;
SHOW INDEXES FROM cars;
```

---

## ⚡ CACHING & QUEUE

### Enable Redis
```bash
# Start Redis
sudo service redis-server start
sudo systemctl enable redis-server

# Test Redis connection
redis-cli ping  # Should return PONG
```

### Configure Supervisor for Queue Workers
```bash
sudo nano /etc/supervisor/conf.d/sdcc-queue.conf
```

Add:
```ini
[program:sdcc-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/reservation-sdcc/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/reservation-sdcc/storage/logs/queue.log
environment=APP_ENV=production,APP_DEBUG=false
```

Then:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start sdcc-queue-worker:*
```

### Verify Queue Workers
```bash
php artisan queue:monitor
sudo supervisorctl status
```

---

## 🔒 SSL/HTTPS CONFIGURATION

### Option 1: Let's Encrypt (Free)
```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Generate certificate
sudo certbot certonly --nginx -d reservation.sdcc.ma

# Auto-renewal
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer
```

### Option 2: Commercial Certificate
```bash
# Place your certificate files in:
# /etc/ssl/certs/reservation.sdcc.ma.crt
# /etc/ssl/private/reservation.sdcc.ma.key

# Verify certificate
openssl x509 -in /etc/ssl/certs/reservation.sdcc.ma.crt -text -noout
```

### Nginx SSL Configuration
```nginx
server {
    listen 443 ssl http2;
    server_name reservation.sdcc.ma;

    ssl_certificate /etc/letsencrypt/live/reservation.sdcc.ma/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/reservation.sdcc.ma/privkey.pem;

    # Strong SSL settings
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    # HSTS header
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Laravel configuration
    root /var/www/reservation-sdcc/public;
    index index.php;

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name reservation.sdcc.ma;
    return 301 https://$server_name$request_uri;
}
```

Test & reload:
```bash
sudo nginx -t
sudo systemctl reload nginx
```

---

## 📊 MONITORING & MAINTENANCE

### Application Health Check
```bash
# Create health check endpoint
php artisan make:controller HealthController

# Test: curl https://reservation.sdcc.ma/health
```

### Log Monitoring
```bash
# Real-time log monitoring
tail -f storage/logs/laravel.log

# Log rotation (daily)
# Set in .env: LOG_DAILY_DAYS=14

# Clean old logs
find storage/logs -name "*.log" -mtime +14 -delete
```

### Database Backups
```bash
# Create backup script: /usr/local/bin/backup-sdcc.sh
#!/bin/bash

BACKUP_DIR="/var/backups/sdcc"
DB_NAME="sdcc_reservation"
DB_USER="sdcc_app_user"
DATE=$(date +"%Y%m%d_%H%M%S")

mkdir -p $BACKUP_DIR
mysqldump -u $DB_USER -p${DB_PASSWORD} $DB_NAME | gzip > $BACKUP_DIR/sdcc_$DATE.sql.gz

# Keep only last 30 days
find $BACKUP_DIR -name "sdcc_*.sql.gz" -mtime +30 -delete

echo "Backup completed: $BACKUP_DIR/sdcc_$DATE.sql.gz"

# Schedule with cron
chmod +x /usr/local/bin/backup-sdcc.sh
sudo crontab -e
# Add: 0 2 * * * /usr/local/bin/backup-sdcc.sh
```

### System Resource Monitoring
```bash
# Install tools
sudo apt install -y htop nethogs iotop

# CPU/Memory
htop

# Network
nethogs

# Disk I/O
iotop

# Check disk space
df -h
du -sh /var/www/reservation-sdcc/*
```

---

## 🐛 TROUBLESHOOTING

### 500 Error - Check Logs
```bash
tail -f storage/logs/laravel.log
sudo tail -f /var/log/nginx/error.log
```

### Database Connection Failed
```bash
# Test MySQL connection
php artisan tinker
>>> DB::connection()->getPdo()

# Check credentials in .env
mysql -u sdcc_app_user -p sdcc_reservation
```

### Queue Not Processing
```bash
# Check queue status
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Monitor queue
php artisan queue:monitor
```

### High Memory Usage
```bash
# Check what's consuming memory
ps aux --sort=-%mem | head

# Clear old cache
php artisan cache:clear
php artisan view:clear
```

### Redis Connection Failed
```bash
# Test Redis
redis-cli ping

# Check Redis config
sudo nano /etc/redis/redis.conf

# Restart Redis
sudo systemctl restart redis-server
```

---

## ↩️ ROLLBACK PROCEDURES

### Database Rollback
```bash
# Rollback last batch
php artisan migrate:rollback

# Rollback to specific migration
php artisan migrate:rollback --step=5

# Rollback all (DANGER!)
php artisan migrate:reset
```

### Application Rollback
```bash
# Revert to previous commit
git checkout v1.0.0  # Use version tags
git pull origin main

# Clear caches
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Re-cache
php artisan config:cache
php artisan route:cache
```

### Restore from Backup
```bash
# Restore database
gunzip < /var/backups/sdcc/sdcc_YYYYMMDD_HHMMSS.sql.gz | \
  mysql -u sdcc_app_user -p sdcc_reservation
```

---

## ✨ POST-DEPLOYMENT VALIDATION

```bash
# ✅ All checks
1. Test login: https://reservation.sdcc.ma/login
2. Create test reservation
3. Check email notifications (if configured)
4. Verify calendar displays correctly
5. Test PDF export
6. Check admin panel access
7. Verify Redis cache working: php artisan tinker >>> Redis::ping()
8. Monitor logs for errors: tail -f storage/logs/laravel.log
9. Test queue: php artisan queue:work --once
10. Performance check: curl -w "@curl-format.txt" -o /dev/null -s https://reservation.sdcc.ma
```

---

## 📞 SUPPORT & DOCUMENTATION

- **Laravel Docs:** https://laravel.com/docs/10.x
- **Nginx Docs:** https://nginx.org/en/docs/
- **MySQL Docs:** https://dev.mysql.com/doc/
- **Redis Docs:** https://redis.io/docs/

---

**🎉 Deployment Complete! Monitor logs and enjoy your production application!**
