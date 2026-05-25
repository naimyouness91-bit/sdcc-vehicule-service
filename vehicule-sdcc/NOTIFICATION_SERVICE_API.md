# 📖 NotificationService API Reference

## Overview

The `NotificationService` provides a clean, role-aware API for sending notifications throughout your application.

```php
use App\Services\NotificationService;

// Inject into your controller or service
public function __construct(private NotificationService $notificationService) {}
```

---

## Methods

### 1. `notifyAllAdmins(Notification, bool $async = true): array`

Send notification to all admins (admin + super_admin roles).

**Example:**
```php
$this->notificationService->notifyAllAdmins(
    new RequestSubmittedNotification(
        employeeName: 'Ahmed Ali',
        destination: 'Marrakech',
        carId: 1,
        demandeId: 123
    ),
    async: true
);
```

**Returns:**
```php
[
    'sent' => 2,       // Number successfully sent
    'failed' => 0,     // Number failed
    'recipients' => 2  // Total recipients
]
```

**Roles targeted:** `admin`, `super_admin`

---

### 2. `notifyUser(User, Notification, bool $async = true): bool`

Send notification to a single user.

**Example:**
```php
$success = $this->notificationService->notifyUser(
    $employee,
    new VehicleReservationNotification(
        destination: 'Rabat',
        carId: 2,
        demandeId: 456
    ),
    async: true
);
```

**Returns:** `true` if sent successfully, `false` otherwise

**Logs:** All failures logged to `storage/logs/laravel.log`

---

### 3. `notifyByRoles(string|array, Notification, bool $async = true): array`

Send notification to users with specific role(s).

**Example - Single Role:**
```php
$this->notificationService->notifyByRoles(
    roles: 'admin',
    notification: $notification,
    async: true
);
```

**Example - Multiple Roles:**
```php
$this->notificationService->notifyByRoles(
    roles: ['admin', 'super_admin'],
    notification: $notification,
    async: true
);
```

**Available Roles:**
- `'super_admin'` - Full access
- `'admin'` - Administrative access
- `'employee'` - Basic user access

**Returns:**
```php
[
    'sent' => 5,
    'failed' => 1,
    'recipients' => 6
]
```

---

### 4. `notifyUsers(Collection, Notification, bool $async = true): array`

Send notification to multiple users at once.

**Example:**
```php
$users = User::where('service', 'Commerciale')->get();

$this->notificationService->notifyUsers(
    $users,
    new SystemUpdateNotification(
        title: 'Service Update',
        message: 'New policy for Commerciale service',
        url: route('dashboard')
    ),
    async: true
);
```

**Returns:** Statistics array with `sent`, `failed`, `recipients`

---

### 5. `notifyByRolesExcept(string|array, User, Notification, bool $async = true): array`

Send notification to all users with role(s) EXCEPT a specific user.

**Example:**
```php
// Notify all admins except the one who triggered this
$this->notificationService->notifyByRolesExcept(
    roles: ['admin', 'super_admin'],
    exceptUser: $currentAdmin,
    notification: $notification,
    async: true
);
```

**Use Cases:**
- Don't notify the user who triggered an action
- Notify team members except the requester

---

### 6. `notifyUserWithFallback(User, Notification, bool $async = true): bool`

Send notification with graceful fallback on email failure.

**Example:**
```php
$this->notificationService->notifyUserWithFallback(
    $user,
    $notification,
    async: true
);
```

**Behavior:**
1. Try to send notification (database + email)
2. If email fails, fallback to database-only notification
3. Return `true` if either succeeded, `false` if both failed

**Use Cases:**
- When email is not critical (non-blocking)
- Want to ensure in-app notification always succeeds

---

## Notification Types

### RequestSubmittedNotification

Sent when employee creates a new reservation request.

**Constructor:**
```php
new RequestSubmittedNotification(
    employeeName: string,
    destination: string,
    carId: ?int = null,
    demandeId: ?int = null
)
```

**Recipients:** Admin, Super Admin only

**Channels:** Database + Email

**Example:**
```php
$notificationService->notifyAllAdmins(
    new RequestSubmittedNotification(
        employeeName: 'Fatima Mahmoud',
        destination: 'Casablanca - Jorf Lasfar',
        carId: 5,
        demandeId: 789
    )
);
```

---

### RequestStatusUpdatedNotification

Sent when admin approves, rejects, or cancels a request.

**Constructor:**
```php
new RequestStatusUpdatedNotification(
    status: 'approuvee' | 'rejetee' | 'annulee' | 'pending',
    destination: string,
    demandeId: ?int = null
)
```

**Recipients:** Employee who made the request

**Channels:** Database + Email

**Example:**
```php
$notificationService->notifyUser(
    $employee,
    new RequestStatusUpdatedNotification(
        status: 'approuvee',
        destination: 'Marrakech',
        demandeId: 789
    )
);
```

---

### VehicleReservationNotification

Confirmation when employee successfully creates a request.

**Constructor:**
```php
new VehicleReservationNotification(
    destination: string,
    carId: ?int = null,
    demandeId: ?int = null
)
```

**Recipients:** All roles

**Channels:** Database + Email

**Example:**
```php
$notificationService->notifyUser(
    $sender,
    new VehicleReservationNotification(
        destination: 'Jorf Lasfar',
        carId: 3,
        demandeId: 790
    )
);
```

---

### SystemUpdateNotification

General system updates (profile changes, password reset, etc.)

**Constructor:**
```php
new SystemUpdateNotification(
    title: string,
    message: string,
    url: string
)
```

**Recipients:** All roles

**Channels:** Database + Email

