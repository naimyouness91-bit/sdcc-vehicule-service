#!/bin/bash
# ╔══════════════════════════════════════════════════════════════════════════════╗
# ║  SDCC — Réservation Véhicule Service — Production Deployment Script        ║
# ║  Usage: bash deploy.sh                                                      ║
# ╚══════════════════════════════════════════════════════════════════════════════╝

set -e

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  SDCC Deployment Script                                     ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# ── 1. Check PHP version ────────────────────────────────────────────────────
echo "▸ [1/10] Checking PHP version..."
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
echo "  PHP version: $PHP_VERSION"
if [[ $(php -r "echo version_compare(PHP_VERSION, '8.1.0', '>=') ? 'ok' : 'fail';") == "fail" ]]; then
    echo "  ✗ ERROR: PHP 8.1+ required. Found: $(php -v | head -1)"
    exit 1
fi
echo "  ✓ PHP version OK"

# ── 2. Check required PHP extensions ────────────────────────────────────────
echo "▸ [2/10] Checking PHP extensions..."
REQUIRED_EXTENSIONS=(pdo_mysql mbstring openssl tokenizer xml ctype json bcmath fileinfo gd zip)
MISSING=()
for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if ! php -m | grep -qi "^${ext}$"; then
        MISSING+=("$ext")
    fi
done
if [ ${#MISSING[@]} -gt 0 ]; then
    echo "  ✗ Missing extensions: ${MISSING[*]}"
    echo "  Install with: sudo apt install php${PHP_VERSION}-{$(IFS=,; echo "${MISSING[*]}")}"
    exit 1
fi
echo "  ✓ All required extensions present"

# ── 3. Check .env exists ────────────────────────────────────────────────────
echo "▸ [3/10] Checking .env file..."
if [ ! -f .env ]; then
    echo "  ✗ .env file not found!"
    echo "  Copy .env.production to .env and fill in production values:"
    echo "    cp .env.production .env"
    exit 1
fi

# Validate critical env vars
APP_ENV=$(grep "^APP_ENV=" .env | cut -d'=' -f2)
APP_DEBUG=$(grep "^APP_DEBUG=" .env | cut -d'=' -f2)
APP_KEY=$(grep "^APP_KEY=" .env | cut -d'=' -f2)

if [ "$APP_ENV" != "production" ]; then
    echo "  ⚠ WARNING: APP_ENV is '$APP_ENV', should be 'production'"
fi
if [ "$APP_DEBUG" == "true" ]; then
    echo "  ⚠ WARNING: APP_DEBUG is 'true' — MUST be 'false' in production!"
fi
if [ -z "$APP_KEY" ]; then
    echo "  ▸ Generating APP_KEY..."
    php artisan key:generate --force
fi
echo "  ✓ .env file OK"

# ── 4. Install dependencies ────────────────────────────────────────────────
echo "▸ [4/10] Installing Composer dependencies (no-dev)..."
composer install --no-dev --optimize-autoloader --no-interaction
echo "  ✓ Dependencies installed"

# ── 5. Run migrations ──────────────────────────────────────────────────────
echo "▸ [5/10] Running database migrations..."
php artisan migrate --force
echo "  ✓ Migrations complete"

# ── 6. Seed database (first deploy only) ────────────────────────────────────
echo "▸ [6/10] Checking if seeding is needed..."
USERS_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null || echo "0")
if [ "$USERS_COUNT" == "0" ]; then
    echo "  ▸ No users found — running database seeder..."
    php artisan db:seed --force
    echo "  ✓ Database seeded"
else
    echo "  ✓ Database already seeded ($USERS_COUNT users found)"
fi

# ── 7. Build frontend assets ───────────────────────────────────────────────
echo "▸ [7/10] Building frontend assets..."
if command -v npm &> /dev/null; then
    npm ci --production=false
    npm run build
    echo "  ✓ Vite assets built"
else
    echo "  ⚠ npm not found — skipping asset build"
    echo "  Make sure public/build/ contains pre-built assets"
fi

# ── 8. Storage & permissions ────────────────────────────────────────────────
echo "▸ [8/10] Setting up storage..."
php artisan storage:link 2>/dev/null || echo "  (storage link already exists)"

# Create required directories
mkdir -p storage/logs
mkdir -p storage/framework/{cache,sessions,testing,views}
mkdir -p storage/app/public
mkdir -p storage/app/hr
mkdir -p bootstrap/cache

# Set permissions (Linux/Mac)
if [ "$(uname)" != "MINGW64_NT" ] && [ "$(uname)" != "MSYS_NT" ]; then
    WEB_USER=${WEB_USER:-www-data}
    echo "  Setting ownership to $WEB_USER..."
    sudo chown -R $WEB_USER:$WEB_USER storage bootstrap/cache
    sudo chmod -R 775 storage bootstrap/cache
    echo "  ✓ Permissions set"
else
    echo "  ✓ Windows detected — skip chmod (set via file properties)"
fi

# ── 9. Cache optimization ──────────────────────────────────────────────────
echo "▸ [9/10] Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
echo "  ✓ Caches warmed"

# ── 10. Final health check ──────────────────────────────────────────────────
echo "▸ [10/10] Running health checks..."

# Test database connection
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'DB: OK'; } catch(\Exception \$e) { echo 'DB: FAIL - '.\$e->getMessage(); }" 2>/dev/null

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  ✓ Deployment complete!                                     ║"
echo "║                                                              ║"
echo "║  Post-deploy checklist:                                      ║"
echo "║  □ Verify APP_URL in .env matches your real domain           ║"
echo "║  □ Verify MAIL_* credentials are set                         ║"
echo "║  □ Test login at https://your-domain.com/login               ║"
echo "║  □ Check storage/logs/laravel-*.log for errors               ║"
echo "║  □ Set up a cron for scheduled tasks (if any)                ║"
echo "╚══════════════════════════════════════════════════════════════╝"
