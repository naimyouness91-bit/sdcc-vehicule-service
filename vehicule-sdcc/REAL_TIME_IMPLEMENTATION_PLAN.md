# Real-Time Vehicle Management - Implementation Plan
**SDCC Car Reservation Platform**

---

## PHASE 1: Setup Broadcasting Infrastructure

### Step 1.1: Configure Laravel Broadcasting

**File:** `.env`
```
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1

# Or if using Redis (local development)
BROADCAST_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

**File:** `config/broadcasting.php`
```php
'default' => env('BROADCAST_DRIVER', 'pusher'),

'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'useTLS' => true,
    ],
],
```

### Step 1.2: Create Broadcasting Events

**Create:** `app/Events/CarCreated.php`
```php
<?php

namespace App\Events;

use App\Models\Car;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CarCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithBroadcasting, SerializesModels;

    public $car;

    public function __construct(Car $car)
    {
        $this->car = $car;
    }

    public function broadcastOn()
    {
        return new Channel('vehicles');
    }

    public function broadcastAs()
    {
        return 'car.created';
    }

    public function broadcastWith()
    {
        return [
            'car' => [
                'id' => $this->car->id,
                'name' => $this->car->name,
                'matricule' => $this->car->matricule,
                'model' => $this->car->model,
                'year' => $this->car->year,
                'km' => $this->car->km,
                'status' => $this->car->status,
                'availability_type' => $this->car->availability_type,
                'created_at' => $this->car->created_at,
                'updated_at' => $this->car->updated_at,
            ]
        ];
    }
}
```

**Create:** `app/Events/CarUpdated.php`
```php
<?php

namespace App\Events;

use App\Models\Car;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CarUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithBroadcasting, SerializesModels;

    public $car;
    public $changedFields;

    public function __construct(Car $car, $changedFields = null)
    {
        $this->car = $car;
        $this->changedFields = $changedFields;
    }

    public function broadcastOn()
    {
        return new Channel('vehicles');
    }

    public function broadcastAs()
    {
        return 'car.updated';
    }

    public function broadcastWith()
    {
        return [
            'car' => [
                'id' => $this->car->id,
                'name' => $this->car->name,
                'matricule' => $this->car->matricule,
                'model' => $this->car->model,
                'year' => $this->car->year,
                'km' => $this->car->km,
                'status' => $this->car->status,
                'availability_type' => $this->car->availability_type,
                'updated_at' => $this->car->updated_at,
            ],
            'changed_fields' => $this->changedFields,
        ];
    }
}
```

**Create:** `app/Events/CarDeleted.php`
```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class CarDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithBroadcasting;

    public $carId;
    public $carName;

    public function __construct($carId, $carName)
    {
        $this->carId = $carId;
        $this->carName = $carName;
    }

    public function broadcastOn()
    {
        return new Channel('vehicles');
    }

    public function broadcastAs()
    {
        return 'car.deleted';
    }

    public function broadcastWith()
    {
        return [
            'car_id' => $this->carId,
            'car_name' => $this->carName,
        ];
    }
}
```

**Create:** `app/Events/AvailabilityChanged.php`
```php
<?php

namespace App\Events;

