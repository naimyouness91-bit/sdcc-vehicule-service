# Admin Sidebar - Developer Guide

## Architecture Overview

```
Request Flow:
┌─────────────┐
│ User clicks │
│ sidebar link│
└──────┬──────┘
       │
       ▼
┌─────────────────────────────────┐
│ JavaScript: loadTabContent()    │
│ - Updates active link           │
│ - Fetches /admin/tab/{tab}      │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│ Laravel Route: /admin/tab/{tab} │
│ Calls AdminDataManagementCtrl   │
└──────┬──────────────────────────┘
       │
       ▼
┌──────────────────────────────────┐
│ Controller: loadTab($tab)        │
│ Matches tab name & calls method  │
└──────┬───────────────────────────┘
       │
       ▼
┌──────────────────────────────────┐
│ Specific Method (e.g. loadEmpl..│
│ - Queries database              │
│ - Collects data                 │
│ - Calls view->render()          │
└──────┬───────────────────────────┘
       │
       ▼
┌──────────────────────────────────┐
│ Returns HTML                     │
│ JavaScript inserts into DOM      │
│ Initializes event listeners      │
└──────────────────────────────────┘
```

## Adding a New Data Section

### Step 1: Create the View
Create file: `resources/views/admin/tables/mynewsection.blade.php`

```blade
<!-- MyNewSection Table -->
<div class="data-table-container">
    <div class="table-toolbar">
        <div class="search-box">
            <input type="text" placeholder="Search...">
        </div>
        <div class="table-filters">
            <select class="filter-select">
                <option value="">All</option>
                <!-- Your filter options -->
            </select>
            <button class="action-btn" onclick="openNewSectionModal()">
                <i class="fas fa-plus"></i> Add
            </button>
        </div>
    </div>

    @if($data->count() > 0)
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Column 1</th>
                        <th>Column 2</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                    <tr>
                        <td>{{ $item->field1 }}</td>
                        <td>{{ $item->field2 }}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="icon-btn view" onclick="viewItem({{ $item->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="icon-btn edit" onclick="editItem({{ $item->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="icon-btn delete" onclick="deleteItem({{ $item->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 40px;">
                            <p style="color: #999;">No data found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No records</p>
        </div>
    @endif
</div>

<script>
function openNewSectionModal() {
    // Implement modal
}

function viewItem(id) {
    // Implement view
}

function editItem(id) {
    // Implement edit
}

function deleteItem(id) {
    if (confirm('Are you sure?')) {
        // Implement delete
    }
}
</script>
```

### Step 2: Add Controller Method
In `AdminDataManagementController.php`:

```php
private function loadMynewsectionTab()
{
    $data = MyModel::all(); // Your query here
    
    return view('admin.tables.mynewsection', compact('data'))->render();
}
```

### Step 3: Update Route Handler
In `loadTab()` method of controller:

```php
return match ($tab) {
    'employees' => $this->loadEmployeesTab(),
    'vehicles' => $this->loadVehiclesTab(),
    // ... existing cases ...
    'mynewsection' => $this->loadMynewsectionTab(),  // ADD THIS
    default => '<div class="empty-state">...</div>'
};
```

### Step 4: Add Sidebar Link
In `resources/views/layouts/admin-sidebar.blade.php`:

```blade
<li class="sidebar-menu-item">
    <a href="#mynewsection" class="sidebar-menu-link" data-tab="mynewsection">
        <i class="fas fa-icon-name"></i>
        <span>My New Section</span>
    </a>
</li>
```

### Step 5: Update JavaScript
In the script section of `admin-sidebar.blade.php`, update `updatePageTitle()`:

```javascript
function updatePageTitle(tabName) {
    const titles = {
        // ... existing titles ...
        'mynewsection': 'My New Section Title',
    };
    
    const icons = {
        // ... existing icons ...
        'mynewsection': 'fas fa-icon-name',
    };
    
    // ... rest of function ...
}
```

### Step 6: Test
1. Navigate to admin dashboard
2. Click new sidebar link
3. Verify data loads correctly
4. Test search and filters

## Customizing Existing Sections

### Modify Table Columns
Edit the corresponding view file, add/remove `<th>` and `<td>` tags:

```blade
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>New Column</th>  <!-- ADD THIS -->
            <th>Existing Column</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->newfield }}</td>  <!-- ADD THIS -->
            <td>{{ $item->existing }}</td>
        </tr>
        @endforelse
    </tbody>
</table>
```

### Add More Filters
In the table toolbar:

```blade
<select class="filter-select">
    <option value="">All Statuses</option>
    <option value="active">Active</option>
    <option value="inactive">Inactive</option>
    <option value="archived">Archived</option>  <!-- ADD THIS -->
</select>
```

### Change Table Title
Edit page title in JavaScript:

```javascript
const titles = {
    'employees': 'Employee Management System',  // EDIT THIS
    // ...
};
```

## Advanced Features

### Implementing Modal Forms

```javascript
function openEmployeeModal(id = null) {
    // Create or fetch modal
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content">
            <h2>${id ? 'Edit' : 'Add'} Employee</h2>
            <form onsubmit="saveEmployee(event, ${id})">
                <input type="text" name="name" required>
                <input type="email" name="email" required>
                <button type="submit">Save</button>
                <button type="button" onclick="this.closest('.modal').remove()">Cancel</button>
            </form>
        </div>
    `;
    document.body.appendChild(modal);
}

