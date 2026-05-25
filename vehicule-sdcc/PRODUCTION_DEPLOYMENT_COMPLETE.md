# 🚀 Production Deployment Guide - SDCC Vehicle Reservation System

## Table of Contents
1. [Pre-Deployment Checklist](#pre-deployment-checklist)
2. [Server Setup](#server-setup)
3. [Database Migration](#database-migration)
4. [Environment Configuration](#environment-configuration)
5. [Deployment Steps](#deployment-steps)
6. [Post-Deployment Validation](#post-deployment-validation)
7. [Monitoring & Maintenance](#monitoring--maintenance)
8. [Rollback Procedure](#rollback-procedure)

---

## Pre-Deployment Checklist

### Code Review ✅
- [ ] All debug logs removed (`dd()`, `dump()`, `Log::debug()`)
- [ ] APP_DEBUG=false in production .env
- [ ] No hardcoded passwords or API keys
- [ ] All validators reviewed and FormRequests implemented
- [ ] Rate limiting added to sensitive endpoints
- [ ] CSRF protection enabled
- [ ] Mass assignment protection verified on all models
- [ ] Database indexes optimized
- [ ] Eager loading patterns implemented

### Security ✅
- [ ] Role-based access control (RBAC) configured
- [ ] Email domain validation (@sdcc.ma)
- [ ] Password requirements: min 8 chars, hashed with bcrypt
- [ ] Session security: HTTPS cookie + secure flag
- [ ] Account deactivation workflow tested
- [ ] Backup & restore procedures documented

### Testing ✅
- [ ] Unit tests pass: `php artisan test`
- [ ] Feature tests cover critical flows
- [ ] API endpoints tested with valid/invalid inputs
- [ ] RBAC tests verify authorization
- [ ] Performance tested with expected user load

### Database ✅
- [ ] MySQL server version 5.7+
- [ ] All migrations apply cleanly: `php artisan migrate --force`
- [ ] Foreign keys configured with proper cascade/restrict
- [ ] Indexes created on frequently queried columns
- [ ] Backup strategy defined (daily backups recommended)

---

## Server Setup

### Requirements
```
OS: Linux (Ubuntu 20.04 LTS+) or Windows Server 2019+
PHP: 8.1+ with extensions: bcmath, ctype, curl, mbstring, openssl, pdo_mysql, tokenizer, xml
MySQL: 5.7+ (8.0 recommended)
Node.js: 18+ (for asset compilation)
Composer: Latest version
```

### Installation on Ubuntu 20.04

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.1+ with required extensions
sudo apt install -y php8.1-cli php8.1-fpm php8.1-mysql php8.1-bcmath php8.1-curl php8.1-mbstring php8.1-xml php8.1-zip

# Install MySQL 8.0
sudo apt install -y mysql-server mysql-client

# Install Node.js 18+
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Install Nginx
sudo apt install -y nginx

# Install SSL certificate (Let's Encrypt)
sudo apt install -y certbot python3-certbot-nginx
```

### Directory Permissions

```bash
cd /var/www/reservation-sdcc

# Set ownership
sudo chown -R www-data:www-data .

# Set permissions
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache

# Verify
ls -la | grep storage
```

---

## Database Migration

### MySQL Setup

```bash
# Connect to MySQL
sudo mysql -u root

# Create database
CREATE DATABASE reservation_sdcc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create dedicated user
CREATE USER 'sdcc_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';

# Grant permissions
GRANT ALL PRIVILEGES ON reservation_sdcc.* TO 'sdcc_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Test connection
mysql -u sdcc_user -p reservation_sdcc -e "SELECT 1;"
```

### Configure Laravel Database

Edit `.env` on production server:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reservation_sdcc
DB_USERNAME=sdcc_user
DB_PASSWORD=STRONG_PASSWORD_HERE
```

### Run Migrations

```bash
cd /var/www/reservation-sdcc

# Run migrations (fresh if new install)
php artisan migrate --force

# If seeding with initial super admin
php artisan db:seed --force
```

---

## Environment Configuration

### Create .env from Template

```bash
cp .env.production .env
```

### Update Critical Variables

```env
# Application
APP_NAME="SDCC Réservation"
APP_ENV=production
APP_KEY=base64:GENERATED_BY_KEY:GENERATE
APP_DEBUG=false
APP_URL=https://reservation.sdcc.ma

# Database (set above)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=reservation_sdcc
DB_USERNAME=sdcc_user
DB_PASSWORD=STRONG_PASSWORD_HERE

# Cache & Session
CACHE_DRIVER=redis  # or 'file' if no Redis
SESSION_DRIVER=file
SESSION_LIFETIME=1440  # 24 hours
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict

# Super Admin (first seed only)
SUPER_ADMIN_NAME="Super Admin"
SUPER_ADMIN_EMAIL=superadmin@sdcc.ma
SUPER_ADMIN_PASSWORD=CHANGE_THIS_STRONG_PASSWORD

# Mail (optional for notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@sdcc.ma
MAIL_PASSWORD=app-specific-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@sdcc.ma

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### Generate Application Key

```bash
php artisan key:generate
```

### Optimize for Production

```bash
# Combine config, routes, and views into single files
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optional: optimize autoloader
composer install --optimize-autoloader --no-dev
```

---

## Deployment Steps

### 1. Pull Code from Git

```bash
cd /var/www/reservation-sdcc
git pull origin main
```

### 2. Install Dependencies

```bash
# PHP dependencies
composer install --optimize-autoloader --no-dev

# Node dependencies (if Vite is used)
npm install
npm run build
```

### 3. Clear All Caches

```bash
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 4. Run Database Migrations

```bash
php artisan migrate --force
```

### 5. Seed Initial Data (First Time Only)

```bash
php artisan db:seed --force
```

### 6. Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/reservation-sdcc
sudo chmod -R 775 storage bootstrap/cache
```

### 7. Configure Web Server (Nginx)

Create `/etc/nginx/sites-available/reservation-sdcc`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name reservation.sdcc.ma;

    # Redirect HTTP to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name reservation.sdcc.ma;

    # SSL certificate (Let's Encrypt)
    ssl_certificate /etc/letsencrypt/live/reservation.sdcc.ma/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/reservation.sdcc.ma/privkey.pem;

    root /var/www/reservation-sdcc/public;
    index index.php;

    # Security headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }

    location ~ /\.env {
        deny all;
    }

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1000;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/rss+xml application/atom+xml image/svg+xml;
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/reservation-sdcc /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 8. Setup SSL Certificate

```bash
sudo certbot certonly --nginx -d reservation.sdcc.ma
sudo certbot renew --dry-run  # Test auto-renewal
```

### 9. Start PHP-FPM

```bash
sudo systemctl start php8.1-fpm
sudo systemctl enable php8.1-fpm
```

---

## Post-Deployment Validation

### Application Health

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Test application
curl -I https://reservation.sdcc.ma/login

# Test database connection
php artisan tinker
>>> DB::table('users')->count()
```

### Functional Testing

```bash
# Run tests
php artisan test --parallel

# Check specific test file
php artisan test tests/Feature/ReservationFlowTest.php
```

### Performance Check

```bash
# Check page load time
curl -w "Time taken: %{time_total}s\n" https://reservation.sdcc.ma/login

# Monitor server resources
htop
```

### Security Verification

- [ ] HTTPS is enforced
- [ ] APP_DEBUG=false in logs
- [ ] Super Admin can login: superadmin@sdcc.ma / PASSWORD
- [ ] No sensitive data in error pages
- [ ] Rate limiting works (test login endpoint)
- [ ] CSRF token validation works

---

## Monitoring & Maintenance

### Daily Tasks

```bash
# Check error logs
tail -n 50 storage/logs/laravel.log

# Monitor disk space
df -h

# Monitor MySQL
mysqladmin -u sdcc_user -p processlist
```

### Weekly Tasks

```bash
# Backup database
sudo mysqldump -u sdcc_user -p reservation_sdcc > backup_$(date +%Y%m%d).sql

# Check for security updates
sudo apt update && apt list --upgradable

# Review application logs for errors
grep ERROR storage/logs/laravel.log | wc -l
```

### Monthly Tasks

```bash
# Update PHP and dependencies
composer update
npm update

# Verify SSL certificate expiry
sudo certbot renew --dry-run

# Analyze database performance
php artisan tinker
>>> DB::table('demandes')->count()
>>> DB::table('users')->count()
```

---

## Rollback Procedure

### If Deployment Fails

```bash
# Stop application
sudo systemctl stop php8.1-fpm

# Revert code
git reset --hard HEAD~1
git pull origin main

# Clear caches
php artisan optimize:clear

# Restart
sudo systemctl start php8.1-fpm
```

### Database Rollback

```bash
# List available migrations
php artisan migrate:status

# Rollback to previous state
php artisan migrate:rollback
```

### From Backup

```bash
# Restore database
mysql -u sdcc_user -p reservation_sdcc < backup_20260503.sql

# Verify restore
mysql -u sdcc_user -p reservation_sdcc -e "SELECT COUNT(*) FROM users;"
```

---

## Support & Documentation

- **Admin Dashboard:** https://reservation.sdcc.ma/admin
- **API Documentation:** See `INTEGRATION_DOCUMENTATION.md`
- **Error Logs:** `/var/www/reservation-sdcc/storage/logs/`
- **Config Reference:** `config/` directory

---

## Emergency Contacts

- **Database Issues:** MySQL logs at `/var/log/mysql/error.log`
- **Web Server Issues:** Nginx logs at `/var/log/nginx/error.log`
- **Application Issues:** Laravel logs at `storage/logs/laravel.log`

---

**Last Updated:** May 3, 2026  
**Deployment Version:** 1.0.0  
**Status:** ✅ Production Ready
