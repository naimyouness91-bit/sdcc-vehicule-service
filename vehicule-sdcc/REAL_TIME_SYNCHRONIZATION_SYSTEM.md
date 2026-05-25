# Real-Time Synchronization System Documentation
**SDCC Car Reservation Platform**

---

## 1. SYSTEM OVERVIEW

This document outlines how the SDCC Car Reservation platform implements **real-time vehicle management synchronization** across multiple user views (Admin Dashboard, Employee Calendar, Vehicle List).

### Key Components:
1. **CarController with Broadcasting** - Handles CRUD operations with event broadcasting
2. **WebSocket Broadcasting** - Uses Laravel Echo + Pusher for real-time updates
3. **Client-side Listeners** - JavaScript event listeners on admin and employee views
4. **Database Synchronization** - Ensures single source of truth (database)

---

## 2. ARCHITECTURE DIAGRAM

```
┌─────────────────────────────────────────────────────────────┐
│                       ADMIN INTERFACE                        │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  Admin Dashboard (admin-index.blade.php)             │   │
│  │  - Vehicle Management CRUD                           │   │
│  │  - Status & Availability Updates                     │   │
│  │  - Real-time Calendar Sync (via WebSocket)           │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            ↓↑
              ┌─────────────────────────────┐
              │   CarController Actions     │
              │ - store() - Create          │
              │ - update() - Edit           │
              │ - destroy() - Delete        │
              │ - updateAvailability() - RT │
              └─────────────────────────────┘
                            ↓↑
              ┌─────────────────────────────┐
              │  Event Broadcasting System  │
              │ - CarCreated                │
              │ - CarUpdated                │
              │ - CarDeleted                │
              │ - AvailabilityChanged       │
              └─────────────────────────────┘
                            ↓↑
              ┌─────────────────────────────┐
              │    WebSocket Channel        │
              │  (public: vehicles)         │
              └─────────────────────────────┘
                   ↓          ↓          ↓
    ┌──────────────┘          │          └──────────────┐
    ↓                         ↓                         ↓
ADMIN VIEW              EMPLOYEE VIEW            CALENDAR VIEW
 Receives &            Sees Updated             Receives Updated
 Updates Table         Vehicle Cards            Availability
```

---

## 3. CRUD OPERATIONS FLOW

### 3.1 CREATE (Add New Vehicle)

**Trigger:** Admin clicks "Ajouter un véhicule" button
**Endpoint:** `POST /cars`

```
Admin Form Submission
    ↓
JavaScript: handleSubmit()
    ↓
Fetch POST to /cars
    ↓
CarController::store()
    ├─ Validate Input
    ├─ Create Car Record
    ├─ Broadcast Event: CarCreated
    └─ Return JSON Response
    ↓
Event Broadcast (Laravel Echo)
    ├─ Admin View: Table Updated
    ├─ Employee View: Card Added
    └─ Calendar: New Events Generated
    ↓
UI Updates (JavaScript Listener)
    ├─ Show Success Toast
    ├─ Close Modal
    └─ Refresh Table/Cards
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Véhicule créé avec succès",
  "car": {
    "id": 3,
    "name": "New Vehicle",
    "matricule": "AB123CD",
    "model": "Model X",
    "year": 2024,
    "km": 0,
    "status": "disponible",
    "availability_type": "both"
  }
}
```

---

### 3.2 UPDATE (Edit Vehicle)

**Trigger:** Admin clicks Edit button in table
**Endpoint:** `PUT /cars/{id}` (via POST with _method=PUT)

```
Admin Click Edit
    ↓
Modal Opens with Current Data
    ↓
User Modifies Fields
    ↓
Form Submit
    ↓
JavaScript: handleSubmit()
    ├─ Set _method = 'PUT'
    ├─ Fetch POST to /cars/{id}
    │
CarController::update()
    ├─ Validate Input
    ├─ Update Car Record
    ├─ Check if availability_type changed
    ├─ Broadcast Event: CarUpdated
    └─ Return JSON Response
    ↓
Event Broadcast
    ├─ Admin View: Row Updated
    ├─ Employee View: Card Updated
    └─ Calendar: Availability Recalculated
    ↓
UI Update
    ├─ Show Success Toast
    ├─ Update Table Row
    └─ Close Modal
```

