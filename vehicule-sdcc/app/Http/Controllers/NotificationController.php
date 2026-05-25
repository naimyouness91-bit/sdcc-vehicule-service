<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class NotificationController extends Controller
{
    /**
     * Get notifications for the authenticated user.
     * Ensures proper user isolation - users can only see their own notifications.
     */
    public function index()
    {
        $user = Auth::user();

        // Verify user is authenticated
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $user->notifications()
                ->latest()
                ->limit(10)
                ->get()
                ->map(function ($notification) use ($user) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->data['title'] ?? 'Notification',
                        'message' => $notification->data['message'] ?? '',
                        'url' => $this->getRedirectUrlForUser($user, $notification),
                        'read_at' => $notification->read_at,
                        'created_at' => $notification->created_at?->diffForHumans(),
                    ];
                }),
        ]);
    }

    /**
     * Mark a notification as read.
     * Ensures users can only mark their own notifications.
     */
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

    /**
     * Mark all notifications as read for the authenticated user.
     * Only marks the current user's notifications.
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        
        // Only mark this user's unread notifications
        $user->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Delete a notification.
     * Ensures users can only delete their own notifications.
     */
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        
        // Verify the notification belongs to this user
        $notification = $user->notifications()
            ->where('id', $id)
            ->firstOrFail();
        
        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Determine the correct redirect URL based on user role and notification type.
     * Prevents unauthorized access to admin pages.
     */
    private function getRedirectUrlForUser($user, $notification): string
    {
        $notificationType = $notification->data['type'] ?? null;
        $demandeId = $notification->data['meta']['demande_id'] ?? null;

        // Admin/Super Admin can access admin pages
        if ($user->hasAnyRole(['admin', 'super_admin'])) {
            return match($notificationType) {
                'request_submitted' => route('admin.data-management'),
                'request_status_updated' => route('mes-demandes.index'),
                'reservation_confirmed' => route('mes-demandes.index'),
                'vehicle_mileage_exceeded' => route('admin.data-management'),
                'vehicle_mileage_warning' => route('admin.data-management'),
                'vehicle_changed' => route('admin.data-management'),
                default => route('dashboard')
            };
        }

        // Employees can only access employee pages
        return match($notificationType) {
            'request_status_updated' => route('mes-demandes.index'),
            'reservation_confirmed' => route('mes-demandes.index'),
            default => route('dashboard')
        };
    }
}
