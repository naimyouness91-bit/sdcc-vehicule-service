# Critical Fixes - Implementation Guide

**Priority:** CRITICAL  
**Estimated Time:** 2-3 hours  
**Impact:** High (Database connectivity, Security, Performance)

---

## FIX #1: SQLite Database Path Error

### Problem
```
Database file at path [C:\\Users\\PC\\...\\database.sqlite] does not exist.
```

### Root Cause
`.env` uses absolute path instead of Laravel's `database_path()` helper

### Solution

**Step 1: Update .env**

```bash
# BEFORE (WRONG):
DB_CONNECTION=sqlite
DB_DATABASE=C:\\Users\\PC\\Desktop\\projet-sdcc\\Reservation-Vehicule-Service\\vehicule-sdcc\\database\\database.sqlite

# AFTER (CORRECT):
DB_CONNECTION=sqlite
DB_DATABASE=database.sqlite
```

**Step 2: Update .env.production**

```bash
DB_CONNECTION=sqlite
DB_DATABASE=database.sqlite
```

**Step 3: Update config/database.php (if needed)**

```php
'sqlite' => [
    'driver' => 'sqlite',
    'url' => env('DATABASE_URL'),
    'database' => env('DB_DATABASE', database_path('database.sqlite')),
    'prefix' => '',
    'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
],
```

**Step 4: Create the database file if missing**

```bash
# Ensure directory exists
mkdir -p database

# Create empty database.sqlite
touch database/database.sqlite

# Run migrations
php artisan migrate
```

**Step 5: Run migrations (if not already done)**

```bash
php artisan migrate:fresh --seed
```

### Verification

```bash
# Test database connection
php artisan tinker
> DB::connection()->getPdo()
# Should not throw exception
```

---

## FIX #2: Email Domain Validation - Make Configurable

### Problem
```php
// routes/auth.php (Line 18)
if (!str_ends_with($credentials['email'], '@sdcc.ma')) {
    throw ValidationException::withMessages(['email' => '...']);
}
```

**Issues:**
1. Hardcoded in code
2. Can't change per environment
3. Not reusable

### Solution

**Step 1: Create Config Entry**

Create `config/auth.php` section:

```php
// config/auth.php
return [
    // ... existing config ...

    'email_domain' => env('AUTH_EMAIL_DOMAIN', '@sdcc.ma'),
];
```

**Step 2: Update .env Files**

```env
# .env
AUTH_EMAIL_DOMAIN=@sdcc.ma

# .env.production
AUTH_EMAIL_DOMAIN=@sdcc.ma

# .env.testing
AUTH_EMAIL_DOMAIN=@test.local
```

**Step 3: Create Custom Validation Rule**

Create file: `app/Rules/SdccEmailRule.php`

```php
<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SdccEmailRule implements Rule
{
    private string $domain;

    public function __construct(?string $domain = null)
    {
        $this->domain = $domain ?? config('auth.email_domain');
    }

    public function passes($attribute, $value): bool
    {
        // Validate email format
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Validate domain
        return str_ends_with($value, $this->domain);
    }

    public function message(): string
    {
        return 'Email must be a valid ' . $this->domain . ' address';
    }
}
```

**Step 4: Update routes/auth.php**

```php
// BEFORE (WRONG):
if (!str_ends_with($credentials['email'], '@sdcc.ma')) {
    throw ValidationException::withMessages([
        'email' => 'Only @sdcc.ma email addresses are allowed',
    ]);
}

// AFTER (CORRECT):
use App\Rules\SdccEmailRule;

$validator = Validator::make($credentials, [
    'email' => ['required', new SdccEmailRule()],
    'password' => ['required'],
]);

if ($validator->fails()) {
    throw ValidationException::withMessages($validator->errors()->all());
}
```

**Step 5: Use in Form Requests**

Update all form requests that validate email:

```php
// app/Http/Requests/StoreUserRequest.php
use App\Rules\SdccEmailRule;

public function rules(): array
{
    return [
        'email' => ['required', 'unique:users', new SdccEmailRule()],
        // ... other rules
    ];
}
```

### Verification