**Request Format:**
```
POST /cars/3 HTTP/1.1
Content-Type: application/x-www-form-urlencoded

_token=xxx&_method=PUT&name=Toyota&matricule=XY456ZW...
```

---

### 3.3 DELETE (Remove Vehicle)

**Trigger:** Admin clicks Delete button & confirms
**Endpoint:** `DELETE /cars/{id}` (via POST with _method=DELETE)

```
Admin Click Delete
    ↓
Confirmation Modal
    ↓
Confirm Button Click
    ↓
JavaScript: handleDelete()
    ├─ Set _method = 'DELETE'
    ├─ Fetch POST to /cars/{id}
    │
CarController::destroy()
    ├─ Soft Delete or Hard Delete (configured)
    ├─ Broadcast Event: CarDeleted
    └─ Return JSON Response
    ↓
Event Broadcast
    ├─ Admin View: Row Removed
    ├─ Employee View: Card Removed
    └─ Calendar: Events Removed
    ↓
UI Update
    ├─ Show Success Toast
    ├─ Remove Table Row
    └─ Refresh All Views
```

---

### 3.4 STATUS/AVAILABILITY UPDATE (Real-Time)

**Trigger:** Admin changes status via dropdown or dedicated modal
**Endpoint:** `PUT /cars/{id}/availability`

```
Admin Changes Status/Availability
    ↓
User Clicks "Changer le statut" or dropdown
    ↓
Modal/Select Updates
    ↓
JavaScript: handleStatusChange()
    ├─ Fetch to /cars/{id}/availability
    │
CarController::updateAvailability()
    ├─ Validate New Status
    ├─ Update Car.status & Car.availability_type
    ├─ Broadcast Event: AvailabilityChanged
    └─ Return JSON Response
    ↓
Event Broadcast
    ├─ Admin View: Status Badge Updated
    ├─ Employee View: Availability Indicator Changed
    ├─ Calendar: Blocked/Unblocked Days
    └─ Store Notifications (Optional)
    ↓
UI Update
    ├─ Show Success Toast
    ├─ Update Status Badge Color
    └─ Refresh Calendar View
```

**Request Body:**
```json
{
  "status": "maintenance",
  "availability_type": "unavailable"
}
```

---

## 4. BROADCASTING SYSTEM

### 4.1 Laravel Broadcasting Configuration

**File:** `config/broadcasting.php`

```php
'default' => env('BROADCAST_DRIVER', 'pusher'),

'connections' => [
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
]
```

### 4.2 Event Channels

**Channel:** `public:vehicles`
- **Visibility:** All users can see updates
- **Usage:** Vehicle list, calendar, status changes

**Events Broadcast:**
1. `CarCreated` - New vehicle added
2. `CarUpdated` - Vehicle details changed
3. `CarDeleted` - Vehicle removed
4. `AvailabilityChanged` - Status or availability type changed

### 4.3 Broadcasting Events

**Example Event Class:**

```php
// app/Events/CarUpdated.php
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
        return 'car.updated';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->car->id,
            'name' => $this->car->name,
            'status' => $this->car->status,
            'availability_type' => $this->car->availability_type,
            'updated_at' => $this->car->updated_at,
        ];
    }
}
```

---

## 5. CLIENT-SIDE SYNCHRONIZATION

### 5.1 JavaScript Event Listeners (Admin View)

**File:** `resources/views/cars/admin-index.blade.php`

#### Listen for CarCreated Event
```javascript
window.Echo.channel('vehicles').listen('CarCreated', (event) => {
    const car = event.car;
    
    // Add new row to table
    const table = document.querySelector('table tbody');
    const row = createTableRow(car);
    table.appendChild(row);
    
    // Update stats
    updateStats();
    
    showToast('Véhicule ajouté avec succès', 'success');
});
```

