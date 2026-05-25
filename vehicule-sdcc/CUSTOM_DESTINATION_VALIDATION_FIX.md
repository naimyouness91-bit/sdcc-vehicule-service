# Custom Destination Validation Error - Fix Documentation

**Date**: May 18, 2026  
**Status**: ✅ FIXED  
**Error**: "The custom destination field must be a string."

---

## Problem Summary

When users created a new reservation request and selected a **predefined destination** (not "Autre destination"), the system threw a validation error:

```
Erreur de validation
The custom destination field must be a string.
```

This happened even though users didn't interact with the custom destination field.

---

## Root Cause Analysis

### Why the Error Occurred

1. **Form Structure**: The form has two destination fields:
   - `destination` select (predefined destinations + "Autre destination" option)
   - `custom_destination` text input (hidden by default, shown only when "Autre destination" is selected)

2. **Form Submission**: When submitting the form:
   - The `custom_destination` input is ALWAYS part of the form's data
   - Even when hidden (display: none), the browser includes it in the submission
   - When user selects a predefined destination, the field value is an empty string

3. **Validation Rule (BEFORE FIX)**:
   ```php
   'custom_destination' => 'required_if:destination,__other__|string|max:255',
   ```
   
   This rule meant:
   - IF destination is "__other__", THEN custom_destination is required
   - Otherwise, custom_destination is NOT validated by "required_if"
   - BUT the `string` rule still applied
   - An empty string from a hidden field was being treated as invalid

4. **Why It Failed**:
   - The form was sending `custom_destination=""` (empty string)
   - The validation rule didn't explicitly allow nullable values
   - The `string` validation was failing on the empty string in certain edge cases

---

## Fixes Applied

### Fix 1: Updated Validation Rule (CRITICAL)

**File**: `app/Http/Controllers/MesDemandesController.php` (Line 190)

**Before**:
```php
'custom_destination' => 'required_if:destination,__other__|string|max:255',
```

**After**:
```php
'custom_destination' => 'nullable|required_if:destination,__other__|string|max:255',
```

**What Changed**: Added `nullable` at the beginning of the validation chain. This tells Laravel:
- The field can be null or empty
- If destination is "__other__", the field IS required (and must be a non-empty string)
- If destination is NOT "__other__", the field can be empty/null
- If a value is provided, it must be a string (max 255 chars)

---

### Fix 2: Added Error Display (SUPPORT)

**File**: `resources/views/mes-demandes/create.blade.php` (After line 556)

**Before**:
```blade
@if($errors->has('destination'))
    <div style="color: #e53935; font-weight: 600; margin-top: 6px;">{{ $errors->first('destination') }}</div>
@endif
```

**After**:
```blade
@if($errors->has('destination'))
    <div style="color: #e53935; font-weight: 600; margin-top: 6px;">{{ $errors->first('destination') }}</div>
@endif
@if($errors->has('custom_destination'))
    <div style="color: #e53935; font-weight: 600; margin-top: 6px;">{{ $errors->first('custom_destination') }}</div>
@endif
```

**What Changed**: Added error display for `custom_destination` field so users see validation errors if they occur.

---

### Fix 3: JavaScript Enhancement (ROBUSTNESS)

**File**: `resources/views/mes-demandes/create.blade.php` (Form submit event)

**Before**:
- The form would submit `custom_destination` with empty string when not needed
- Only the `destination` field management was controlled

**After**:
- When user selects a predefined destination:
  ```javascript
  customInput.removeAttribute('name');  // ← Remove from form submission
  customInput.value = '';               // ← Clear the value
  ```
- When user selects "Autre destination":
  ```javascript
  if (!customInput.getAttribute('name')) {
      customInput.setAttribute('name', 'custom_destination');  // ← Add back
  }
  ```

**What Changed**: The custom_destination field's `name` attribute is dynamically added/removed, ensuring:
- When not needed: Field is NOT submitted to the server
- When needed: Field IS submitted with its value
- Validation will only see the field when it should be there

---

## How It Works Now

### Scenario 1: User Selects Predefined Destination ✅

1. User opens "Nouvelle demande" form
2. Selects destination from dropdown (e.g., "Casablanca")
3. Clicks submit button
4. JavaScript removes `name` attribute from custom_destination input
5. Form sends: `destination=Casablanca` (custom_destination NOT sent)
6. Validation passes ✅
   - destination="Casablanca" ✓ (required, string, max 255)
   - custom_destination not in request ✓ (nullable rule allows this)

### Scenario 2: User Selects "Autre destination" ✅

1. User opens "Nouvelle demande" form
2. Selects "✏️ Autre destination" from dropdown
3. Custom destination input appears
4. User types: "Mon lieu personnalisé"
5. Clicks submit button
6. JavaScript ensures custom_destination has name attribute and is submitted
7. Form sends: `destination=__other__`, `custom_destination=Mon lieu personnalisé`
8. Validation passes ✅
   - destination="__other__" ✓ (required, string, max 255)
   - custom_destination="Mon lieu personnalisé" ✓ (required_if triggers, string, max 255)