use App\Models\Car;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AvailabilityChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithBroadcasting, SerializesModels;

    public $car;
    public $previousStatus;
    public $previousAvailability;

    public function __construct(Car $car, $prevStatus = null, $prevAvail = null)
    {
        $this->car = $car;
        $this->previousStatus = $prevStatus;
        $this->previousAvailability = $prevAvail;
    }

    public function broadcastOn()
    {
        return new Channel('vehicles');
    }

    public function broadcastAs()
    {
        return 'availability.changed';
    }

    public function broadcastWith()
    {
        return [
            'car' => [
                'id' => $this->car->id,
                'name' => $this->car->name,
                'status' => $this->car->status,
                'availability_type' => $this->car->availability_type,
                'updated_at' => $this->car->updated_at,
            ],
            'previous' => [
                'status' => $this->previousStatus,
                'availability_type' => $this->previousAvailability,
            ],
        ];
    }
}
```

---

## PHASE 2: Update CarController for Broadcasting

**File:** `app/Http/Controllers/CarController.php`

### Update store() method
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'matricule' => 'required|string|unique:cars|max:20',
        'model' => 'required|string|max:255',
        'year' => 'required|integer|min:1990|max:' . date('Y'),
        'km' => 'required|integer|min:0',
        'status' => 'required|in:disponible,maintenance',
        'availability_type' => 'required|in:both,weekend,unavailable',
    ]);

    try {
        $car = DB::transaction(function () use ($validated) {
            $car = Car::create($validated);
            
            // Broadcast event to all users
            broadcast(new \App\Events\CarCreated($car))->toOthers();
            
            return $car;
        });

        return response()->json([
            'success' => true,
            'message' => 'Véhicule créé avec succès',
            'car' => $car,
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la création: ' . $e->getMessage(),
        ], 500);
    }
}
```

### Update update() method
```php
public function update(Request $request, Car $car)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'matricule' => 'required|string|unique:cars,matricule,' . $car->id . '|max:20',
        'model' => 'required|string|max:255',
        'year' => 'required|integer|min:1990|max:' . date('Y'),
        'km' => 'required|integer|min:0',
        'status' => 'required|in:disponible,maintenance',
        'availability_type' => 'required|in:both,weekend,unavailable',
    ]);

    try {
        DB::transaction(function () use ($car, $validated) {
            $previousStatus = $car->status;
            $previousAvailability = $car->availability_type;
            
            // Check what changed
            $changedFields = array_keys(array_diff_assoc($validated, $car->only(array_keys($validated))));
            
            $car->update($validated);
            
            // Broadcast appropriate event
            if (in_array('status', $changedFields) || in_array('availability_type', $changedFields)) {
                broadcast(new \App\Events\AvailabilityChanged(
                    $car,
                    $previousStatus,
                    $previousAvailability
                ))->toOthers();
            } else {
                broadcast(new \App\Events\CarUpdated($car, $changedFields))->toOthers();
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Véhicule mis à jour avec succès',
            'car' => $car->fresh(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage(),
        ], 500);
    }
}
```

### Update destroy() method
```php
public function destroy(Car $car)
{
    try {
        $carId = $car->id;
        $carName = $car->name;
        
        DB::transaction(function () use ($car, $carId, $carName) {
            $car->delete();
            
            // Broadcast deletion event
            broadcast(new \App\Events\CarDeleted($carId, $carName))->toOthers();
        });

        return response()->json([
            'success' => true,
            'message' => 'Véhicule supprimé avec succès',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la suppression: ' . $e->getMessage(),
        ], 500);
    }
}
```

### Add updateAvailability() method
```php
public function updateAvailability(Request $request, Car $car)
{
    $validated = $request->validate([
        'status' => 'required|in:disponible,maintenance',
        'availability_type' => 'required|in:both,weekend,unavailable',
    ]);

    try {
        DB::transaction(function () use ($car, $validated) {
            $previousStatus = $car->status;
            $previousAvailability = $car->availability_type;
            
            $car->update($validated);
            
            // Broadcast availability change
            broadcast(new \App\Events\AvailabilityChanged(
                $car,
                $previousStatus,
                $previousAvailability
            ))->toOthers();
        });

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès',
            'car' => $car->fresh(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage(),
        ], 500);
    }
}
```

---

## PHASE 3: Update Routes

**File:** `routes/web.php`

```php
// Existing resource route handles all CRUD
Route::resource('cars', CarController::class)->middleware('auth');

// Add specific route for availability updates
Route::put('/cars/{car}/availability', [CarController::class, 'updateAvailability'])
    ->middleware('auth')
    ->name('cars.updateAvailability');
```

---

