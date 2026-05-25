<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Notifications\Notification;

/**
 * NotificationPolicy
 * 
 * Handles authorization for notification access.
 * Ensures users can only view, update, and delete their own notifications.
 */
class NotificationPolicy
{
    /**
     * Determine if the user can view a notification.
     * Users can only view their own notifications.
     */
    public function view(User $user, $notification): bool
    {
        return $notification->notifiable_id === $user->id;
    }

    /**
     * Determine if the user can update a notification.
     * Users can only update (mark as read) their own notifications.
     */
    public function update(User $user, $notification): bool
    {
        return $notification->notifiable_id === $user->id;
    }

    /**
     * Determine if the user can delete a notification.
     * Users can only delete their own notifications.
     */
    public function delete(User $user, $notification): bool
    {
        return $notification->notifiable_id === $user->id;
    }

    /**
     * Determine if the user can view all notifications.
     * Only admins and super_admins can view the notifications management page.
     */
    public function viewNotificationsList(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super_admin']);
    }

    /**
     * Determine if the user can access the admin notifications page.
     * Only admins and super_admins can access this.
     */
    public function accessAdminNotifications(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super_admin']);
    }
}