**Example:**
```php
$notificationService->notifyUser(
    $user,
    new SystemUpdateNotification(
        title: 'Profil mis à jour',
        message: 'Vos informations ont été modifiées.',
        url: route('settings.show')
    )
);
```

---

## Async vs Sync

### Synchronous (Sync)

When `async: false`:
```php
$notificationService->notifyUser($user, $notification, async: false);
```

- Notification sent immediately
- Blocks the request until complete
- Better for testing
- Not recommended for production

### Asynchronous (Queue)

When `async: true` (default):
```php
$notificationService->notifyUser($user, $notification, async: true);
```

- Notification queued for background processing
- Request completes immediately
- Much faster for users
- Requires queue worker running
- Recommended for production

**To start queue worker:**
```bash
php artisan queue:work
```

---

## Role-Based Filtering

Each notification automatically filters by allowed roles.

**In RequestSubmittedNotification:**
```php
protected array $allowedRoles = ['admin', 'super_admin'];
```

If employee receives this notification, it's silently ignored (not sent).

**To see who will receive:**
```php
$user = User::find(1);
$notification = new RequestSubmittedNotification(...);

if ($notification->shouldNotify($user)) {
    echo "User will receive this notification";
} else {
    echo "User will NOT receive this notification";
}
```

---

## Error Handling

### Logging

All notification operations are logged:

```bash
tail -f storage/logs/laravel.log
```

You'll see entries like:
```
[2026-04-28 10:30:45] local.INFO: Notification sent successfully {"user_id":1,"notification":"RequestSubmittedNotification"}
[2026-04-28 10:30:46] local.ERROR: Failed to send notification {"user_id":2,"notification":"...","error":"Connection timeout"}
```

### Graceful Failures

If email sending fails:
1. Attempt is logged
2. In-app database notification still created
3. User alerted via in-app notification
4. Admin can see failed emails in logs

### Retry Failed Notifications

Check failed queue jobs:
```bash
php artisan queue:failed
```

Retry all failed jobs:
```bash
php artisan queue:retry all
```

---

## Real-World Examples

### Example 1: Complete Request Workflow

```php
// 1. Employee creates request
$demande = Demande::create([...]);

// 2. Notify admins about new request
$this->notificationService->notifyAllAdmins(
    new RequestSubmittedNotification(
        employeeName: Auth::user()->name,
        destination: $demande->destination,
        carId: $demande->car_id,
        demandeId: $demande->id
    )
);

// 3. Notify employee about confirmation
$this->notificationService->notifyUser(
    Auth::user(),
    new VehicleReservationNotification(
        destination: $demande->destination,
        carId: $demande->car_id,
        demandeId: $demande->id
    )
);
```

### Example 2: Admin Approves Request

```php
public function approve(Request $request, $id)
{
    $demande = Demande::find($id);
    $demande->update(['status' => 'approved']);
    
    // Notify employee about approval
    $this->notificationService->notifyUser(
        $demande->user,
        new RequestStatusUpdatedNotification(
            status: 'approuvee',
            destination: $demande->destination,
            demandeId: $demande->id
        )
    );
    
    // Notify other admins about this action
    $this->notificationService->notifyByRolesExcept(
        roles: ['admin', 'super_admin'],
        exceptUser: Auth::user(),
        notification: new SystemUpdateNotification(
            title: 'Demande approuvée',
            message: "La demande #{$demande->id} a été approuvée par " . Auth::user()->name,
            url: route('dashboard')
        )
    );
}
```

### Example 3: Bulk Notification to Department

```php
$commercialeUsers = User::whereHas('roles', function ($q) {
    $q->where('name', 'employee');
})
->where('service', 'Commerciale')
->get();

$this->notificationService->notifyUsers(
    $commercialeUsers,
    new SystemUpdateNotification(
        title: 'Nouvelle politique départementale',
        message: 'Une nouvelle politique s\'applique à votre département',
        url: route('settings.show')
    )
);
```

---

## Testing

### Unit Test Example

```php
use App\Services\NotificationService;
use App\Notifications\SystemUpdateNotification;

class NotificationTest extends TestCase
{
    public function test_notification_sent_to_correct_user()
    {
        $user = User::factory()->create();
        $service = app(NotificationService::class);
        
        $result = $service->notifyUser(
            $user,
            new SystemUpdateNotification(...),
            async: false  // Use sync for testing
        );
        
        $this->assertTrue($result);
        
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $user->id,
        ]);
    }
}
```

### Manual Testing

```bash
# Test system notification
php artisan notifications:test --user-id=1 --type=system

# Test all notifications
php artisan notifications:test --user-id=1 --type=all

# Test specific user
php artisan notifications:test --user-id=5 --type=submitted
```

---

## Best Practices

✅ **Do:**
- Use async for production (better UX)
- Always include demandeId for tracking
- Test with Mailtrap first
- Monitor logs regularly
- Use notifyAllAdmins() for consistency

❌ **Don't:**
- Use sync in production (blocks requests)
- Send notifications in tight loops
- Hardcode role names
- Forget to handle failures
- Ignore queue worker monitoring

---

## FAQ

**Q: How do I send notifications to multiple users?**
A: Use `notifyUsers()` with a collection:
```php
$users = User::where(...)->get();
$this->notificationService->notifyUsers($users, $notification);
```

**Q: What if email sending fails?**
A: Use `notifyUserWithFallback()` to fallback to database-only notification.

**Q: How do I test notifications?**
A: Use the test command: `php artisan notifications:test --user-id=1`

**Q: Where are sent notifications stored?**
A: In `notifications` table. Query: `SELECT * FROM notifications;`

**Q: How do I retry failed notifications?**
A: Run: `php artisan queue:retry all`

---

For complete documentation, see `NOTIFICATIONS_SYSTEM.md`
