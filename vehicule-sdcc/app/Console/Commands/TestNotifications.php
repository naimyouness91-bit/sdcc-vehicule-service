<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\RequestStatusUpdatedNotification;
use App\Notifications\RequestSubmittedNotification;
use App\Notifications\SystemUpdateNotification;
use App\Notifications\VehicleReservationNotification;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestNotifications extends Command
{
    protected $signature = 'notifications:test {--user-id=1} {--type=all}';
    protected $description = 'Test notification system with various notification types';

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle(): void
    {
        $userId = (int) $this->option('user-id');
        $type = (string) $this->option('type');

        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return;
        }

        $this->info("📧 Testing Notification System");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->line("User: {$user->name} ({$user->email})");
        $this->line("Roles: " . $user->getRoleNames()->join(', '));
        $this->line("Queue Mode: " . config('queue.default'));
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        $tests = [];

        if ($type === 'all' || $type === 'system') {
            $tests['system'] = [
                'name' => '🔔 SystemUpdateNotification',
                'notification' => new SystemUpdateNotification(
                    title: 'Test Notification',
                    message: 'This is a test notification from console command.',
                    url: route('dashboard')
                ),
            ];
        }

        if ($type === 'all' || $type === 'submitted') {
            $tests['submitted'] = [
                'name' => '📝 RequestSubmittedNotification',
                'notification' => new RequestSubmittedNotification(
                    employeeName: 'Test Employee',
                    destination: 'Test Destination',
                    carId: 1,
                    demandeId: 999
                ),
            ];
        }

        if ($type === 'all' || $type === 'status') {
            $tests['status'] = [
                'name' => '✅ RequestStatusUpdatedNotification (Approved)',
                'notification' => new RequestStatusUpdatedNotification(
                    status: 'approuvee',
                    destination: 'Test Destination',
                    demandeId: 999
                ),
            ];
        }

        if ($type === 'all' || $type === 'reservation') {
            $tests['reservation'] = [
                'name' => '🚗 VehicleReservationNotification',
                'notification' => new VehicleReservationNotification(
                    destination: 'Test Location',
                    carId: 1,
                    demandeId: 999
                ),
            ];
        }

        if (empty($tests)) {
            $this->error("Invalid notification type: {$type}");
            $this->info("Available types: all, system, submitted, status, reservation");
            return;
        }

        $this->info("\nRunning tests...\n");

        foreach ($tests as $testKey => $test) {
            $this->line($test['name']);

            try {
                $result = $this->notificationService->notifyUser(
                    $user,
                    $test['notification'],
                    async: false  // Send immediately for testing
                );

                $this->info("✓ Sent successfully\n");
            } catch (\Exception $e) {
                $this->error("✗ Failed: " . $e->getMessage() . "\n");
                Log::error("Notification test failed: " . $e->getMessage());
            }
        }

        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("✅ Tests completed!");
        $this->line("\n📌 Next steps:");
        $this->line("1. Check in-app notifications: Notifications dropdown");
        $this->line("2. Check email (Mailtrap/Gmail/SendGrid)");
        $this->line("3. Check database: SELECT * FROM notifications ORDER BY created_at DESC;");
        $this->line("4. Check audit log: SELECT * FROM notification_logs ORDER BY created_at DESC;");
    }
}