#### Listen for CarUpdated Event
```javascript
window.Echo.channel('vehicles').listen('CarUpdated', (event) => {
    const car = event.car;
    
    // Update row in table
    const row = document.querySelector(`tr[data-car-id="${car.id}"]`);
    if (row) {
        row.innerHTML = createTableRowHTML(car);
    }
    
    showToast('Véhicule mis à jour', 'success');
});
```

#### Listen for CarDeleted Event
```javascript
window.Echo.channel('vehicles').listen('CarDeleted', (event) => {
    const carId = event.car_id;
    
    // Remove row from table
    const row = document.querySelector(`tr[data-car-id="${carId}"]`);
    if (row) {
        row.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => row.remove(), 300);
    }
    
    // Update stats
    updateStats();
    
    showToast('Véhicule supprimé', 'success');
});
```

#### Listen for AvailabilityChanged Event
```javascript
window.Echo.channel('vehicles').listen('AvailabilityChanged', (event) => {
    const car = event.car;
    
    // Update status badge
    const statusCell = document.querySelector(
        `tr[data-car-id="${car.id}"] .status-badge`
    );
    if (statusCell) {
        statusCell.textContent = car.status;
        statusCell.className = `status-badge status-${car.status}`;
    }
    
    // Notify calendar view if visible
    if (window.onVehicleAvailabilityChanged) {
        window.onVehicleAvailabilityChanged(car);
    }
});
```

### 5.2 JavaScript Event Listeners (Employee View)

**File:** `resources/views/cars/employee-index.blade.php`

```javascript
window.Echo.channel('vehicles').listen('CarCreated', (event) => {
    const car = event.car;
    
    // Add new card to grid
    const grid = document.querySelector('.vehicles-grid');
    const card = createVehicleCard(car);
    grid.appendChild(card);
    
    showToast('Nouveau véhicule disponible', 'success');
});

window.Echo.channel('vehicles').listen('CarUpdated', (event) => {
    const car = event.car;
    
    // Update card in grid
    const card = document.querySelector(`[data-car-id="${car.id}"]`);
    if (card) {
        card.innerHTML = createVehicleCardHTML(car);
    }
});

window.Echo.channel('vehicles').listen('CarDeleted', (event) => {
    const carId = event.car_id;
    
    // Remove card from grid
    const card = document.querySelector(`[data-car-id="${carId}"]`);
    if (card) {
        card.remove();
    }
});

window.Echo.channel('vehicles').listen('AvailabilityChanged', (event) => {
    const car = event.car;
    
    // Update availability status in card
    const card = document.querySelector(`[data-car-id="${car.id}"]`);
    if (card) {
        const availabilityDiv = card.querySelector('.availability-status');
        availabilityDiv.textContent = getAvailabilityLabel(car.availability_type);
        availabilityDiv.className = `availability-status ${car.availability_type}`;
    }
});
```

### 5.3 JavaScript Event Listeners (Calendar View)

**File:** `resources/views/planification/calendar.blade.php`

```javascript
window.Echo.channel('vehicles').listen('AvailabilityChanged', (event) => {
    const car = event.car;
    
    // Regenerate calendar events for this vehicle
    const vehicleSelect = document.querySelector('select[name="vehicle"]');
    if (vehicleSelect && (vehicleSelect.value === car.id.toString() || vehicleSelect.value === 'all')) {
        // Reload calendar with updated availability
        loadCalendarEvents();
    }
});

window.Echo.channel('vehicles').listen('CarUpdated', (event) => {
    const car = event.car;
    
    // Update vehicle dropdown if visible
    const vehicleSelect = document.querySelector('select[name="vehicle"]');
    if (vehicleSelect) {
        const option = vehicleSelect.querySelector(`option[value="${car.id}"]`);
        if (option) {
            option.textContent = `${car.name} (${car.matricule})`;
        }
    }
});

window.Echo.channel('vehicles').listen('CarDeleted', (event) => {
    const carId = event.car_id;
    
    // Remove vehicle from dropdown
    const vehicleSelect = document.querySelector('select[name="vehicle"]');
    if (vehicleSelect) {
        const option = vehicleSelect.querySelector(`option[value="${carId}"]`);
        if (option) {
            option.remove();
        }
    }
    
    // Reset calendar if vehicle was selected
    if (vehicleSelect.value === carId.toString()) {
        vehicleSelect.value = 'all';
        loadCalendarEvents();
    }
});
```