## PHASE 4: Setup Client-Side Broadcasting

**File:** `resources/views/layouts/app.blade.php` (add to head or before closing body tag)

```html
<!-- Laravel Echo & Pusher -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>

<script>
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ env("PUSHER_APP_KEY") }}',
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
        encrypted: true,
    });

    console.log('Laravel Echo initialized on cluster: {{ env("PUSHER_APP_CLUSTER") }}');
</script>
```

---

## PHASE 5: Complete Admin Interface JavaScript

**File:** `resources/views/cars/admin-index.blade.php`

Add this JavaScript section at the end (before closing body tag):

```html
<script>
    // Real-time Listeners
    window.Echo.channel('vehicles')
        .listen('.car.created', (event) => {
            console.log('🆕 Vehicle Created:', event.car);
            handleVehicleCreated(event.car);
        })
        .listen('.car.updated', (event) => {
            console.log('✏️ Vehicle Updated:', event.car);
            handleVehicleUpdated(event.car);
        })
        .listen('.car.deleted', (event) => {
            console.log('🗑️ Vehicle Deleted:', event.car_id);
            handleVehicleDeleted(event.car_id);
        })
        .listen('.availability.changed', (event) => {
            console.log('📅 Availability Changed:', event.car);
            handleAvailabilityChanged(event.car);
        });

    // Handler: Vehicle Created
    function handleVehicleCreated(car) {
        const tbody = document.querySelector('table tbody');
        if (!tbody) return;

        // Check if vehicle already exists
        if (document.querySelector(`tr[data-car-id="${car.id}"]`)) {
            return;
        }

        const row = createTableRow(car);
        tbody.insertBefore(row, tbody.firstChild);
        
        updateVehicleStats();
        showToast(`✅ ${car.name} ajouté avec succès`, 'success');
    }

    // Handler: Vehicle Updated
    function handleVehicleUpdated(car) {
        const row = document.querySelector(`tr[data-car-id="${car.id}"]`);
        if (!row) return;

        // Update row data
        row.dataset.updatedAt = car.updated_at;
        updateTableRow(row, car);
        
        showToast(`✏️ ${car.name} mis à jour`, 'success');
    }

    // Handler: Vehicle Deleted
    function handleVehicleDeleted(carId) {
        const row = document.querySelector(`tr[data-car-id="${carId}"]`);
        if (!row) return;

        row.style.opacity = '0.5';
        setTimeout(() => {
            row.remove();
            updateVehicleStats();
            showToast('🗑️ Véhicule supprimé', 'success');
        }, 300);
    }

    // Handler: Availability Changed
    function handleAvailabilityChanged(car) {
        const row = document.querySelector(`tr[data-car-id="${car.id}"]`);
        if (!row) return;

        // Update status badge
        const statusCell = row.querySelector('.status-badge');
        if (statusCell) {
            statusCell.className = `status-badge status-${car.status}`;
            statusCell.innerHTML = car.status === 'disponible' 
                ? '✓ Disponible' 
                : '⚙ Maintenance';
        }

        // Update availability badge
        const availCell = row.querySelector('.availability-badge');
        if (availCell) {
            availCell.className = `availability-badge availability-${car.availability_type}`;
            availCell.innerHTML = getAvailabilityLabel(car.availability_type);
        }

        showToast(`📅 ${car.name} - Statut mis à jour`, 'success');
    }

    // Helper: Create table row
    function createTableRow(car) {
        const row = document.createElement('tr');
        row.dataset.carId = car.id;
        row.dataset.updatedAt = car.updated_at;
        row.innerHTML = `
            <td>
                <div class="vehicle-cell">
                    <div class="vehicle-avatar">🚗</div>
                    <div class="vehicle-info">
                        <div class="vehicle-name">${escapeHtml(car.name)}</div>
                        <div class="vehicle-subtitle">${escapeHtml(car.matricule)}</div>
                    </div>
                </div>
            </td>
            <td>${escapeHtml(car.model)}</td>
            <td>${car.year}</td>
            <td>${car.km.toLocaleString()} km</td>
            <td>
                <span class="status-badge status-${car.status}">
                    ${car.status === 'disponible' ? '✓ Disponible' : '⚙ Maintenance'}
                </span>
            </td>
            <td>
                <span class="availability-badge availability-${car.availability_type}">
                    ${getAvailabilityLabel(car.availability_type)}
                </span>
            </td>
            <td>
                <div class="action-buttons">
                    <button class="action-btn edit" onclick="handleEdit(${car.id})">
                        <i class="fas fa-edit"></i> Modifier
                    </button>
                    <button class="action-btn delete" onclick="handleDeleteClick(${car.id})">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            </td>
        `;
        return row;
    }

    // Helper: Update table row
    function updateTableRow(row, car) {
        const cells = row.querySelectorAll('td');
        
        cells[1].textContent = car.model;
        cells[2].textContent = car.year;
        cells[3].textContent = car.km.toLocaleString() + ' km';
        
        // Update status badge
        const statusBadge = cells[4].querySelector('.status-badge');
        statusBadge.className = `status-badge status-${car.status}`;
        statusBadge.textContent = car.status === 'disponible' ? '✓ Disponible' : '⚙ Maintenance';
        
        // Update availability badge
        const availBadge = cells[5].querySelector('.availability-badge');
        availBadge.className = `availability-badge availability-${car.availability_type}`;
        availBadge.textContent = getAvailabilityLabel(car.availability_type);
    }

    // Helper: Get availability label
    function getAvailabilityLabel(type) {
        const labels = {
            'both': '📅 Toute la semaine',
            'weekend': '☀️ Week-end seulement',
            'unavailable': '❌ Indisponible'
        };
        return labels[type] || 'Inconnu';
    }

    // Helper: Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Helper: Update vehicle stats
    function updateVehicleStats() {
        const rows = document.querySelectorAll('table tbody tr');
        const total = rows.length;
        const available = Array.from(rows).filter(r => 
            r.querySelector('.status-badge').textContent.includes('Disponible')
        ).length;
        const maintenance = total - available;

        document.querySelector('.stat-value:nth-of-type(1)').textContent = total;
        document.querySelector('.stat-value:nth-of-type(2)').textContent = available;
        document.querySelector('.stat-value:nth-of-type(3)').textContent = maintenance;
    }
</script>
```

