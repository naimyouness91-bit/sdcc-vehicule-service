# Modern Toast Notification System Guide

## Overview

The application now has a complete, production-ready toast notification system with smooth animations and consistent styling. All system messages (success, errors, warnings, info) appear as modern popup notifications that are non-intrusive and user-friendly.

## Features

✅ **Auto-dismiss** - Notifications disappear after 4-5 seconds (configurable)  
✅ **Manual dismiss** - Users can click the × button to close immediately  
✅ **Smooth animations** - Slide-in and fade-out effects  
✅ **Color-coded** - Green (success), Red (error), Orange (warning), Blue (info)  
✅ **Dark theme integration** - Styled to match NFS design  
✅ **Responsive** - Works perfectly on mobile and desktop  
✅ **Non-blocking** - Notifications stack in top-right corner  
✅ **AJAX-ready** - Built-in functions for API responses  

## Toast Types & Durations

| Type | Color | Duration | Icon |
|------|-------|----------|------|
| Success | `#00d084` (Green) | 4.5s | ✓ Check circle |
| Error | `#ff4757` (Red) | 5.5s | ✗ Times circle |
| Warning | `#ffa500` (Orange) | 4.5s | ⚠ Exclamation circle |
| Info | `#0066ff` (Blue) | 4.0s | ⓘ Info circle |

## Usage in Controllers

### Flash Messages (Page Redirect)

```php
// Success message
return redirect()->route('page')->with('success', 'Action completed successfully!');

// Error message
return back()->with('error', 'An error occurred while processing your request');

// Warning message
return redirect()->route('page')->with('warning', 'Please review this information');

// Info message
return redirect()->route('page')->with('info', 'This is informational');
```

### JSON/AJAX Responses

The system automatically handles JSON responses with the following formats:

```php
// Option 1: With success boolean
return response()->json([
    'success' => true,
    'message' => 'Véhicule créé avec succès!',
    'title' => 'Succès',  // Optional
    'data' => $car
]);

// Option 2: With explicit type
return response()->json([
    'message' => 'Something went wrong',
    'type' => 'error',
    'title' => 'Erreur'  // Optional
], 422);
```

## Usage in JavaScript

### Basic Methods (Recommended)

```javascript
// Success notification
Toast.success('Your message here');
Toast.success('Message', 'Custom Title');

// Error notification
Toast.error('Error message');
Toast.error('Error message', 'Custom Title');

// Warning notification
Toast.warning('Warning message');
Toast.warning('Warning message', 'Custom Title');

// Info notification
Toast.info('Info message');
Toast.info('Info message', 'Custom Title');
```

### Custom Options

```javascript
Toast.show({
    message: 'Custom message',
    title: 'Custom Title',
    type: 'success',  // success, error, warning, info
    duration: 6000,   // milliseconds (0 = never auto-close)
    dismissible: true // show close button
});
```

### From AJAX Fetch Responses

```javascript
// After fetch request
fetch('/api/cars', {
    method: 'POST',
    body: JSON.stringify(data),
    headers: { 'Content-Type': 'application/json' }
})
.then(response => response.json())
.then(data => {
    displayToastFromResponse(data);
    // data should have: success, message, title, type
})
.catch(error => {
    displayErrorToast(error, 'Failed to complete action');
});
```

## Real-World Examples

### Example 1: Create a Car (Controller)

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'matricule' => 'required|string|unique:cars',
        // ... more validations
    ]);

    try {
        $car = Car::create($validated);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Véhicule ajouté avec succès!',
                'car' => $car
            ], 201);
        }

        return redirect()->route('cars.index')
            ->with('success', 'Véhicule ajouté avec succès!');
    } catch (Exception $e) {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du véhicule'
            ], 500);
        }

        return back()->with('error', 'Erreur lors de la création du véhicule');
    }
}
```

### Example 2: Handle AJAX Request (JavaScript)

```javascript
// Create form submission handler
document.getElementById('carForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('/api/cars', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        displayToastFromResponse(result);
        
        if (result.success) {
            this.reset();
            // Redirect or update UI
        }
    } catch (error) {
        displayErrorToast(error);
    }
});
```

### Example 3: Manual Toast Trigger

```javascript
// Simple success notification
button.addEventListener('click', () => {
    Toast.success('Réservation confirmée!');
});