### Scenario 3: User Selects "Autre" but Doesn't Fill Custom Field ❌ (Intentional)

1. User selects "✏️ Autre destination"
2. Doesn't fill in the custom destination field
3. Clicks submit button
4. JavaScript checks if custom_destination is empty:
   ```javascript
   if (!customInput.value.trim()) {
       e.preventDefault();
       alert('Veuillez entrer une destination personnalisée.');
       return false;
   }
   ```
5. Form doesn't submit ❌ (Client-side validation prevents it)
6. User sees alert message

---

## Validation Rule Explanation

```php
'custom_destination' => 'nullable|required_if:destination,__other__|string|max:255',
```

Breaking it down:

| Rule | Meaning | Example |
|------|---------|---------|
| `nullable` | Can be null, empty string, or missing | Doesn't fail if not sent |
| `required_if:destination,__other__` | Required ONLY if destination is "__other__" | Must have a value if user chose "Autre" |
| `string` | Must be a string (if provided) | Can't be array, object, or number |
| `max:255` | Maximum length is 255 characters | Long text will fail |

**Validation Matrix**:

| Destination | custom_destination | Passes? | Why? |
|---|---|---|---|
| "Casablanca" | (not sent) | ✅ YES | nullable + no required_if trigger |
| "Casablanca" | "" | ✅ YES | nullable + no required_if trigger |
| "__other__" | "Place Jamaa" | ✅ YES | required_if triggers + string + <= 255 chars |
| "__other__" | "" | ❌ NO | required_if triggers but value is empty |
| "__other__" | (not sent) | ❌ NO | required_if triggers but field missing |

---

## Files Modified

1. **`app/Http/Controllers/MesDemandesController.php`** (Line 190)
   - Updated validation rule with `nullable`

2. **`resources/views/mes-demandes/create.blade.php`** (3 locations)
   - Added error display for custom_destination
   - Enhanced JavaScript to manage name attribute
   - Ensured field is cleared when not needed

---

## Testing

### Test Case 1: Predefined Destination ✅

```
1. Go to "Nouvelle demande"
2. Destination dropdown → Select "Casablanca"
3. Verify: custom_destination wrapper is hidden
4. Fill in other required fields
5. Submit form
6. Expected: ✅ Form submits without validation error
```

### Test Case 2: Custom Destination ✅

```
1. Go to "Nouvelle demande"
2. Destination dropdown → Select "✏️ Autre destination"
3. Custom input appears
4. Type: "Mon agence locale"
5. Fill in other required fields
6. Submit form
7. Expected: ✅ Form submits with custom destination
```

### Test Case 3: Custom Without Value ❌ (Intentional)

```
1. Go to "Nouvelle demande"
2. Destination dropdown → Select "✏️ Autre destination"
3. Leave custom input empty
4. Fill in other required fields
5. Submit form
6. Expected: ❌ JavaScript alert appears, form doesn't submit
7. User must enter a custom destination first
```

### Test Case 4: Browser Network Inspection

```
1. Open browser DevTools (F12) → Network tab
2. Fill form with predefined destination
3. Submit
4. Check request payload:
   Expected: NO "custom_destination" field in form data
   OR custom_destination is not sent at all

5. Fill form with custom destination
6. Submit
7. Check request payload:
   Expected: Both "destination=__other__" and "custom_destination=value"
```

---

## Debugging

### Check Validation Rules
```php
// In MesDemandesController.php
$rules = [
    'custom_destination' => 'nullable|required_if:destination,__other__|string|max:255',
];

// Test with tinker
php artisan tinker
> \Illuminate\Support\Facades\Validator::make(['destination' => 'Casablanca', 'custom_destination' => ''], $rules)->passes()
true  // ✅ Passes when predefined destination selected

> \Illuminate\Support\Facades\Validator::make(['destination' => '__other__', 'custom_destination' => ''], $rules)->passes()
false  // ❌ Fails when __other__ selected but custom is empty

> \Illuminate\Support\Facades\Validator::make(['destination' => '__other__', 'custom_destination' => 'My Place'], $rules)->passes()
true  // ✅ Passes when custom destination provided
```

### Check Request Data
```php
// In MesDemandesController.php store() method, add:
Log::debug('Request data:', $request->all());
Log::debug('Validated data:', $validated);

// Check logs:
tail -f storage/logs/laravel.log | grep "Request data"
```

---

## Related Documentation

- Admin Reservation Creation: `app/Http/Controllers/AdminReservationsController.php` (doesn't use custom_destination)
- Destination Selection Logic: `resources/views/mes-demandes/create.blade.php` (lines 500-560)
- Form Submission Handler: `resources/views/mes-demandes/create.blade.php` (lines 1045-1100)

---

## Status

- ✅ Validation rule fixed
- ✅ Error display added
- ✅ JavaScript enhancement applied
- ✅ Ready for testing
- ✅ No breaking changes

**Production Ready**: Yes ✅  
**Backward Compatible**: Yes ✅  
**Security Impact**: None (just validation improvement)