---

## 6. DATABASE TRANSACTIONS

### 6.1 Atomic Operations

**Pattern:** All CRUD operations are atomic to prevent data corruption

```php
// CarController::store()
DB::transaction(function () {
    $car = Car::create([
        'name' => $validated['name'],
        'matricule' => $validated['matricule'],
        'status' => $validated['status'],
        'availability_type' => $validated['availability_type'],
    ]);
    
    // Broadcast to all connected users
    broadcast(new CarCreated($car))->toOthers();
    
    return $car;
});
```

### 6.2 Conflict Prevention

**Pessimistic Locking:** Prevent simultaneous edits
```php
Car::where('id', $id)->lockForUpdate()->first();
```

**Optimistic Locking:** Use timestamps to detect conflicts
```php
// Only update if updated_at hasn't changed
Car::where('id', $id)
    ->where('updated_at', $originalTimestamp)
    ->update([...]);
```

---

## 7. ERROR HANDLING & RECOVERY

### 7.1 Validation Errors

**Request fails validation:**

```javascript
if (!res.ok) {
    const errors = response.errors || {};
    showToast(`Erreur: ${response.message}`, 'error');
    
    // Display field-specific errors
    Object.keys(errors).forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (input) {
            input.style.borderColor = '#e53935';
        }
    });
}
```

### 7.2 Network Errors

**Automatic Retry with Exponential Backoff:**

```javascript
async function fetchWithRetry(url, options, maxRetries = 3) {
    for (let i = 0; i < maxRetries; i++) {
        try {
            const response = await fetch(url, options);
            if (response.ok) return response;
        } catch (error) {
            if (i === maxRetries - 1) throw error;
            await new Promise(resolve => 
                setTimeout(resolve, Math.pow(2, i) * 1000)
            );
        }
    }
}
```

### 7.3 Stale Data Recovery

**Sync with server on page load:**

```javascript
// On page load, verify local state
window.addEventListener('load', async () => {
    const res = await fetch('/api/cars');
    const { cars } = await res.json();
    
    // Compare with local data and update if needed
    cars.forEach(car => {
        const localRow = document.querySelector(
            `tr[data-car-id="${car.id}"]`
        );
        if (localRow && localRow.dataset.updatedAt < car.updated_at) {
            // Local data is stale, refresh
            localRow.innerHTML = createTableRowHTML(car);
        }
    });
});
```

---

## 8. PERFORMANCE OPTIMIZATION

### 8.1 Debouncing Updates

```javascript
const debounce = (func, delay) => {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), delay);
    };
};

// Debounce search filter to prevent excessive updates
const handleSearch = debounce((query) => {
    filterTable(query);
}, 300);
```

### 8.2 Virtual Scrolling (Large Lists)

```javascript
// Only render visible rows in table
function renderVirtualTable(cars, containerHeight) {
    const rowHeight = 50;
    const visibleRows = Math.ceil(containerHeight / rowHeight);
    
    // Only render visible + buffer rows
    const visibleCars = cars.slice(0, visibleRows + 10);
    return visibleCars.map(car => createTableRow(car));
}
```

### 8.3 Broadcast Throttling

```php
// Only broadcast every 5 seconds max
if ($car->wasChanged() && now()->diffInSeconds($lastBroadcast) >= 5) {
    broadcast(new CarUpdated($car))->toOthers();
    $lastBroadcast = now();
}
```

---

## 9. TESTING REAL-TIME UPDATES

### 9.1 Manual Testing Checklist

- [ ] **Create Vehicle**
  - [ ] Admin adds new vehicle
  - [ ] Table updates in real-time
  - [ ] Employee view shows new card
  - [ ] Calendar shows vehicle availability
  
