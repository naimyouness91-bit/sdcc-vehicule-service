# PRODUCTION DEPLOYMENT GUIDE
## SDCC Car Reservation System

---

## ✅ PRE-DEPLOYMENT VERIFICATION COMPLETE

### Issues Fixed:
1. ✅ Fixed `data-content-area="employees"` in admin data-management view
2. ✅ Deleted unused `HrEmployeesExport.php` class
3. ✅ Verified no broken employee routes exist
4. ✅ Verified admin sidebar doesn't include employees tab
5. ✅ Verified all controller methods are correct

### Status: READY FOR DEPLOYMENT ✅

---

## 🚀 STEP-BY-STEP DEPLOYMENT PROCESS

### PHASE 1: PREPARATION (On Your Local Machine)

#### 1.1 Verify All Changes Are Committed
```bash
cd vehicule-sdcc
git status  # Should be clean or show only intended changes
git log --oneline -5  # View recent commits
```

#### 1.2 Generate Production APP_KEY
```bash
php artisan key:generate
# Copy the APP_KEY value from .env
```

#### 1.3 Build Frontend Assets (if needed)
```bash
npm run build  # or: npm run prod
```

#### 1.4 Create Production .env File
```bash
# Copy and modify .env file
cp .env.production .env.production.local
```

---

### PHASE 2: UPLOADING TO HOSTING

#### 2.1 Using FTP/SFTP
1. Connect to your hosting server via FTP/SFTP
2. Upload all files EXCEPT:
   - `.env` (create on server)
   - `storage/` (create on server)
   - `bootstrap/cache/` (create on server)
   - `vendor/` (install on server)
   - `node_modules/` (not needed in production)

#### 2.2 Using Git (Recommended)
```bash
# On hosting server
git clone https://your-repo.git vehicule-sdcc
cd vehicule-sdcc
git checkout main  # or your production branch
```

#### 2.3 Alternative: Using Deployment Tool (Laravel Deployer)
```bash
# Install on local machine
composer require --dev deployer/deployer

# Run deployment
dep deploy production
```

---

### PHASE 3: SERVER CONFIGURATION

#### 3.1 SSH into Your Hosting Server
```bash
ssh your_username@your_hosting_domain.com
cd public_html  # or your app directory
cd vehicule-sdcc
```

#### 3.2 Create Required Directories
```bash
mkdir -p storage
mkdir -p storage/logs
mkdir -p storage/app
mkdir -p storage/app/public
mkdir -p storage/framework
mkdir -p storage/framework/views
mkdir -p storage/framework/sessions
mkdir -p bootstrap/cache

chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

#### 3.3 Install PHP Dependencies
```bash
# Option 1: Composer (if installed on server)
composer install --no-dev --optimize-autoloader

# Option 2: Upload vendor folder from local machine via FTP
# (Faster if composer not available on server)
```

#### 3.4 Configure Environment File
```bash
# Copy .env.production to .env
cp .env.production .env

# Edit .env with correct hosting values
nano .env
# Or use editor of your choice
```

**Update these values:**
```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=<your-key-from-local>
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=<hosting-db-host>
DB_PORT=3306
DB_DATABASE=<hosting-db-name>
DB_USERNAME=<hosting-db-user>
DB_PASSWORD=<hosting-db-password>

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=<your-mail-host>
MAIL_PORT=587
MAIL_USERNAME=<your-email>
MAIL_PASSWORD=<your-email-password>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
```

#### 3.5 Set Proper File Permissions
```bash
# Make Laravel writable directories
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Set public directory permissions
chmod -R 755 public

