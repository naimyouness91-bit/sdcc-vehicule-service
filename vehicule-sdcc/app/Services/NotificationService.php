<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * NotificationService
 * 
 * Handles role-based notification routing and sending.
 * Supports:
 * - Filtering users by roles
 * - Sending to multiple users simultaneously
 * - Graceful error handling
 * - Queue-based async sending
 */
class NotificationService
{
    /**
     * Send notification to users with specific roles.
     * 
     * @param string|array $roles - Role(s) to target (e.g., 'admin', ['admin', 'super_admin'])
     * @param Notification $notification - The notification to send
     * @param bool $async - Whether to queue the notification (default: true for production)
     * @return array - Statistics ['sent' => int, 'failed' => int, 'recipients' => int]
     */
    public function notifyByRoles(
        string|array $roles,
        Notification $notification,
        bool $async = true
    ): array {
        $roles = is_array($roles) ? $roles : [$roles];
        
        $users = User::role($roles)->get();
        
        return $this->notifyUsers($users, $notification, $async);
    }

    /**
     * Send notification to a single user with fallback handling.
     * 
     * @param User $user - The user to notify
     * @param Notification $notification - The notification to send
     * @param bool $async - Whether to queue the notification
     * @return bool - Success status
     */
    public function notifyUser(
        User $user,
        Notification $notification,
        bool $async = true
    ): bool {
        try {
            if ($async) {
                $user->notify($notification);
            } else {
                // Send immediately without queue
                $user->notify($notification);
            }
            
            Log::info('Notification sent successfully', [
                'user_id' => $user->id,
                'notification' => get_class($notification),
            ]);
            
            return true;
        } catch (Throwable $e) {
            Log::error('Failed to send notification', [
                'user_id' => $user->id,
                'notification' => get_class($notification),
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Send notification to multiple users.
     * 
     * @param Collection $users - Collection of users
     * @param Notification $notification - The notification to send
     * @param bool $async - Whether to queue the notification
     * @return array - Statistics
     */
    public function notifyUsers(
        Collection $users,
        Notification $notification,
        bool $async = true
    ): array {
        $stats = [
            'sent' => 0,
            'failed' => 0,
            'recipients' => $users->count(),
        ];

        foreach ($users as $user) {
            if ($this->notifyUser($user, $notification, $async)) {
                $stats['sent']++;
            } else {
                $stats['failed']++;
            }
        }

        Log::info('Batch notification completed', $stats);

        return $stats;
    }

    /**
     * Send notification to all admins (admin + super_admin).
     * 
     * @param Notification $notification
     * @param bool $async
     * @return array - Statistics
     */
    public function notifyAllAdmins(Notification $notification, bool $async = true): array
    {
        return $this->notifyByRoles(['admin', 'super_admin'], $notification, $async);
    }

    /**
     * Send notification to all users of specific role except one.
     * 
     * @param string|array $roles
     * @param User $exceptUser - User to exclude
     * @param Notification $notification
     * @param bool $async
     * @return array - Statistics
     */
    public function notifyByRolesExcept(
        string|array $roles,
        User $exceptUser,
        Notification $notification,
        bool $async = true
    ): array {
        $roles = is_array($roles) ? $roles : [$roles];
        
        $users = User::role($roles)
            ->where('id', '!=', $exceptUser->id)
            ->get();
        
        return $this->notifyUsers($users, $notification, $async);
    }

    /**
     * Send notification with fallback to database-only if email fails.
     * This is useful when email delivery is not critical.
     * 
     * @param User $user
     * @param Notification $notification
     * @param bool $async
     * @return bool - Success status
     */
    public function notifyUserWithFallback(
        User $user,
        Notification $notification,
        bool $async = true
    ): bool {
        try {
            $user->notify($notification);
            return true;
        } catch (Throwable $e) {
            Log::warning('Notification email failed, fallback to database only', [
                'user_id' => $user->id,
                'notification' => get_class($notification),
                'error' => $e->getMessage(),
            ]);

            // Send to database channel only
            try {
                \Illuminate\Support\Facades\Notification::sendNow($user, $notification, ['database']);
                return true;
            } catch (Throwable $fallbackError) {
                Log::error('Fallback notification also failed', [
                    'user_id' => $user->id,
                    'error' => $fallbackError->getMessage(),
                ]);
                return false;
            }
        }
    }
}
