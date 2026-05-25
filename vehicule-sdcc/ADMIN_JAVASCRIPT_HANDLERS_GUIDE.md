# Admin Interface JavaScript Handlers - Complete Guide
**SDCC Car Reservation - Vehicle Management UI**

---

## Overview

The admin vehicle management interface (`resources/views/cars/admin-index.blade.php`) requires comprehensive JavaScript handlers for CRUD operations with real-time synchronization. This guide provides complete implementation for all handlers.

---

## 1. FORM HANDLING

### 1.1 Vehicle Form Submission Handler

**Location:** Bottom of `admin-index.blade.php` in `<script>` section

```javascript
// FORM SUBMISSION HANDLER
document.getElementById('vehicleForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const btn = document.getElementById('submitBtn');
    const originalText = btn.innerHTML;
    btn.classList.add('loading');
    btn.disabled = true;

    try {
        const formData = new FormData(e.target);
        
        // Set correct HTTP method for updates
        if (currentEditId) {
            formData.set('_method', 'PUT');
        }

        // Debug logging
        console.log('[FORM SUBMIT]', {
            action: currentEditId ? 'UPDATE' : 'CREATE',
            vehicleId: currentEditId,
            name: formData.get('name'),
            matricule: formData.get('matricule'),
            status: formData.get('status'),
            availability: formData.get('availability_type'),
        });

        // Determine endpoint
        const endpoint = currentEditId ? `/cars/${currentEditId}` : '/cars';
        
        // Send request
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Une erreur est survenue');
        }

        // Success
        console.log('[SUCCESS]', result);
        showToast(result.message, 'success');
        
        // Close modal and refresh
        closeModal('vehicleModal');
        
        // Wait for broadcast event to update UI
        setTimeout(() => {
            // If no broadcast (offline mode), update manually
            if (!window.Echo) {
                if (currentEditId) {
                    handleVehicleUpdated(result.car);
                } else {
                    handleVehicleCreated(result.car);
                }
            }
        }, 500);

    } catch (error) {
        console.error('[ERROR]', error);
        showToast(error.message || 'Erreur lors du traitement', 'error');
    } finally {
        btn.classList.remove('loading');
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});
```

---

## 2. EDIT HANDLER

### 2.1 Edit Button Click Handler

```javascript
function handleEdit(carId) {
    console.log('[EDIT] Opening modal for car:', carId);
    
    // Fetch current vehicle data
    fetch(`/api/cars/${carId}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('Erreur lors du chargement');
        return res.json();
    })
    .then(data => {
        const car = data.car;
        
        // Set form ID for update tracking
        currentEditId = car.id;
        
        // Populate form fields
        document.getElementById('vehicleForm').reset();
        document.getElementById('modalTitle').textContent = '✏️ Modifier un véhicule';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Enregistrer';
        
        // Fill in form values
        document.querySelector('input[name="name"]').value = car.name;
        document.querySelector('input[name="matricule"]').value = car.matricule;
        document.querySelector('input[name="model"]').value = car.model;
        document.querySelector('input[name="year"]').value = car.year;
        document.querySelector('input[name="km"]').value = car.km;
        document.querySelector('select[name="status"]').value = car.status;
        document.querySelector('select[name="availability_type"]').value = car.availability_type;
        
        // Trigger availability description update
        document.getElementById('availabilityTypeSelect').dispatchEvent(new Event('change'));
        
        // Open modal
        openModal('vehicleModal');
        
        console.log('[EDIT] Form populated with car data:', car);
    })
    .catch(error => {
        console.error('[EDIT ERROR]', error);
        showToast('Impossible de charger le véhicule', 'error');
    });
}
```

### 2.2 API Endpoint (Backend)

**File:** `app/Http/Controllers/CarController.php`

Add a method to return JSON data:

```php
public function show(Car $car)
{
    return response()->json([
        'car' => $car,
    ]);
}
```

Or create an API route:

**File:** `routes/api.php`

```php
Route::get('/cars/{car}', [CarController::class, 'show']);
```

---

## 3. DELETE HANDLER

### 3.1 Delete Click Handler

```javascript
function handleDeleteClick(carId) {
    console.log('[DELETE] Requesting confirmation for car:', carId);
    
    // Store car ID for confirmation
    currentDeleteId = carId;
    
    // Get car name for display
    const row = document.querySelector(`tr[data-car-id="${carId}"]`);
    const carName = row ? row.querySelector('.vehicle-name').textContent : 'Véhicule';
    
    // Update modal text
    const modalBody = document.querySelector('#deleteModal .modal-body');
    modalBody.innerHTML = `
        <p style="color: #666; line-height: 1.6;">
            Êtes-vous sûr de vouloir supprimer <strong>${escapeHtml(carName)}</strong> ? 
            Cette action ne peut pas être annulée.
        </p>
    `;
    
    // Open confirmation modal
    openModal('deleteModal');
}

