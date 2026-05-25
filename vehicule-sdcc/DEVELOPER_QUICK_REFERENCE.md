# 🚀 Developer Quick Reference - Production-Ready SDCC Reservation System

**Project:** SDCC Vehicle Reservation System  
**Status:** 🟢 PRODUCTION READY (May 3, 2026)  
**Target:** Quick reference for developers working on this project  

---

## ⚡ QUICK START

### Local Development

```bash
# 1. Setup environment
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# 2. Start development server
php artisan serve

# 3. Run tests
php artisan test

# Visit: http://127.0.0.1:8000/login
# Credentials: superadmin@sdcc.ma / password
```

### Key Ports

| Service | Port | URL |
|---------|------|-----|
| Laravel | 8000 | http://localhost:8000 |
| MySQL | 3306 | localhost |
| Redis | 6379 | localhost |

---

## 📁 PROJECT STRUCTURE REFERENCE

```
app/
  ├── Models/
  │   ├── User.php          ← Auth + roles
  │   ├── Car.php           ← Vehicles
  │   ├── Demande.php       ← Reservations
  │   └── ...
  ├── Http/
  │   ├── Controllers/      ← Business logic
  │   ├── Middleware/       ← Auth, rate limit
  │   ├── Requests/         ← ✨ NEW: FormRequests
  │   └── Kernel.php        ← Middleware config
  ├── Traits/
  │   └── OptimizedQueries.php  ← ✨ NEW: Eager load
  └── Services/
      └── OptionsService.php    ← Shared data

routes/
  ├── web.php               ← Web routes
  ├── auth.php              ← Login/logout
  └── api.php               ← API endpoints

tests/
  ├── Feature/
  │   ├── ReservationFlowTest.php    ← ✨ NEW: CRUD tests
  │   └── SecurityTest.php           ← ✨ NEW: Security tests
  └── Unit/

resources/views/
  ├── layouts/app.blade.php
  ├── auth/
  ├── admin/
  ├── cars/
  └── demandes/

database/
  ├── migrations/           ← 30+ migrations
  └── seeders/              ← Initial data

config/
  ├── auth.php
  ├── database.php
  ├── session.php           ← ✨ Updated: 1440 min
  └── cache.php
```

---

## 🔑 CRITICAL CONFIGURATION

### Environment Variables (.env)

```env
# ✅ MUST BE FALSE for production
APP_DEBUG=false

# ✅ MUST BE 1440 (24 hours)
SESSION_LIFETIME=1440

# ✅ Use MySQL for production
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=reservation_sdcc
DB_USERNAME=sdcc_user
DB_PASSWORD=secure_password

# ✅ Cache config (prod: redis)
CACHE_DRIVER=file  # or redis
```

### .env.production Template

See `PRODUCTION_DEPLOYMENT_COMPLETE.md` for complete template

---

## 🛡️ SECURITY CHECKLIST FOR DEVELOPERS

### When Adding New Routes ✅

```php
// ❌ BAD: No middleware
Route::post('/reservations', [ReservationController::class, 'store']);

// ✅ GOOD: Auth + role
Route::post('/reservations', [ReservationController::class, 'store'])
    ->middleware(['auth', 'role:admin|super_admin']);
```

### When Creating Forms ✅

```php
// ❌ BAD: Inline validation + no authorization
public function store(Request $request) {
    $validated = $request->validate([...]);
}

// ✅ GOOD: FormRequest with authorize()
public function store(StoreReservationRequest $request) {
    // Automatically validated + authorized
    $validated = $request->validated();
}
```

### When Querying Database ✅

```php
// ❌ BAD: N+1 queries
$demandes = Demande::all();
foreach ($demandes as $demande) {
    echo $demande->user->name;  // Query per item!
}

// ✅ GOOD: Eager loading
$demandes = Demande::withOptimizations()->get();
foreach ($demandes as $demande) {
    echo $demande->user->name;  // No extra queries
}
```

### When Assigning Data ✅

```php
// ❌ BAD: Mass assignment
User::create($request->all());

// ✅ GOOD: Explicit fillable
User::create($request->validated());
// or use FormRequest->validated()
```