---

## PHASE 6: Update Employee View Listeners

**File:** `resources/views/cars/employee-index.blade.php`

Add JavaScript listeners:

```html
<script>
    window.Echo.channel('vehicles')
        .listen('.car.created', (event) => {
            console.log('🆕 New vehicle available:', event.car);
            const grid = document.querySelector('.vehicles-grid');
            if (grid && event.car.status === 'disponible' && event.car.availability_type !== 'unavailable') {
                const card = createVehicleCard(event.car);
                grid.appendChild(card);
                showToast('Nouveau véhicule disponible!', 'success');
            }
        })
        .listen('.car.updated', (event) => {
            console.log('✏️ Vehicle updated:', event.car);
            const card = document.querySelector(`[data-car-id="${event.car.id}"]`);
            if (card) {
                updateVehicleCard(card, event.car);
            }
        })
        .listen('.car.deleted', (event) => {
            console.log('🗑️ Vehicle removed:', event.car_id);
            const card = document.querySelector(`[data-car-id="${event.car_id}"]`);
            if (card) {
                card.remove();
            }
        })
        .listen('.availability.changed', (event) => {
            console.log('📅 Availability changed:', event.car);
            const card = document.querySelector(`[data-car-id="${event.car.id}"]`);
            if (!card) return;

            // Hide/show card based on availability
            if (event.car.status === 'maintenance' || event.car.availability_type === 'unavailable') {
                card.style.opacity = '0.5';
                card.querySelector('button').disabled = true;
            } else {
                card.style.opacity = '1';
                card.querySelector('button').disabled = false;
            }
        });

    function createVehicleCard(car) {
        const div = document.createElement('div');
        div.className = 'vehicle-card';
        div.dataset.carId = car.id;
        div.innerHTML = `
            <div class="card-header">
                <span class="car-emoji">🚗</span>
                <span class="car-year">${car.year}</span>
            </div>
            <h3>${escapeHtml(car.name)}</h3>
            <p class="license-plate">${escapeHtml(car.matricule)}</p>
            <p class="car-model">${escapeHtml(car.model)}</p>
            <button class="reserve-btn" onclick="redirectToReservation(${car.id})">Réserver</button>
        `;
        return div;
    }

    function updateVehicleCard(card, car) {
        card.querySelector('h3').textContent = car.name;
        card.querySelector('.license-plate').textContent = car.matricule;
        card.querySelector('.car-model').textContent = car.model;
        card.querySelector('.car-year').textContent = car.year;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function redirectToReservation(carId) {
        window.location.href = `/demandes/create?car_id=${carId}`;
    }

    function showToast(msg, type = 'success') {
        // Toast implementation same as admin view
        console.log(`[${type.toUpperCase()}] ${msg}`);
    }
</script>
```