// Confirm delete button
document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
    if (!currentDeleteId) return;
    
    const btn = document.getElementById('confirmDeleteBtn');
    btn.classList.add('loading');
    btn.disabled = true;

    try {
        console.log('[DELETE] Sending delete request for car:', currentDeleteId);
        
        const response = await fetch(`/cars/${currentDeleteId}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                _method: 'DELETE',
                _token: CSRF,
            })
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Erreur lors de la suppression');
        }

        console.log('[DELETE] Success:', result);
        showToast(result.message, 'success');
        
        closeModal('deleteModal');
        
        // Wait for broadcast event
        setTimeout(() => {
            if (!window.Echo) {
                handleVehicleDeleted(currentDeleteId);
            }
            currentDeleteId = null;
        }, 500);

    } catch (error) {
        console.error('[DELETE ERROR]', error);
        showToast(error.message || 'Erreur lors de la suppression', 'error');
    } finally {
        btn.classList.remove('loading');
        btn.disabled = false;
    }
});
```

### 3.2 Delete Form Data (Alternative Method)

If using form submission instead of JSON:

```javascript
document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
    if (!currentDeleteId) return;
    
    const formData = new FormData();
    formData.append('_method', 'DELETE');
    formData.append('_token', CSRF);
    
    // ... rest of handler
});
```

---

## 4. STATUS UPDATE HANDLER

### 4.1 Status Change Modal Handler

```javascript
// Status change button in table row
function handleStatusChange(carId) {
    console.log('[STATUS] Opening status modal for car:', carId);
    
    currentStatusId = carId;
    
    // Get current status from table
    const row = document.querySelector(`tr[data-car-id="${carId}"]`);
    if (row) {
        const currentStatus = row.querySelector('.status-badge').textContent.toLowerCase();
        const statusSelect = document.getElementById('newStatus');
        
        // Set current status
        if (currentStatus.includes('disponible')) {
            statusSelect.value = 'disponible';
        } else {
            statusSelect.value = 'maintenance';
        }
    }
    
    openModal('statusModal');
}

// Confirm status change
document.getElementById('confirmStatusBtn').addEventListener('click', async () => {
    if (!currentStatusId) return;
    
    const btn = document.getElementById('confirmStatusBtn');
    const newStatus = document.getElementById('newStatus').value;
    
    if (!newStatus) {
        showToast('Veuillez sélectionner un statut', 'error');
        return;
    }
    
    btn.classList.add('loading');
    btn.disabled = true;

    try {
        console.log('[STATUS] Updating status to:', newStatus);
        
        const response = await fetch(`/cars/${currentStatusId}/availability`, {
            method: 'PUT',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                status: newStatus,
                availability_type: 'both', // Keep current availability
            })
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Erreur lors de la mise à jour');
        }

        console.log('[STATUS] Success:', result);
        showToast('Statut mis à jour avec succès', 'success');
        
        closeModal('statusModal');
        
        // Update UI
        setTimeout(() => {
            if (!window.Echo) {
                handleAvailabilityChanged(result.car);
            }
            currentStatusId = null;
        }, 500);

    } catch (error) {
        console.error('[STATUS ERROR]', error);
        showToast(error.message || 'Erreur lors de la mise à jour', 'error');
    } finally {
        btn.classList.remove('loading');
        btn.disabled = false;
    }
});
```

---

## 5. SEARCH & FILTER HANDLERS

### 5.1 Search Handler

```javascript
const searchInput = document.querySelector('.search-box input');

const handleSearch = debounce((query) => {
    console.log('[SEARCH] Query:', query);
    
    const rows = document.querySelectorAll('table tbody tr');
    const queryLower = query.toLowerCase();
    
    rows.forEach(row => {
        const name = row.querySelector('.vehicle-name').textContent.toLowerCase();
        const matricule = row.querySelector('.vehicle-subtitle').textContent.toLowerCase();
        const model = row.cells[1].textContent.toLowerCase();
        
        const matches = name.includes(queryLower) || 
                       matricule.includes(queryLower) || 
                       model.includes(queryLower);
        
        row.classList.toggle('hidden-row', !matches);
    });
    
    // Show empty state if no results
    const visibleRows = document.querySelectorAll('table tbody tr:not(.hidden-row)');
    const emptyState = document.querySelector('.empty-state');
    
    if (visibleRows.length === 0 && emptyRows.length > 0) {
        if (!emptyState) {
            const empty = document.createElement('tr');
            empty.className = 'empty-state';
            empty.innerHTML = `
                <td colspan="7" style="text-align: center; padding: 40px;">
                    <i class="fas fa-search" style="font-size: 32px; opacity: 0.3; margin-bottom: 16px; display: block;"></i>
                    <p style="color: var(--text-light);">Aucun véhicule trouvé</p>
                </td>
            `;
            document.querySelector('table tbody').appendChild(empty);
        }
    } else if (emptyState) {
        emptyState.remove();
    }
}, 300);

if (searchInput) {
    searchInput.addEventListener('input', (e) => handleSearch(e.target.value));
}

// Debounce helper
function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func(...args), delay);
    };
}
```

### 5.2 Filter Button Handler

```javascript
const filterButtons = document.querySelectorAll('.filter-btn');

filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        // Remove active class from all buttons
        filterButtons.forEach(b => b.classList.remove('active'));
        
        // Add active to clicked button
        btn.classList.add('active');
        
        const filter = btn.dataset.filter; // data-filter="all|disponible|maintenance"
        
        console.log('[FILTER]', filter);
        
        const rows = document.querySelectorAll('table tbody tr');
        rows.forEach(row => {
            if (filter === 'all') {
                row.classList.remove('hidden-row');
            } else {
                const status = row.querySelector('.status-badge').textContent.toLowerCase();
                const matches = status.includes(filter);
                row.classList.toggle('hidden-row', !matches);
            }
        });
    });
});
```

---

## 6. ACTION MENU HANDLERS

### 6.1 Dropdown Menu Toggle

```javascript
const actionTriggers = document.querySelectorAll('.action-trigger');

actionTriggers.forEach(trigger => {
    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        
        const dropdown = trigger.nextElementSibling;
        const isOpen = dropdown.classList.contains('show');
        
        // Close all other dropdowns
        document.querySelectorAll('.action-dropdown').forEach(d => {
            d.classList.remove('show');
        });
        
        // Toggle current dropdown
        if (!isOpen) {
            dropdown.classList.add('show');
        }
    });
});

// Close dropdowns when clicking outside
document.addEventListener('click', () => {
    document.querySelectorAll('.action-dropdown').forEach(d => {
        d.classList.remove('show');
    });
});
```

### 6.2 Action Menu Items

```javascript
// Edit via menu
document.addEventListener('click', (e) => {
    if (e.target.closest('[data-action="edit"]')) {
        const carId = e.target.closest('tr').dataset.carId;
        handleEdit(carId);
    }
});

// Delete via menu
document.addEventListener('click', (e) => {
    if (e.target.closest('[data-action="delete"]')) {
        const carId = e.target.closest('tr').dataset.carId;
        handleDeleteClick(carId);
    }
});

// Status via menu
document.addEventListener('click', (e) => {
    if (e.target.closest('[data-action="status"]')) {
        const carId = e.target.closest('tr').dataset.carId;
        handleStatusChange(carId);
    }
});
```

---

## 7. AVAILABILITY TYPE DESCRIPTION

### 7.1 Availability Type Change Handler

```javascript
const availabilityDescriptions = {
    both: {
        title: '📅 Disponible toute la semaine',
        helper: 'Les clients peuvent réserver ce véhicule tous les jours (lundi au dimanche).',
        icon: 'fas fa-calendar'
    },
    weekend: {
        title: '☀️ Disponible week-end uniquement',
        helper: 'Les clients ne peuvent réserver ce véhicule que le samedi et dimanche.',
        icon: 'fas fa-sun'
    },
    unavailable: {
        title: '❌ Véhicule indisponible',
        helper: '<i class="fas fa-exclamation-triangle" style="color: #e53935; margin-right: 4px;"></i>Ce véhicule ne peut PAS être réservé du tout.',
        icon: 'fas fa-ban'
    }
};

document.getElementById('availabilityTypeSelect').addEventListener('change', (e) => {
    const type = e.target.value;
    const desc = availabilityDescriptions[type] || availabilityDescriptions.both;
    
    document.getElementById('availabilityDescription').textContent = desc.title;
    document.getElementById('availabilityHelper').innerHTML = desc.helper;
    
    console.log('[AVAILABILITY] Changed to:', type, desc);
});
```

---

## 8. REAL-TIME BROADCAST LISTENERS

### 8.1 Vehicle Created Listener

```javascript
window.Echo?.channel('vehicles').listen('car.created', (event) => {
    console.log('🆕 [BROADCAST] Car Created:', event);
    handleVehicleCreated(event.car);
});

function handleVehicleCreated(car) {
    // Check if already exists
    if (document.querySelector(`tr[data-car-id="${car.id}"]`)) {
        return;
    }

    const tbody = document.querySelector('table tbody');
    if (!tbody) return;

    const row = createTableRow(car);
    tbody.insertBefore(row, tbody.firstChild);
    
    updateVehicleStats();
    showToast(`✅ ${escapeHtml(car.name)} ajouté avec succès`, 'success');
}
```

### 8.2 Vehicle Updated Listener

```javascript
window.Echo?.channel('vehicles').listen('car.updated', (event) => {
    console.log('✏️ [BROADCAST] Car Updated:', event);
    handleVehicleUpdated(event.car);
});

function handleVehicleUpdated(car) {
    const row = document.querySelector(`tr[data-car-id="${car.id}"]`);
    if (!row) return;

    row.dataset.updatedAt = car.updated_at;
    updateTableRow(row, car);
    
    showToast(`✏️ ${escapeHtml(car.name)} mis à jour`, 'success');
}
```

### 8.3 Vehicle Deleted Listener

```javascript
window.Echo?.channel('vehicles').listen('car.deleted', (event) => {
    console.log('🗑️ [BROADCAST] Car Deleted:', event);
    handleVehicleDeleted(event.car_id);
});

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
```

### 8.4 Availability Changed Listener

```javascript
window.Echo?.channel('vehicles').listen('availability.changed', (event) => {
    console.log('📅 [BROADCAST] Availability Changed:', event);
    handleAvailabilityChanged(event.car);
});

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

    showToast(`📅 ${escapeHtml(car.name)} - Statut mis à jour`, 'success');
}
```

---

## 9. UTILITY FUNCTIONS

### 9.1 Table Row Creation

```javascript
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
```

### 9.2 Table Row Update

```javascript
function updateTableRow(row, car) {
    const cells = row.querySelectorAll('td');
    
    // Update basic info
    cells[0].querySelector('.vehicle-name').textContent = car.name;
    cells[0].querySelector('.vehicle-subtitle').textContent = car.matricule;
    cells[1].textContent = car.model;
    cells[2].textContent = car.year;
    cells[3].textContent = car.km.toLocaleString() + ' km';
    
    // Update status badge
    const statusBadge = cells[4].querySelector('.status-badge');
    statusBadge.className = `status-badge status-${car.status}`;
    statusBadge.textContent = car.status === 'disponible' 
        ? '✓ Disponible' 
        : '⚙ Maintenance';
    
    // Update availability badge
    const availBadge = cells[5].querySelector('.availability-badge');
    availBadge.className = `availability-badge availability-${car.availability_type}`;
    availBadge.textContent = getAvailabilityLabel(car.availability_type);
}
```

### 9.3 Helper Functions

```javascript
function getAvailabilityLabel(type) {
    const labels = {
        'both': '📅 Toute la semaine',
        'weekend': '☀️ Week-end seulement',
        'unavailable': '❌ Indisponible'
    };
    return labels[type] || 'Inconnu';
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function updateVehicleStats() {
    const rows = document.querySelectorAll('table tbody tr:not(.empty-state)');
    const total = rows.length;
    const available = Array.from(rows).filter(r => {
        const badge = r.querySelector('.status-badge');
        return badge && badge.textContent.includes('Disponible');
    }).length;
    const maintenance = total - available;

    const stats = document.querySelectorAll('.stat-value');
    if (stats[0]) stats[0].textContent = total;
    if (stats[1]) stats[1].textContent = available;
    if (stats[2]) stats[2].textContent = maintenance;
}

function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    const icon = type === 'success' 
        ? 'fas fa-check-circle' 
        : 'fas fa-exclamation-circle';
    
    toast.innerHTML = `<i class="${icon}"></i><span>${escapeHtml(message)}</span>`;
    container.appendChild(toast);
    
    setTimeout(() => toast.remove(), 4000);
}

function openModal(id) {
    document.getElementById(id)?.classList.add('show');
}

function closeModal(id) {
    document.getElementById(id)?.classList.remove('show');
}

function closeAll() {
    document.querySelectorAll('.modal-overlay.show').forEach(m => {
        m.classList.remove('show');
    });
}
```

---

## 10. INITIALIZATION

```javascript
// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    console.log('[INIT] Admin Vehicle Management Interface');
    
    // Setup modal close handlers
    document.getElementById('closeModalBtn')?.addEventListener('click', () => closeModal('vehicleModal'));
    document.getElementById('cancelBtn')?.addEventListener('click', () => closeModal('vehicleModal'));
    document.getElementById('closeDetailsBtn')?.addEventListener('click', () => closeModal('detailsModal'));
    document.getElementById('closeDeleteBtn')?.addEventListener('click', () => closeModal('deleteModal'));
    document.getElementById('cancelDeleteBtn')?.addEventListener('click', () => closeModal('deleteModal'));
    document.getElementById('closeStatusBtn')?.addEventListener('click', () => closeModal('statusModal'));
    document.getElementById('cancelStatusBtn')?.addEventListener('click', () => closeModal('statusModal'));
    
    // Setup add button
    document.getElementById('addVehicleBtn')?.addEventListener('click', () => {
        currentEditId = null;
        document.getElementById('vehicleForm').reset();
        document.getElementById('modalTitle').textContent = 'Ajouter un véhicule';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-plus"></i> Ajouter';
        document.getElementById('availabilityTypeSelect').dispatchEvent(new Event('change'));
        openModal('vehicleModal');
    });
    
    // Close modals on outside click
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal(modal.id);
            }
        });
    });
    
    // Initialize stats
    updateVehicleStats();
    
    // Setup broadcast listeners if Echo is available
    if (window.Echo) {
        console.log('[INIT] Broadcasting enabled');
        window.Echo.channel('vehicles')
            .listen('car.created', (e) => handleVehicleCreated(e.car))
            .listen('car.updated', (e) => handleVehicleUpdated(e.car))
            .listen('car.deleted', (e) => handleVehicleDeleted(e.car_id))
            .listen('availability.changed', (e) => handleAvailabilityChanged(e.car));
    } else {
        console.log('[INIT] Broadcasting disabled (offline mode)');
    }
});

// Global variables
let currentEditId = null;
let currentDeleteId = null;
let currentStatusId = null;
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
```

---

## Complete JavaScript Block

Paste this entire script at the end of the admin-index.blade.php file (before `</body>`):

```html
<script>
    // Global Variables
    let currentEditId = null;
    let currentDeleteId = null;
    let currentStatusId = null;
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ============================================
    // AVAILABILITY DESCRIPTIONS
    // ============================================
    const availabilityDescriptions = {
        both: { title: '📅 Disponible toute la semaine', helper: 'Les clients peuvent réserver ce véhicule tous les jours.' },
        weekend: { title: '☀️ Disponible week-end uniquement', helper: 'Les clients ne peuvent réserver que le samedi et dimanche.' },
        unavailable: { title: '❌ Véhicule indisponible', helper: 'Ce véhicule ne peut PAS être réservé du tout.' }
    };

    // ============================================
    // MODAL & TOAST MANAGEMENT
    // ============================================
    function openModal(id) { document.getElementById(id)?.classList.add('show'); }
    function closeModal(id) { document.getElementById(id)?.classList.remove('show'); }
    function closeAll() { document.querySelectorAll('.modal-overlay.show').forEach(m => m.classList.remove('show')); }

    function showToast(msg, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        toast.innerHTML = `<i class="${icon}"></i><span>${escapeHtml(msg)}</span>`;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    // ============================================
    // UTILITY FUNCTIONS
    // ============================================
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function debounce(func, delay) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => func(...args), delay);
        };
    }

    function getAvailabilityLabel(type) {
        const labels = { 'both': '📅 Toute la semaine', 'weekend': '☀️ Week-end seulement', 'unavailable': '❌ Indisponible' };
        return labels[type] || 'Inconnu';
    }

    // ============================================
    // FORM HANDLERS
    // ============================================
    document.getElementById('vehicleForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        btn.classList.add('loading');
        btn.disabled = true;

        try {
            const formData = new FormData(e.target);
            if (currentEditId) formData.set('_method', 'PUT');
            
            const endpoint = currentEditId ? `/cars/${currentEditId}` : '/cars';
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            const result = await response.json();
            if (!response.ok) throw new Error(result.message);

            showToast(result.message, 'success');
            closeModal('vehicleModal');
            setTimeout(() => {
                if (!window.Echo) {
                    currentEditId ? handleVehicleUpdated(result.car) : handleVehicleCreated(result.car);
                }
            }, 500);
        } catch (error) {
            showToast(error.message || 'Erreur', 'error');
        } finally {
            btn.classList.remove('loading');
            btn.disabled = false;
        }
    });

    // ============================================
    // EDIT HANDLER
    // ============================================
    function handleEdit(carId) {
        fetch(`/api/cars/${carId}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.ok ? r.json() : Promise.reject(new Error('Erreur')))
            .then(data => {
                currentEditId = data.car.id;
                document.getElementById('modalTitle').textContent = '✏️ Modifier';
                ['name', 'matricule', 'model', 'year', 'km'].forEach(field => {
                    const el = document.querySelector(`input[name="${field}"]`);
                    if (el) el.value = data.car[field];
                });
                document.querySelector('select[name="status"]').value = data.car.status;
                document.querySelector('select[name="availability_type"]').value = data.car.availability_type;
                document.getElementById('availabilityTypeSelect').dispatchEvent(new Event('change'));
                openModal('vehicleModal');
            })
            .catch(e => showToast('Impossible de charger le véhicule', 'error'));
    }

    // ============================================
    // DELETE HANDLER
    // ============================================
    function handleDeleteClick(carId) {
        currentDeleteId = carId;
        openModal('deleteModal');
    }

    document.getElementById('confirmDeleteBtn')?.addEventListener('click', async () => {
        if (!currentDeleteId) return;
        const btn = document.getElementById('confirmDeleteBtn');
        btn.classList.add('loading');
        btn.disabled = true;

        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('_token', CSRF);
            
            const response = await fetch(`/cars/${currentDeleteId}`, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            const result = await response.json();
            if (!response.ok) throw new Error(result.message);

            showToast(result.message, 'success');
            closeModal('deleteModal');
            setTimeout(() => {
                if (!window.Echo) handleVehicleDeleted(currentDeleteId);
                currentDeleteId = null;
            }, 500);
        } catch (error) {
            showToast(error.message || 'Erreur', 'error');
        } finally {
            btn.classList.remove('loading');
            btn.disabled = false;
        }
    });

    // ============================================
    // STATUS HANDLER
    // ============================================
    document.getElementById('confirmStatusBtn')?.addEventListener('click', async () => {
        if (!currentStatusId) return;
        const newStatus = document.getElementById('newStatus').value;
        if (!newStatus) { showToast('Sélectionnez un statut', 'error'); return; }

        const btn = document.getElementById('confirmStatusBtn');
        btn.classList.add('loading');
        btn.disabled = true;

        try {
            const response = await fetch(`/cars/${currentStatusId}/availability`, {
                method: 'PUT',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: newStatus, availability_type: 'both' })
            });

            const result = await response.json();
            if (!response.ok) throw new Error(result.message);

            showToast('Statut mis à jour', 'success');
            closeModal('statusModal');
            setTimeout(() => {
                if (!window.Echo) handleAvailabilityChanged(result.car);
                currentStatusId = null;
            }, 500);
        } catch (error) {
            showToast(error.message || 'Erreur', 'error');
        } finally {
            btn.classList.remove('loading');
            btn.disabled = false;
        }
    });

    // ============================================
    // BROADCAST LISTENERS
    // ============================================
    window.Echo?.channel('vehicles')
        .listen('car.created', (e) => handleVehicleCreated(e.car))
        .listen('car.updated', (e) => handleVehicleUpdated(e.car))
        .listen('car.deleted', (e) => handleVehicleDeleted(e.car_id))
        .listen('availability.changed', (e) => handleAvailabilityChanged(e.car));

    function handleVehicleCreated(car) {
        if (document.querySelector(`tr[data-car-id="${car.id}"]`)) return;
        const tbody = document.querySelector('table tbody');
        if (tbody) tbody.insertBefore(createTableRow(car), tbody.firstChild);
        updateVehicleStats();
        showToast(`✅ ${escapeHtml(car.name)} ajouté`, 'success');
    }

    function handleVehicleUpdated(car) {
        const row = document.querySelector(`tr[data-car-id="${car.id}"]`);
        if (row) updateTableRow(row, car);
    }

    function handleVehicleDeleted(carId) {
        const row = document.querySelector(`tr[data-car-id="${carId}"]`);
        if (row) {
            row.style.opacity = '0.5';
            setTimeout(() => { row.remove(); updateVehicleStats(); }, 300);
        }
    }

    function handleAvailabilityChanged(car) {
        const row = document.querySelector(`tr[data-car-id="${car.id}"]`);
        if (!row) return;
        const statusCell = row.querySelector('.status-badge');
        if (statusCell) { statusCell.className = `status-badge status-${car.status}`; statusCell.textContent = car.status === 'disponible' ? '✓ Disponible' : '⚙ Maintenance'; }
    }

    // ============================================
    // TABLE HELPERS
    // ============================================
    function createTableRow(car) {
        const row = document.createElement('tr');
        row.dataset.carId = car.id;
        row.innerHTML = `<td><div class="vehicle-cell"><div class="vehicle-avatar">🚗</div><div class="vehicle-info"><div class="vehicle-name">${escapeHtml(car.name)}</div><div class="vehicle-subtitle">${escapeHtml(car.matricule)}</div></div></div></td><td>${escapeHtml(car.model)}</td><td>${car.year}</td><td>${car.km.toLocaleString()} km</td><td><span class="status-badge status-${car.status}">${car.status === 'disponible' ? '✓ Disponible' : '⚙ Maintenance'}</span></td><td><span class="availability-badge availability-${car.availability_type}">${getAvailabilityLabel(car.availability_type)}</span></td><td><div class="action-buttons"><button class="action-btn edit" onclick="handleEdit(${car.id})"><i class="fas fa-edit"></i> Modifier</button><button class="action-btn delete" onclick="handleDeleteClick(${car.id})"><i class="fas fa-trash"></i> Supprimer</button></div></td>`;
        return row;
    }

    function updateTableRow(row, car) {
        const cells = row.querySelectorAll('td');
        cells[1].textContent = car.model;
        cells[2].textContent = car.year;
        cells[3].textContent = car.km.toLocaleString() + ' km';
    }

    function updateVehicleStats() {
        const rows = document.querySelectorAll('table tbody tr');
        const total = rows.length;
        const available = Array.from(rows).filter(r => r.querySelector('.status-badge')?.textContent.includes('Disponible')).length;
        const stats = document.querySelectorAll('.stat-value');
        if (stats[0]) stats[0].textContent = total;
        if (stats[1]) stats[1].textContent = available;
        if (stats[2]) stats[2].textContent = total - available;
    }

    // ============================================
    // INITIALIZATION
    // ============================================
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('addVehicleBtn')?.addEventListener('click', () => {
            currentEditId = null;
            document.getElementById('vehicleForm').reset();
            document.getElementById('modalTitle').textContent = 'Ajouter un véhicule';
            openModal('vehicleModal');
        });

        document.getElementById('closeModalBtn')?.addEventListener('click', () => closeModal('vehicleModal'));
        document.getElementById('cancelBtn')?.addEventListener('click', () => closeModal('vehicleModal'));
        document.getElementById('closeDeleteBtn')?.addEventListener('click', () => closeModal('deleteModal'));
        document.getElementById('cancelDeleteBtn')?.addEventListener('click', () => closeModal('deleteModal'));
        document.getElementById('closeStatusBtn')?.addEventListener('click', () => closeModal('statusModal'));
        document.getElementById('cancelStatusBtn')?.addEventListener('click', () => closeModal('statusModal'));

        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal(modal.id);
            });
        });

        updateVehicleStats();
    });
</script>
```

---

This comprehensive guide provides all the JavaScript handlers needed for a fully functional admin vehicle management interface with real-time synchronization.