---

## 🧪 TESTING PATTERNS

### Run All Tests

```bash
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific file
php artisan test tests/Feature/ReservationFlowTest.php

# Run specific test
php artisan test tests/Feature/ReservationFlowTest.php::test_admin_can_create_reservation
```

### Writing Tests

```php
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends TestCase {
    use RefreshDatabase;

    public function test_example() {
        $user = User::factory()->create();
        
        $this->actingAs($user)
            ->post(route('action'), [...])
            ->assertRedirect();
    }
}
```

### Test Database

```bash
# Create test database
mysql -u root
> CREATE DATABASE reservation_sdcc_test;

# Tests use :memory: by default in SQLite
# Configure in phpunit.xml for MySQL
```

---

## 📊 COMMON QUERIES

### Eager Loading (Performance)

```php
// ✅ Use the trait
$demandes = Demande::withOptimizations()->get();

// ✅ Or explicit with()
$demandes = Demande::with(['user', 'car'])->get();

// ❌ Avoid N+1
$demandes = Demande::all();
foreach ($demandes as $d) {
    echo $d->user->name;  // Separate query!
}
```

### Filtering by Role

```php
// Get all admins
$admins = User::role('admin')->get();

// Get all employees (not admin/super_admin)
$employees = User::all()
    ->filter(fn($u) => $u->isEmployee());

// Or check per user
if ($user->isEmployee()) {
    // Employee-specific logic
}
```

### Pagination

```php
// Paginate results (15 per page)
$demandes = Demande::withOptimizations()
    ->paginate(15);

// In view: {{ $demandes->links() }}

// Custom per page
$demandes = Demande::paginate(50);
```

---

## 🐛 DEBUGGING TIPS

### Enable Query Logging

```bash
php artisan tinker
>>> DB::enableQueryLog();
>>> $result = User::all();
>>> dd(DB::getQueryLog());
```

### Check Rate Limiting

```bash
# Test login endpoint
for i in {1..10}; do
  curl -X POST http://localhost:8000/login \
    -d "email=test@test.com&password=test" \
    -b .cookies.txt -c .cookies.txt
done

# 6th request should get 429 (Too Many Requests)
```

### Verify Cache

```bash
php artisan tinker
>>> Cache::get('key')
>>> Cache::put('key', 'value', 300)  // 5 min TTL
>>> Cache::flush()
```

---

## 📝 CODE STYLE GUIDELINES

### PSR-12 Compliance

```php
// ✅ 4 spaces indentation
class User extends Model {
    public function isEmployee(): bool
    {
        return true;
    }
}

// ✅ Type hints on all methods
public function getUser(int $id): ?User
{
    return User::find($id);
}

// ✅ Return types
public function getName(): string
{
    return $this->name;
}
```

### Naming Conventions

| What | Example | Pattern |
|------|---------|---------|
| Classes | `User`, `Car`, `Demande` | PascalCase |
| Methods | `getName()`, `isActive()` | camelCase |
| Variables | `$userName`, `$userId` | camelCase |
| Constants | `STATUS_ACTIVE`, `MAX_USERS` | UPPER_SNAKE_CASE |
| Blade files | `user.blade.php` | snake_case |
| Routes | `users.show`, `cars.edit` | snake_case |

---

## 🔄 GIT WORKFLOW

### Commit Message Format

```
[type]: Brief description

Detailed explanation if needed.
- Point 1
- Point 2

References: #123
```

**Types:** feat, fix, docs, style, refactor, test, chore

### Feature Branch

```bash
# Create feature branch
git checkout -b feature/add-email-notifications

# Commit changes
git add .
git commit -m "feat: add email notification system"

# Push and create PR
git push origin feature/add-email-notifications
```

---

## ⚙️ COMMON TASKS

### Add New Model

```bash
# Generate model + migration
php artisan make:model ClassName -m

# In migration
Schema::create('table_name', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});
```

### Add New Route

```php
// In routes/web.php
Route::get('/endpoint', [Controller::class, 'action'])
    ->middleware(['auth', 'role:admin'])
    ->name('route.name');
```