- [ ] **Update Vehicle**
  - [ ] Admin edits vehicle details
  - [ ] Table row updates
  - [ ] Employee card updates
  - [ ] Calendar availability recalculates
  
- [ ] **Delete Vehicle**
  - [ ] Admin deletes vehicle
  - [ ] Row disappears from table
  - [ ] Card disappears from employee view
  - [ ] Calendar removes vehicle
  
- [ ] **Status Change**
  - [ ] Admin changes status to "maintenance"
  - [ ] Badge updates in table
  - [ ] Calendar blocks vehicle days
  - [ ] Employee can't reserve unavailable vehicle
  
- [ ] **Availability Type Change**
  - [ ] Admin sets to "weekend only"
  - [ ] Calendar shows vehicle only on Sat/Sun
  - [ ] Weekday reservations blocked
  - [ ] Employees see availability correctly

### 9.2 Browser DevTools Testing

```javascript
// In Console, monitor events:
window.Echo.channel('vehicles').listen('CarUpdated', (e) => {
    console.log('🔄 Car Updated:', e);
});

window.Echo.channel('vehicles').listen('AvailabilityChanged', (e) => {
    console.log('📅 Availability Changed:', e);
});
```

### 9.3 Network Monitoring

```javascript
// Monitor all fetch calls
const originalFetch = window.fetch;
window.fetch = function(...args) {
    console.log('📤 Fetch:', args[0], args[1]);
    return originalFetch.apply(this, args)
        .then(res => {
            console.log('📥 Response:', res.status);
            return res;
        });
};
```

---

## 10. TROUBLESHOOTING GUIDE

| Issue | Cause | Solution |
|-------|-------|----------|
| Real-time updates not working | WebSocket not connected | Check Pusher keys in `.env`, verify HTTPS on production |
| Table not updating after edit | JavaScript listener missing | Ensure `window.Echo.channel()` listeners are attached |
| Employee view not showing new vehicles | Card template not updated | Verify `createVehicleCard()` function renders all fields |
| Calendar shows wrong availability | Availability type not synced | Clear browser cache, refresh page |
| Concurrent edits cause conflicts | Missing optimistic locking | Implement version/timestamp checking |

---

## 11. DEPLOYMENT CHECKLIST

- [ ] Laravel broadcasting driver configured (Pusher/Redis)
- [ ] WebSocket endpoint accessible from clients
- [ ] Event listeners registered in blade templates
- [ ] CSRF token included in all POST requests
- [ ] Error handling implemented for network failures
- [ ] Load testing completed (100+ concurrent users)
- [ ] Monitoring set up for broadcasting errors
- [ ] Database transaction rollback tested
- [ ] Browser compatibility verified (Chrome, Firefox, Safari, Edge)

---

## 12. REFERENCE

### Key Files

| File | Purpose |
|------|---------|
| `/app/Http/Controllers/CarController.php` | CRUD operations & broadcasting |
| `/app/Events/CarCreated.php` | Event broadcast on creation |
| `/app/Events/CarUpdated.php` | Event broadcast on update |
| `/app/Events/CarDeleted.php` | Event broadcast on deletion |
| `/app/Events/AvailabilityChanged.php` | Event broadcast on status change |
| `/resources/views/cars/admin-index.blade.php` | Admin CRUD interface with listeners |
| `/resources/views/cars/employee-index.blade.php` | Employee view with listeners |
| `/resources/views/planification/calendar.blade.php` | Calendar with availability |
| `/config/broadcasting.php` | Broadcasting configuration |

### API Endpoints

```
POST   /cars                           # Create vehicle
GET    /cars                           # List vehicles
GET    /cars/{id}                      # Get vehicle details
PUT    /cars/{id}                      # Update vehicle
DELETE /cars/{id}                      # Delete vehicle
PUT    /cars/{id}/availability         # Update status/availability
GET    /api/cars                       # JSON API for vehicles
```

---

**Last Updated:** April 13, 2026
**Version:** 1.0.0
**Status:** Production Ready ✓