```bash
# Test with wrong domain
echo "Wrong domain test" > /dev/null  # email=user@gmail.com

# Should fail validation

# Test with correct domain
echo "Correct domain test"  # email=user@sdcc.ma

# Should pass
```

---

## FIX #3: Add Soft Deletes to Critical Models

### Problem
Hard deletes permanently remove data, making audit trails impossible

### Solution

**Step 1: Create Migration**

```bash
php artisan make:migration add_soft_deletes_to_users_table
php artisan make:migration add_soft_deletes_to_cars_table
php artisan make:migration add_soft_deletes_to_demandes_table
```

**Step 2: Update Migrations**

`database/migrations/XXXX_add_soft_deletes_to_users_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();  // Adds deleted_at column
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
```

Repeat for `cars` and `demandes` tables.

**Step 3: Update Models**

`app/Models/User.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes;  // Add this trait

    protected $dates = ['deleted_at'];  // Or use casts in newer Laravel
    
    // Or in Laravel 11+:
    protected $casts = [
        'deleted_at' => 'datetime',
    ];
}
```

Repeat for `Car` and `Demande` models.

**Step 4: Run Migration**

```bash
php artisan migrate
```

**Step 5: Update Queries (if needed)**

```php
// Now queries automatically exclude deleted records:
User::all();  // Excludes soft-deleted

// To include soft-deleted:
User::withTrashed()->get();

// Only soft-deleted:
User::onlyTrashed()->get();

// Restore:
$user->restore();

// Force delete:
$user->forceDelete();
```

### Verification

```bash
# Test in tinker
php artisan tinker

# Create and soft delete user
$user = User::first();
$user->delete();  // Soft delete
$user->is_active;  // Still exists
User::all()->count();  // Doesn't include deleted
User::withTrashed()->count();  // Includes deleted
```

---

## FIX #4: Add Eager Loading to Controllers (Fix N+1 Queries)

### Problem
```php
// Query 1: Load reservations
$reservations = Demande::all();

// Query N: For each reservation, load user and car
@foreach($reservations as $res)
    {{ $res->user->name }}  // 1 query per reservation
    {{ $res->car->name }}   // 1 query per reservation
@endforeach

// Total: 1 + (2 * 100) = 201 queries!
```

### Solution

**Issue Location #1: AdminDataManagementController::loadReservationsTab**

```php
// BEFORE (WRONG - N+1):
private function loadReservationsTab()
{
    $reservations = Demande::all();
    // Later in view: access $res->user, $res->car (N+1)
}

// AFTER (CORRECT):
private function loadReservationsTab()
{
    $reservations = Demande::with(['user', 'car'])  // Eager load
        ->paginate(20);  // Add pagination
    
    return view('admin.reservations', [
        'reservations' => $reservations,
    ]);
}
```

**Issue Location #2: MesDemandesController::index**

```php
// BEFORE:
$demandes = Demande::where('user_id', auth()->id())->get();

// AFTER:
$demandes = Demande::with(['car', 'user'])
    ->where('user_id', auth()->id())
    ->latest()
    ->paginate(15);
```

**Issue Location #3: CarController::available (Zone filtering)**

```php
// BEFORE:
if ($user->isEmployee()) {
    $zone = $user->zone;  // N queries if multiple users
    $zoneVehicleIds = $zone->cars()->pluck('id');  // Another query
}

// AFTER:
if ($user->isEmployee()) {
    $zone = $user->load('zone')->zone;  // Load once
    $cars = $zone->load('cars')->cars;  // Already loaded
}

// Or better, in controller:
$users = User::with(['zone.cars'])->get();
```

### Complete Fix Example

File: `app/Http/Controllers/Admin/AdminDataManagementController.php`

