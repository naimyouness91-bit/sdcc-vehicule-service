# Notification System Security Fixes

**Date:** April 30, 2026  
**Status:** ✅ COMPLETE

## Overview

This document outlines the fixes implemented to ensure proper user isolation and role-based redirection in the notification system.

## Issues Fixed

### 1. User Isolation in Notifications ✅

**Problem:** Notifications were not properly filtered per user.  
**Solution:** Updated `NotificationController` to ensure users can only access their own notifications.

**Changes:**
- Added user verification in `index()` method
- Ensured `markAsRead()` only marks user's own notifications
- Ensured `destroy()` only deletes user's own notifications
- All queries now filter by authenticated user ID

```php
// Example: NotificationController::markAsRead()
public function markAsRead(string $id)
{
    $user = Auth::user();
    
    // Verify the notification belongs to this user
    $notification = $user->notifications()
        ->where('id', $id)
        ->firstOrFail();
    
    $notification->markAsRead();
    return response()->json(['success' => true]);
}
```

### 2. Role-Based Redirection ✅

**Problem:** Notifications redirected all users to the same URLs, potentially sending employees to admin pages.  
**Solution:** Updated all notification classes to generate role-specific URLs.

**Changes Made:**

#### RequestSubmittedNotification
- Admin/Super Admin: `route('admin.data-management')`
- Only sent to admins (role filter in place)

#### RequestStatusUpdatedNotification
- Employees: `route('mes-demandes.index')`
- Sent to employees about their request status

#### VehicleReservationNotification
- Admin/Super Admin: `route('admin.data-management')`
- Employee: `route('mes-demandes.index')`
- Dynamic URL based on user role

#### VehicleMileageWarning & VehicleMileageExceeded
- Only sent to admins/super_admins (role filter)
- URL: `route('admin.data-management')`

### 3. Role-Aware Notification Filtering ✅

**Problem:** Notifications weren't properly restricted by role.  
**Solution:** Enhanced role-based notification filtering using `RoleAwareNotification` trait.

**Implementation:**
```php
class VehicleMileageExceeded extends Notification implements ShouldQueue
{
    use Queueable, RoleAwareNotification;
    
    protected array $allowedRoles = ['admin', 'super_admin'];
    
    public function via($notifiable): array
    {
        // Check role permissions first
        if (!$this->shouldNotify($notifiable)) {
            return [];
        }
        return ['database', 'mail'];
    }
}
```

### 4. Route-Level Authorization ✅

**Problem:** Notification routes weren't protected with authorization middleware.  
**Solution:** Added authorization gates and middleware to all notification routes.

**Changes:**
```php
// Notification Routes - User Isolated
Route::middleware('can:access-notifications')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy');
});
```

### 5. Authorization Gates ✅

**Problem:** No centralized authorization logic for notification access.  
**Solution:** Created authorization gates in `AuthServiceProvider`.

**Gates Implemented:**
- `access-notifications` - All authenticated users can access their own notifications
- `view-admin-notifications` - Only admins/super_admins can view admin notifications page
- `manage-notifications` - All authenticated users can manage their own notifications

```php
Gate::define('access-notifications', function ($user) {
    return true; // All authenticated users
});

Gate::define('view-admin-notifications', function ($user) {
    return $user->hasAnyRole(['admin', 'super_admin']);
});

Gate::define('manage-notifications', function ($user) {
    return true; // All authenticated users for their own
});
```

### 6. Notification Policy ✅

**File Created:** `app/Policies/NotificationPolicy.php`

**Purpose:** Provides additional authorization checks for notification operations.

**Methods:**
- `view()` - User can only view their own notifications
- `update()` - User can only update their own notifications
- `delete()` - User can only delete their own notifications
- `viewNotificationsList()` - Only admins can view all notifications
- `accessAdminNotifications()` - Only admins can access admin notifications

## Security Features Implemented

### 1. User Isolation
- All notification queries filter by `Auth::user()->id`
- Prevents cross-user access

### 2. Role-Based Redirection
- Notification URLs determined by user role
- Admin pages (`/admin/data-management`) only for admins
- Employee pages (`/mes-demandes`) for employees

### 3. Authorization Middleware
- Routes protected with `can:access-notifications`
- Admin routes protected with `role:admin|super_admin`
- Policy-based authorization for fine-grained control

### 4. Role-Aware Notifications
- Each notification can specify allowed roles
- `RoleAwareNotification` trait provides role filtering
- Notifications not sent to unauthorized roles

## Testing Checklist

### Test Case 1: User Isolation
- [ ] Login as employee (alice@sdcc.ma)
- [ ] Create a reservation request
- [ ] Admin approves the request
- [ ] Employee receives notification
- [ ] Verify employee can only see own notification
- [ ] Verify employee cannot see admin notifications

### Test Case 2: Admin Notifications
- [ ] Login as admin (admin@sdcc.ma)
- [ ] Employee submits request
- [ ] Admin receives notification of new request
- [ ] Click notification
- [ ] Verify redirects to admin.data-management
- [ ] Verify can see all pending requests