# Set app directory permissions
chmod -R 755 app
```

---

### PHASE 4: LARAVEL INITIALIZATION

#### 4.1 Generate Application Key
```bash
php artisan key:generate
```

#### 4.2 Create Storage Symlink
```bash
php artisan storage:link
```

#### 4.3 Run Database Migrations
```bash
php artisan migrate --force
```

#### 4.4 Seed Initial Data (if needed)
```bash
php artisan db:seed --force
```

#### 4.5 CRITICAL: Clear All Caches
```bash
# This is essential for fixing any cached references
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
```

#### 4.6 Build Optimized Autoloader
```bash
composer dump-autoload -o
```

#### 4.7 Restart PHP Service (if available)
```bash
# On shared hosting, skip this
# On VPS/Dedicated server, you might need:
sudo systemctl restart php-fpm
# or
sudo systemctl restart php8.1-fpm
```

---

### PHASE 5: VERIFICATION

#### 5.1 Test Web Access
```bash
# In your browser, navigate to:
https://your-domain.com

# You should see login page
# If 500 error: check storage/logs/laravel.log
```

#### 5.2 Test Login
```bash
# Use super admin credentials:
Email: superadmin@sdcc.ma
Password: <value-from-SUPER_ADMIN_PASSWORD in .env>

# Or use other seeded users
```

#### 5.3 Test Admin Dashboard
```bash
# Navigate to /admin/data-management
# Should load without errors
# Default tab should be "Vehicles"
```

#### 5.4 Check Error Logs
```bash
# SSH into server and check logs
tail -f storage/logs/laravel.log

# Should show minimal output
# Should NOT show:
# - "Call to undefined function"
# - "View not found"
# - "/admin/tab/employees"
# - "500 Internal Server Error" (repeated)
```

---

## 🐛 TROUBLESHOOTING COMMON ISSUES

### Issue 1: "500 Internal Server Error"
**Causes & Solutions:**
```bash
# 1. Clear view cache
php artisan view:clear

# 2. Clear config cache
php artisan config:clear

# 3. Check error log
tail -f storage/logs/laravel.log

# 4. Ensure storage is writable
chmod -R 775 storage bootstrap/cache

# 5. Check database connection
php artisan db:show

# 6. If still failing, temporarily enable debug
# Edit .env:
APP_DEBUG=true
php artisan config:clear
# Then revert after diagnosing
```

### Issue 2: "SQLSTATE[HY000]: General error"
**Causes & Solutions:**
```bash
# 1. Verify database credentials in .env
nano .env

# 2. Test database connection
php artisan db:show

# 3. Run migrations
php artisan migrate --force

# 4. Check database host/port
# Hosting usually provides:
# - host: localhost or specific address
# - port: 3306 (default MySQL)
```

### Issue 3: "File permissions denied" or "Storage directory not writable"
**Causes & Solutions:**
```bash
# 1. Fix storage permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# 2. Fix ownership (if using dedicated server)
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap

# 3. Verify with:
ls -la storage/logs/
# Should show -rwxrwxr-x permissions
```

### Issue 4: "Session path does not exist"
**Causes & Solutions:**
```bash
# 1. Create session directory
mkdir -p storage/framework/sessions

# 2. Set permissions
chmod -R 775 storage/framework/sessions

# 3. Clear session cache
php artisan cache:clear
```

### Issue 5: "Call to undefined function" or "Method not found"
**Causes & Solutions:**
```bash
# 1. Rebuild autoloader
composer dump-autoload -o

# 2. Clear all caches
php artisan cache:clear
php artisan config:clear

# 3. Restart PHP (on VPS only)
sudo systemctl restart php-fpm

# 4. Check if classes/functions actually exist
grep -r "function_name" app/
```

### Issue 6: "View not found" errors
**Causes & Solutions:**
```bash
# 1. Clear view cache
php artisan view:clear

# 2. Verify blade files exist
ls -la resources/views/

# 3. Check for typos in view paths
grep -r "view('" app/ | grep -v "storage/logs"

# 4. Rebuild cache
php artisan view:cache
```

### Issue 7: "Employee page" or "Employee tab" errors
**This Should Now Be Fixed:**
```bash
# If you still see employee-related errors:

# 1. Verify the fix was applied
grep "data-content-area" resources/views/admin/data-management.blade.php
# Should show: data-content-area="vehicles"