### Add New Controller Action

```bash
php artisan make:controller ClassName

# Then in controller:
public function index()
{
    $items = Model::with(['relation'])->get();
    return view('items.index', compact('items'));
}
```

### Create Migration

```bash
php artisan make:migration create_table_name
php artisan migrate
```

---

## 📦 DEPENDENCY MANAGEMENT

### Composer

```bash
# Install dependencies
composer install

# Add new package
composer require laravel/package-name

# Update all
composer update

# Production: no dev dependencies
composer install --optimize-autoloader --no-dev
```

### NPM

```bash
# Install frontend dependencies
npm install

# Build assets for production
npm run build

# Watch for development
npm run watch
```

---

## 🚀 DEPLOYMENT CHECKLIST

### Before Pushing to Production

```bash
# 1. Run tests
php artisan test --parallel

# 2. Check for errors
php artisan code:analyze  # if using analyzer

# 3. Generate config cache
php artisan config:cache

# 4. Verify .env is production
grep APP_DEBUG .env  # Should be false

# 5. Check git is clean
git status  # Should be clean
```

### After Deploying

```bash
# 1. Verify migrations ran
php artisan migrate:status

# 2. Clear caches
php artisan optimize:clear
php artisan config:cache

# 3. Check logs
tail -f storage/logs/laravel.log

# 4. Test critical flows manually
```

---

## 📚 DOCUMENTATION FILES

| Document | Use For |
|----------|---------|
| `CHANGES_SUMMARY.md` | What was changed and why |
| `PRODUCTION_DEPLOYMENT_COMPLETE.md` | Step-by-step deployment |
| `PRODUCTION_READINESS_CHECKLIST.md` | Pre-production verification |
| `INTEGRATION_DOCUMENTATION.md` | API & workflow reference |
| `README.md` | Quick start |

---

## 🆘 TROUBLESHOOTING

### "Undefined method isEmployee()"

```php
// ✅ Fix: Method is now in User.php
if ($user->isEmployee()) { }
```

### "Too many database queries"

```php
// ❌ Before
$demandes = Demande::all();

// ✅ After
$demandes = Demande::withOptimizations()->get();
```

### "Login rate limited"

```bash
# ✅ This is expected after 5 failed attempts
# Wait 1 minute or use different IP
```

### "Session expires quickly"

```env
# ✅ Should be 1440 (24 hours)
SESSION_LIFETIME=1440
```

---

## 💡 PRODUCTION BEST PRACTICES

### ✅ DO

- [x] Use eager loading (withOptimizations)
- [x] Use FormRequests for validation
- [x] Add rate limiting to sensitive endpoints
- [x] Log important events (not debug info)
- [x] Validate all user input
- [x] Use HTTPS in production
- [x] Keep APP_DEBUG=false in production
- [x] Write tests for critical features

### ❌ DON'T

- [ ] Use dd() or dump() in production code
- [ ] Hardcode credentials
- [ ] Use $request->all() without validation
- [ ] Query without eager loading
- [ ] Leave APP_DEBUG=true
- [ ] Skip rate limiting on auth endpoints
- [ ] Ignore error logs
- [ ] Deploy without testing

---

## 📞 KEY CONTACTS & REFERENCES

**Documentation:**
- [PRODUCTION_DEPLOYMENT_COMPLETE.md](PRODUCTION_DEPLOYMENT_COMPLETE.md)
- [PRODUCTION_READINESS_CHECKLIST.md](PRODUCTION_READINESS_CHECKLIST.md)
- [INTEGRATION_DOCUMENTATION.md](INTEGRATION_DOCUMENTATION.md)

**Code References:**
- Models: `app/Models/`
- Controllers: `app/Http/Controllers/`
- Routes: `routes/web.php`, `routes/auth.php`
- Tests: `tests/Feature/`

---

**Last Updated:** May 3, 2026  
**Project Status:** 🟢 PRODUCTION READY  
**Maintainer:** Development Team  

This is a living document. Update it as new patterns emerge and best practices evolve.