---

## PHASE 7: Testing Checklist

### Manual Testing

- [ ] **Create Vehicle**
  - [ ] Admin adds vehicle form
  - [ ] Form submits successfully
  - [ ] Table row appears immediately
  - [ ] Employees see card appear
  - [ ] Toast shows success message

- [ ] **Update Vehicle**
  - [ ] Admin clicks edit
  - [ ] Modal populates with current data
  - [ ] Modify fields and submit
  - [ ] Table row updates
  - [ ] Other admins see update
  - [ ] Employees see card update

- [ ] **Delete Vehicle**
  - [ ] Admin clicks delete
  - [ ] Confirmation modal appears
  - [ ] Confirm deletion
  - [ ] Row disappears
  - [ ] Employees' view refreshes
  - [ ] Calendar updates

- [ ] **Status/Availability Change**
  - [ ] Admin changes status to maintenance
  - [ ] Badge updates immediately
  - [ ] Calendar reflects change
  - [ ] Employees can't reserve

### Browser Console Testing

```javascript
// Monitor all events
window.Echo.channel('vehicles').listen('*', (name, data) => {
    console.log(`📡 [${name}]`, data);
});
```

---

## PHASE 8: Production Deployment

### Pre-deployment Checklist

- [ ] All events created and registered
- [ ] CarController updated with broadcasts
- [ ] Routes configured
- [ ] Client-side listeners implemented
- [ ] JavaScript error handling added
- [ ] Database transactions implemented
- [ ] Load testing completed
- [ ] Error logging configured
- [ ] WebSocket connectivity verified
- [ ] CSS classes match JavaScript selectors

### Environment Setup

```bash
# Install dependencies
composer require pusher/pusher-php-server

# Configure .env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=xxx
PUSHER_APP_KEY=xxx
PUSHER_APP_SECRET=xxx
PUSHER_APP_CLUSTER=mt1

# Or use Redis for local development
BROADCAST_DRIVER=redis

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Summary

**Total Implementation Time:** 4-6 hours

**Key Files Modified:**
1. `app/Http/Controllers/CarController.php` - Add broadcasts
2. `app/Events/CarCreated.php` - Create event
3. `app/Events/CarUpdated.php` - Create event
4. `app/Events/CarDeleted.php` - Create event
5. `app/Events/AvailabilityChanged.php` - Create event
6. `routes/web.php` - Add availability route
7. `resources/views/cars/admin-index.blade.php` - Add listeners
8. `resources/views/cars/employee-index.blade.php` - Add listeners
9. `resources/views/layouts/app.blade.php` - Add Echo setup

**Result:** Fully synchronized real-time vehicle management system with instant updates across all user views.