```php
private function loadReservationsTab()
{
    // OLD (N+1 queries):
    // $reservations = Demande::all();

    // NEW (Optimized):
    $reservations = Demande::with([
        'user:id,name,email',  // Specific columns
        'car:id,name,matricule',
    ])
    ->select('id', 'user_id', 'car_id', 'status', 'start_date', 'end_date')  // Specific columns
    ->latest('created_at')
    ->paginate(20);

    return view('admin.tabs.reservations', compact('reservations'));
}

private function loadRequestsTab()
{
    // OLD (N+1):
    // $demandes = Demande::all();

    // NEW (Optimized):
    $demandes = Demande::with([
        'user:id,name,email',
        'car:id,name',
    ])
    ->whereIn('status', ['pending', 'approved'])
    ->latest()
    ->paginate(20);

    return view('admin.tabs.requests', compact('demandes'));
}

private function loadVehiclesTab()
{
    // OLD (N+1):
    // $cars = Car::all();

    // NEW (Optimized):
    $cars = Car::with('zone:id,name')  // Load zone info
        ->select('id', 'name', 'matricule', 'is_active', 'planning_zone_id')
        ->paginate(20);

    return view('admin.tabs.vehicles', compact('cars'));
}
```

### Verification (Debugbar/Telescope)

Install Debugbar:
```bash
composer require barryvdh/laravel-debugbar --dev
```

Check Query Count:
```bash
# Before: 201 queries
# After: 4 queries
```

---

## FIX #5: Add Pagination to List Views

### Problem
All records loaded into memory, causing:
- High memory usage
- Slow page loads
- Poor user experience

### Solution

**All list views should use `paginate()`:**

**Controllers to Update:**

1. **AdminDataManagementController** (already fixed in #4)

2. **CarController::index**

```php
// BEFORE:
public function index()
{
    $cars = Car::all();
    return view('cars.index', compact('cars'));
}

// AFTER:
public function index()
{
    $cars = Car::with('zone')
        ->paginate(15);
    return view('cars.index', compact('cars'));
}
```

3. **ZoneController::index**

```php
// BEFORE:
public function index()
{
    $zones = PlanningZone::with('cars')->get();
    return view('zones.index', compact('zones'));
}

// AFTER:
public function index()
{
    $zones = PlanningZone::with('cars')
        ->paginate(20);
    return view('zones.index', compact('zones'));
}
```

4. **UtilisateursController::index**

```php
// AFTER:
public function index()
{
    $users = User::with('roles')
        ->latest()
        ->paginate(25);
    return view('utilisateurs.index', compact('users'));
}
```

**Views - Add pagination links:**

```blade
{{-- resources/views/cars/index.blade.php --}}

<div class="cars-grid">
    @foreach($cars as $car)
        <div class="car-card">
            {{ $car->name }}
        </div>
    @endforeach
</div>

{{-- Add pagination links --}}
<div class="pagination-wrapper">
    {{ $cars->links() }}  {{-- Bootstrap/Tailwind pagination --}}
</div>
```

---

## Implementation Checklist

- [ ] Fix 1: Update .env database path
- [ ] Fix 1: Run migrations
- [ ] Fix 2: Create config entry for email domain
- [ ] Fix 2: Create SdccEmailRule validation rule
- [ ] Fix 2: Update routes/auth.php
- [ ] Fix 3: Create soft delete migrations
- [ ] Fix 3: Add SoftDeletes trait to models
- [ ] Fix 3: Run new migrations
- [ ] Fix 4: Add eager loading with() to 5+ controllers
- [ ] Fix 4: Add pagination paginate() to list queries
- [ ] Fix 5: Update views with pagination links
- [ ] Test all functionality end-to-end
- [ ] Clear caches: `php artisan optimize:clear`
- [ ] Run tests: `php artisan test`

---

## Testing Commands

```bash
# Test database connection
php artisan tinker
> DB::connection()->getPdo()

# Test migrations
php artisan migrate:status

# Test controllers
php artisan test --filter=AdminDataManagementControllerTest

# Check query counts
# Use Laravel Debugbar to see query count before/after

# Verify soft deletes
php artisan tinker
> $user = User::first();
> $user->delete();
> User::all()->count();
> User::withTrashed()->count();
```

---

## Rollback Instructions

If something goes wrong:

```bash
# Rollback soft delete migrations
php artisan migrate:rollback --step=3

# Revert config changes
git checkout config/auth.php

# Revert controller changes
git checkout app/Http/Controllers/

# Clear all caches
php artisan optimize:clear

# Run migrations again
php artisan migrate
```

---

**Estimated Total Time:** 2-3 hours  
**Impact:** High  
**Risk:** Low (backward compatible)

---

**Document Version:** 1.0  
**Last Updated:** May 4, 2026