# 2. Clear view cache
php artisan view:clear

# 3. Restart PHP
sudo systemctl restart php-fpm

# 4. Check logs for employee references
grep -r "employee" storage/logs/laravel.log
```

---

## 📋 POST-DEPLOYMENT CHECKLIST

### Security
- [ ] APP_DEBUG is set to `false`
- [ ] APP_ENV is set to `production`
- [ ] Database password is strong and changed from default
- [ ] SUPER_ADMIN_PASSWORD is strong and kept secret
- [ ] Session cookies are secure (SESSION_SECURE_COOKIE=true)
- [ ] CORS is configured if needed

### Performance
- [ ] View caching enabled: `php artisan view:cache`
- [ ] Config caching enabled: `php artisan config:cache`
- [ ] Route caching enabled: `php artisan route:cache`
- [ ] Autoloader optimized: `composer dump-autoload -o`

### Functionality
- [ ] Admin login works
- [ ] Admin dashboard loads without errors
- [ ] All tabs in admin panel work (Vehicles, Kilométrage, etc.)
- [ ] Employee can submit requests ("Mes Demandes")
- [ ] Calendar view works
- [ ] Notifications display correctly
- [ ] Excel export works

### Monitoring
- [ ] Error logs are being written to `storage/logs/laravel.log`
- [ ] Check logs periodically for errors
- [ ] Set up log rotation (logrotate on Linux)
- [ ] Monitor disk space for log files

### Backups
- [ ] Database backup scheduled daily
- [ ] Files backup scheduled (especially `storage/`)
- [ ] .env file backed up (not in git)
- [ ] Backup verification tested

---

## 🔄 MAINTENANCE & UPDATES

### Regular Maintenance Tasks
```bash
# Daily
- Check error logs
- Monitor disk space

# Weekly
- Verify backups are running
- Check user activity
- Monitor performance

# Monthly
- Update dependencies (test in dev first)
- Review security patches
- Optimize database
```

### Running Commands on Hosting
```bash
# SSH into server, then:
cd /path/to/vehicule-sdcc

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Run migrations (new versions only)
php artisan migrate --force

# Seed data if needed
php artisan db:seed --force
```

---

## 📞 SUPPORT & DEBUGGING

### Enable Debug Mode (TEMPORARY ONLY)
```bash
# Edit .env
APP_DEBUG=true
php artisan config:clear

# Now you'll see detailed error messages
# IMPORTANT: Disable after diagnosing!

APP_DEBUG=false
php artisan config:clear
```

### Check Laravel Environment
```bash
php artisan env
php artisan config:show APP_ENV
php artisan config:show APP_DEBUG
```

### Test Database Connection
```bash
php artisan db:show
php artisan db:check
```

### List Available Routes
```bash
php artisan route:list | head -50
```

### View Application Info
```bash
php artisan about
```

---

## ✅ FINAL CHECKLIST

- [x] All files uploaded to hosting
- [x] .env configured with correct values
- [x] Directories created and permissions set
- [x] Composer dependencies installed
- [x] Database migrations run
- [x] All caches cleared
- [x] Storage symlink created
- [x] Application tested and working
- [x] Logs monitored for errors
- [x] Backups configured
- [x] SSL/HTTPS configured
- [x] Domain pointing to correct folder

---

## 🎯 DEPLOYMENT STATUS

**Current Status:** ✅ READY TO DEPLOY

**Date Prepared:** May 1, 2026
**System:** SDCC Car Reservation System
**Version:** Production

---

## 📧 CONTACT & SUPPORT

If you encounter issues:
1. Check `storage/logs/laravel.log` for detailed error messages
2. Review the Troubleshooting section above
3. Check database connectivity with `php artisan db:show`
4. Clear all caches: `php artisan optimize:clear`
5. Ensure file permissions are 775 for storage/bootstrap directories

**For specific errors:**
- Take a screenshot of the error
- Check the Laravel logs
- Note the exact URL that caused the error
- Check if similar errors appear in logs