// With error handling
button.addEventListener('click', async () => {
    try {
        const response = await updateCar();
        if (response.ok) {
            Toast.success('Véhicule modifié avec succès!');
        } else {
            Toast.error('Erreur lors de la modification');
        }
    } catch (e) {
        Toast.error(e.message);
    }
});
```

## CSS Customization

### Adjust Toast Width
Edit in `/resources/views/layouts/app.blade.php`:

```css
.toast {
    min-width: 320px;  /* Default: 320px */
    max-width: 450px;  /* Default: 450px */
}
```

### Change Duration Times
Edit in `/resources/js/toast-notifications.js`:

```javascript
success: function(message, title = '') {
    return new ToastNotification({
        message,
        title: title || 'Succès',
        type: 'success',
        duration: 4500  // Change this value (ms)
    }).show();
}
```

### Change Position
Edit in `/resources/views/layouts/app.blade.php`:

```css
.toast-container {
    top: 100px;      /* Distance from top */
    right: 25px;     /* Distance from right */
    /* To position on left: left: 25px; right: unset; */
}
```

## Best Practices

### ✅ DO

1. **Use descriptive messages** - "Véhicule ajouté avec succès!" instead of "Success"
2. **Handle errors gracefully** - Always provide user-friendly error messages
3. **Validate before sending** - Use validation errors as modals (for multiple errors)
4. **Keep messages brief** - Max 2 lines of text per toast
5. **Use appropriate type** - Match the severity (error, warning, success, info)
6. **Test on mobile** - Toasts are responsive but verify on small screens

### ❌ DON'T

1. **Don't show too many toasts** - Max 2-3 visible at once
2. **Don't use success for warnings** - Use the correct type
3. **Don't forget CSRF tokens** - Always include them in AJAX requests
4. **Don't make toasts permanent** - Users need to dismiss them, don't set duration to 0
5. **Don't use HTML in messages** - Escape special characters

## Troubleshooting

### Toasts not showing?

1. Check browser console for JavaScript errors
2. Verify `toast-notifications.js` is imported in `app.js`
3. Ensure CSS is loaded in layout
4. Check that flash messages are being set correctly in controller

### Wrong positioning?

Edit `.toast-container` CSS in the layout file to adjust position.

### Wrong colors?

Check the `toast-[type]` CSS classes and their color values.

### Not auto-dismissing?

Adjust `duration` parameter in Toast function or Toast.show() call.

## Browser Support

Works on all modern browsers:
- ✅ Chrome/Edge 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Mobile browsers

## Technical Details

### Files Modified/Created

- `resources/js/toast-notifications.js` - Toast system logic
- `resources/js/app.js` - Imports toast module
- `resources/views/layouts/app.blade.php` - CSS and initialization

### Key CSS Classes

- `.toast-container` - Main wrapper
- `.toast` - Individual toast element
- `.toast-success`, `.toast-error`, `.toast-warning`, `.toast-info` - Type variants
- `.toast-show` - Animation class when showing
- `.toast-hide` - Animation class when dismissing

### Global JavaScript Objects

- `window.Toast` - Main API for showing toasts
- `displayToastFromResponse()` - Helper for API responses
- `displayErrorToast()` - Helper for fetch/axios errors
- `ToastNotification` - Class for manual creation

## Performance

- **No dependencies** on external toast libraries (besides FontAwesome icons)
- **Lightweight** - ~4KB of JavaScript
- **Efficient animations** - Uses CSS transitions and transforms
- **Memory safe** - Properly cleans up DOM elements

---

**Last Updated:** April 21, 2026