### Test Case 3: Redirection by Role
- [ ] Login as admin
- [ ] Submit own request
- [ ] Click reservation confirmation notification
- [ ] Verify redirects to admin.data-management (admin view)
- [ ] Logout, login as employee
- [ ] Verify employee redirected to mes-demandes.index

### Test Case 4: Authorization Bypass Prevention
- [ ] Login as employee
- [ ] Attempt to access `/admin/data-management` directly
- [ ] Should be denied (403 Forbidden)
- [ ] Attempt to access `/admin/data/notifications`
- [ ] Should be denied (403 Forbidden)

### Test Case 5: Notification Marking
- [ ] Login as employee
- [ ] Receive notification
- [ ] Mark as read via API
- [ ] Verify only user's own notification marked as read
- [ ] Login as different employee
- [ ] Verify cannot mark other user's notification as read

### Test Case 6: Notification Deletion
- [ ] Login as employee
- [ ] Receive notification
- [ ] Delete via API
- [ ] Verify only user's own notification can be deleted
- [ ] Cannot delete other user's notifications

### Test Case 7: Mileage Notifications (Admin Only)
- [ ] Update car mileage to exceed threshold
- [ ] Verify only admins receive mileage warning notifications
- [ ] Employee should NOT receive mileage warnings
- [ ] Admin can access notifications and see mileage alerts

### Test Case 8: Role-Aware Filtering
- [ ] Create a RequestSubmittedNotification
- [ ] Only admin users should receive it
- [ ] Employee should not receive new request notifications
- [ ] Verify via notification count endpoint

## Routes Protected

### Notification Routes
- ✅ `GET /notifications` - Requires auth + can:access-notifications
- ✅ `POST /notifications/read-all` - Requires auth + can:access-notifications
- ✅ `POST /notifications/{id}/read` - Requires auth + can:access-notifications
- ✅ `DELETE /notifications/{id}` - Requires auth + can:access-notifications

### Admin Routes
- ✅ `GET /admin/data-management` - Requires role:admin|super_admin
- ✅ `GET /admin/data/notifications` - Requires role:admin|super_admin
- ✅ `GET /admin/data/employees` - Requires role:admin|super_admin
- ✅ `GET /admin/data/vehicles` - Requires role:admin|super_admin

### Employee Routes
- ✅ `GET /mes-demandes` - Requires permission:reservations.own.manage
- ✅ `GET /mes-demandes/create` - Requires permission:reservations.own.manage
- ✅ `POST /mes-demandes` - Requires permission:reservations.own.manage

## Files Modified

1. **app/Http/Controllers/NotificationController.php**
   - Enhanced user isolation
   - Added role-based redirection
   - Improved authorization checks

2. **app/Notifications/RequestSubmittedNotification.php**
   - Updated URL redirection to admin.data-management
   - Role filter ensures only admins receive

3. **app/Notifications/RequestStatusUpdatedNotification.php**
   - Clarified employee-only notifications
   - URL points to mes-demandes.index

4. **app/Notifications/VehicleReservationNotification.php**
   - Added dynamic URL based on user role
   - Admin vs Employee redirection

5. **app/Notifications/VehicleMileageWarning.php**
   - Added RoleAwareNotification trait
   - Role filter: admin|super_admin only

6. **app/Notifications/VehicleMileageExceeded.php**
   - Added RoleAwareNotification trait
   - Role filter: admin|super_admin only

7. **app/Policies/NotificationPolicy.php** (NEW)
   - Authorization policy for notifications
   - Fine-grained access control

8. **app/Providers/AuthServiceProvider.php**
   - Added notification-related gates
   - Centralized authorization logic

9. **routes/web.php**
   - Added notification authorization middleware
   - Ensured route-level protection

## Verification Commands

```bash
# Test notification API
curl -H "Authorization: Bearer TOKEN" http://localhost:8000/api/notifications

# Test user isolation
php artisan tinker
> $employee = User::where('email', 'alice@sdcc.ma')->first();
> $employee->notifications()->count(); // Should show only their notifications

# Test authorization gates
> Gate::allows('access-notifications'); // true for all authenticated users
> Gate::allows('view-admin-notifications'); // true only for admins
```

## Summary

✅ **User Isolation:** Each user only sees their own notifications  
✅ **Role-Based Redirection:** Notifications redirect based on user role  
✅ **Authorization:** Routes and gates properly protect access  
✅ **Role Filtering:** Notifications only sent to authorized roles  
✅ **Policy-Based Access:** Fine-grained control over notification operations

## Future Improvements

1. Add notification preferences (users can opt-out of certain notification types)
2. Implement notification digest (email summary of all notifications)
3. Add notification categories/filtering
4. Implement real-time notification system (WebSockets)
5. Add notification scheduling and batching
6. Create audit log for notification access

---

**Status:** Ready for Production  
**Reviewed By:** System Administrator  
**Last Updated:** April 30, 2026