function saveEmployee(e, id) {
    e.preventDefault();
    const form = e.target;
    const data = new FormData(form);
    
    fetch(id ? `/employees/${id}` : '/employees', {
        method: id ? 'PUT' : 'POST',
        body: data,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadTabContent('employees');  // Reload tab
        }
    });
}
```

### Adding Pagination

```blade
@if($data->hasPages())
<div style="padding: 20px; text-align: center;">
    {{ $data->links() }}
</div>
@endif
```

### Inline Editing

```html
<td ondblclick="makeEditable(this, 'name', 1)">
    <span class="content">{{ $item->name }}</span>
</td>

<script>
function makeEditable(cell, field, id) {
    const content = cell.querySelector('.content');
    const value = content.textContent;
    
    cell.innerHTML = `
        <input type="text" value="${value}" onblur="saveEdit(this, '${field}', ${id})">
    `;
    cell.querySelector('input').focus();
}

function saveEdit(input, field, id) {
    fetch(`/items/${id}`, {
        method: 'PUT',
        body: JSON.stringify({ [field]: input.value }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
        }
    }).then(() => location.reload());
}
</script>
```

### Bulk Actions

```javascript
function initializeBulkActions() {
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    const selectedBtn = document.querySelector('.bulk-delete-btn');
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            const anySelected = Array.from(checkboxes).some(c => c.checked);
            selectedBtn.style.display = anySelected ? 'inline-block' : 'none';
        });
    });
}

function bulkDelete() {
    const ids = Array.from(document.querySelectorAll('tbody input[type="checkbox"]:checked'))
        .map(cb => cb.value);
    
    if (ids.length === 0) return;
    if (!confirm(`Delete ${ids.length} items?`)) return;
    
    fetch('/items/bulk-delete', {
        method: 'POST',
        body: JSON.stringify({ ids }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
        }
    }).then(() => location.reload());
}
```

## Performance Optimization

### Database Query Optimization
```php
private function loadEmployeesTab()
{
    // BAD - N+1 queries
    $employees = User::all();
    
    // GOOD - Eager loading
    $employees = User::with('planningZone')
        ->where('role', '!=', 'admin')
        ->get();
    
    return view('admin.tables.employees', compact('employees'))->render();
}
```

### Pagination for Large Datasets
```php
private function loadNotificationsTab()
{
    // Show 20 items per page
    $notifications = auth()->user()->notifications()->paginate(20);
    
    return view('admin.tables.notifications', compact('notifications'))->render();
}
```

### Lazy Loading Images
```blade
<img src="{{ $car->image }}" loading="lazy" alt="Vehicle">
```

## Testing

### Unit Test Example
```php
class AdminDataManagementControllerTest extends TestCase
{
    public function test_loads_employees_tab()
    {
        $response = $this->get('/admin/tab/employees');
        $response->assertStatus(200);
        $response->assertSee('Employés');
    }
    
    public function test_only_admin_can_access()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/data-management');
        $response->assertStatus(403);
    }
}
```

## Common Issues & Solutions

### AJAX Returning 403 Forbidden
**Solution**: Check middleware in routes/web.php
```php
Route::middleware('role:admin|super_admin')->group(function () {
    Route::get('/admin/tab/{tab}', [...]);
});
```

### Table Search Not Working
**Solution**: Ensure search box input exists and is initialized
```javascript
const searchInputs = document.querySelectorAll('.search-box input');
if (searchInputs.length === 0) {
    console.warn('No search inputs found');
}
```

### Styling Not Applied
**Solution**: Check CSS cascade and specificity
```css
/* Make sure selector is specific enough */
.data-table-container table tbody tr:hover {
    background: #f8f9fa !important;  /* Add !important if needed */
}
```

### Mobile Sidebar Collapsed
**Solution**: Check media query breakpoints
```css
@media (max-width: 768px) {
    .admin-sidebar {
        max-height: 60vh;  /* Allows scrolling */
        position: relative;  /* Changes from fixed */
    }
}
```

## Useful Resources

- [Laravel Blade Documentation](https://laravel.com/docs/blade)
- [Eloquent ORM Guide](https://laravel.com/docs/eloquent)
- [CSS Grid/Flexbox](https://web.dev/learn/css)
- [JavaScript Fetch API](https://developer.mozilla.org/docs/Web/API/Fetch_API)
- [AJAX Best Practices](https://developer.mozilla.org/docs/Learn/Server-side/First_steps/Website_architecture)

## Conventions

- Tab names should be lowercase with hyphens (kebab-case)
- View files should match tab names: `resources/views/admin/tables/{tab}.blade.php`
- Controller methods follow pattern: `load{TabName}Tab()`
- CSS classes use BEM naming: `.data-table-container__header`
- Font Awesome icons for consistency
- Green (#4CAF50) for primary actions
- Red (#c62828) for destructive actions

## Version Control Tips

```bash
# Create feature branch
git checkout -b feature/admin-new-section

# Make changes and commit
git add resources/views/admin/tables/mynewsection.blade.php
git commit -m "Add mynewsection to admin sidebar"

# Push and create PR
git push origin feature/admin-new-section
```

## Support & Debugging

### Enable Debug Mode
```php
// In .env
APP_DEBUG=true
LOG_LEVEL=debug
```

### Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

### Browser Console Debugging
```javascript
// In browser console
// Check what's loaded
console.log(document.querySelector('.admin-sidebar'));

// Test fetch
fetch('/admin/tab/employees')
    .then(r => r.text())
    .then(html => console.log(html));
```

---

Happy coding! 🚀
