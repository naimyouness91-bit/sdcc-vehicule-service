<?php
/**
 * ╔════════════════════════════════════════════════════════════════════════════════╗
 * ║                      NOTIFICATION SYSTEM DOCUMENTATION                         ║
 * ║                   Email + Queue + Role-Based Implementation                    ║
 * ╚════════════════════════════════════════════════════════════════════════════════╝
 * 
 * This document describes the complete notification system implementation for the
 * SDCC Vehicle Reservation application. The system supports:
 * 
 * ✓ Email notifications with professional HTML templates
 * ✓ In-app database notifications
 * ✓ Role-based filtering (admin, super_admin, employee)
 * ✓ Queue-based async sending for performance
 * ✓ Graceful fallback on email failures
 * ✓ Audit logging of all notifications sent
 * 
 * ════════════════════════════════════════════════════════════════════════════════
 * 1. ARCHITECTURE OVERVIEW
 * ════════════════════════════════════════════════════════════════════════════════
 * 
 * Components:
 * 
 *   App\Services\NotificationService
 *   └─ High-level API for sending notifications
 *   └─ Handles role-based filtering
 *   └─ Supports batch and single user notifications
 *   └─ Provides fallback strategies
 *   
 *   App\Notifications\Traits\RoleAwareNotification
 *   └─ Trait for notifications to declare allowed roles
 *   └─ Implements shouldNotify() for filtering
 *   └─ Used by all notification classes
 *   
 *   Notification Classes (implements ShouldQueue)
 *   ├─ RequestSubmittedNotification (admin + super_admin only)
 *   ├─ RequestStatusUpdatedNotification (employee + admin)
 *   ├─ VehicleReservationNotification (all roles)
 *   └─ SystemUpdateNotification (all roles)
 *   
 *   Controllers
 *   ├─ MesDemandesController (uses NotificationService)
 *   └─ SettingsController (uses NotificationService)
 * 
 * ════════════════════════════════════════════════════════════════════════════════
 * 2. CONFIGURATION
 * ════════════════════════════════════════════════════════════════════════════════
 * 
 * A. Email Configuration (.env)
 * ─────────────────────────────
 * 
 * Option 1: Mailtrap (Testing)
 * ────────────────────────────
 * MAIL_MAILER=smtp
 * MAIL_HOST=sandbox.smtp.mailtrap.io
 * MAIL_PORT=2525
 * MAIL_USERNAME=your_mailtrap_username
 * MAIL_PASSWORD=your_mailtrap_password
 * MAIL_ENCRYPTION=tls
 * MAIL_FROM_ADDRESS="noreply@sdcc.ma"
 * MAIL_FROM_NAME="SDCC — Réservation Véhicule"
 * 
 * How to set up Mailtrap:
 * 1. Visit https://mailtrap.io
 * 2. Create a free account
 * 3. Create an Inbox for testing
 * 4. Copy credentials from "SMTP Settings" into .env
 * 5. Run: php artisan config:cache
 * 
 * Option 2: Gmail SMTP (Production with App Password)
 * ─────────────────────────────────────────────────
 * MAIL_MAILER=smtp
 * MAIL_HOST=smtp.gmail.com
 * MAIL_PORT=587
 * MAIL_USERNAME=your-email@gmail.com
 * MAIL_PASSWORD=your_app_password_16_chars  # NOT your regular password!
 * MAIL_ENCRYPTION=tls
 * MAIL_FROM_ADDRESS="noreply@sdcc.ma"
 * MAIL_FROM_NAME="SDCC — Réservation Véhicule"
 * 
 * How to create Gmail App Password:
 * 1. Enable 2FA on your Google Account
 * 2. Visit: https://myaccount.google.com/apppasswords
 * 3. Select "Mail" and "Windows Computer" (or device type)
 * 4. Copy the 16-character password
 * 5. Use it as MAIL_PASSWORD in .env
 * 
 * Option 3: SendGrid (Recommended for Production)
 * ───────────────────────────────────────────────
 * MAIL_MAILER=sendgrid
 * SENDGRID_API_KEY=your_sendgrid_api_key
 * MAIL_FROM_ADDRESS="noreply@sdcc.ma"
 * MAIL_FROM_NAME="SDCC — Réservation Véhicule"
 * 
 * How to set up SendGrid:
 * 1. Create account at https://sendgrid.com
 * 2. Create an API Key in Settings > API Keys
 * 3. Add to .env as shown above
 * 
 * B. Queue Configuration (.env)
 * ─────────────────────────────
 * 
 * Development (Synchronous) - Process immediately:
 * QUEUE_CONNECTION=sync
 * 
 * Production (Asynchronous) - Uses database:
 * QUEUE_CONNECTION=database
 * 
 * For database queue, first run migrations:
 * php artisan migrate
 * Then start the queue worker:
 * php artisan queue:work
 * 
 * ════════════════════════════════════════════════════════════════════════════════
 * 3. USAGE EXAMPLES
 * ════════════════════════════════════════════════════════════════════════════════
 * 
 * A. Notify Admins About New Request
 * ──────────────────────────────────
 * 
 *   use App\Services\NotificationService;
 *   use App\Notifications\RequestSubmittedNotification;
 *   
 *   $notificationService->notifyAllAdmins(
 *       new RequestSubmittedNotification(
 *           employeeName: 'Jean Dupont',
 *           destination: 'Casablanca',
 *           carId: 5,
 *           demandeId: 123
 *       ),
 *       async: true  // Queue for async sending
 *   );
 * 
 * Output:
 *   [
 *       'sent' => 3,
 *       'failed' => 0,
 *       'recipients' => 3
 *   ]
 * 
 * 
 * B. Notify Specific User
 * ──────────────────────
 * 
 *   $notificationService->notifyUser(
 *       $employee,
 *       new RequestStatusUpdatedNotification(
 *           status: 'approuvee',
 *           destination: 'Marrakech',
 *           demandeId: 123
 *       ),
 *       async: true
 *   );
 * 
 * 
 * C. Notify by Roles
 * ──────────────────
 * 
 *   $notificationService->notifyByRoles(
 *       roles: ['admin', 'super_admin'],
 *       notification: $notification,
 *       async: true
 *   );
 * 
 * 
 * D. With Fallback (email failure → database only)
 * ────────────────────────────────────────────────
 * 
 *   $notificationService->notifyUserWithFallback(
 *       $user,
 *       $notification,
 *       async: true
 *   );
 * 
 * ════════════════════════════════════════════════════════════════════════════════
 * 4. NOTIFICATION CLASSES
 * ════════════════════════════════════════════════════════════════════════════════
 * 
 * A. RequestSubmittedNotification
 * ───────────────────────────────
 * 
 * Triggered: When an employee creates a new reservation request
 * Recipients: Admin + Super Admin roles only
 * Channels: Database + Email
 * 
 * Example:
 *   new RequestSubmittedNotification(
 *       employeeName: 'Ahmed Ali',
 *       destination: 'Jorf Lasfar',
 *       carId: 2,
 *       demandeId: 45
 *   )
 * 
 * Email Subject: "Nouvelle demande de réservation véhicule"
 * 
 * 
 * B. RequestStatusUpdatedNotification
 * ───────────────────────────────────
 * 
 * Triggered: When admin approves, rejects, or cancels a request
 * Recipients: Employee who made the request
 * Channels: Database + Email
 * Statuses: 'approuvee', 'rejetee', 'annulee', 'pending'
 * 
 * Example:
 *   new RequestStatusUpdatedNotification(
 *       status: 'approuvee',
 *       destination: 'Rabat',
 *       demandeId: 45
 *   )
 * 
 * Email Subject: "Statut de votre demande - **APPROUVÉE** ✓"
 * 
 * 
 * C. VehicleReservationNotification
 * ──────────────────────────────────
 * 
 * Triggered: Confirmation when employee successfully creates a request
 * Recipients: All roles
 * Channels: Database + Email
 * 
 * Example:
 *   new VehicleReservationNotification(
 *       destination: 'Marrakech',
 *       carId: 3,
 *       demandeId: 46
 *   )
 * 
 * Email Subject: "Confirmation de réservation véhicule"
 * 
 * 
 * D. SystemUpdateNotification
 * ──────────────────────────
 * 
 * Triggered: System updates (profile changes, password reset, etc.)
 * Recipients: All roles
 * Channels: Database + Email
 * 
 * Example:
 *   new SystemUpdateNotification(
 *       title: 'Profil mis à jour',
 *       message: 'Vos informations ont été modifiées.',
 *       url: route('settings.show')
 *   )
 * 
 * Email Subject: "Profil mis à jour"
 * 
 * ════════════════════════════════════════════════════════════════════════════════
 * 5. DATABASE SETUP
 * ════════════════════════════════════════════════════════════════════════════════
 * 
 * Run all migrations:
 * 
 *   php artisan migrate
 * 
 * This creates:
 * ├─ jobs (queue table for async jobs)
 * ├─ notifications (in-app notifications table)
 * └─ notification_logs (audit trail)
 * 
 * Schema: notification_logs
 * ├─ id (Primary Key)
 * ├─ user_id (Foreign Key → users)
 * ├─ notification_type (e.g., 'RequestSubmittedNotification')
 * ├─ notification_data (JSON)
 * ├─ channel (database, mail, etc.)
 * ├─ status (pending, sent, failed)
 * ├─ error_message (if failed)
 * ├─ retry_count (number of retry attempts)
 * ├─ targeted_roles (JSON array of roles)
 * ├─ related_id & related_type (for linking to demandes, etc.)
 * ├─ sent_at (when successfully sent)
 * ├─ created_at & updated_at
 * └─ Indexes on user_id, notification_type, status, created_at
 * 
 * ════════════════════════════════════════════════════════════════════════════════
 * 6. QUEUE WORKER SETUP (Production)
 * ════════════════════════════════════════════════════════════════════════════════
 * 
 * For async email sending in production:
 * 
 * 1. Update .env:
 *    QUEUE_CONNECTION=database
 * 
 * 2. Start queue worker:
 *    php artisan queue:work
 * 
 * 3. (Optional) Run with supervisor for auto-restart:
 *    Install supervisor: apt-get install supervisor
 *    Configure: /etc/supervisor/conf.d/laravel-worker.conf
 *    Start: supervisorctl reread && supervisorctl update && supervisorctl start laravel-worker
 * 
 * 4. Monitor queue:
 *    php artisan queue:failed    # See failed jobs
 *    php artisan queue:retry all # Retry failed jobs
 *    php artisan queue:flush     # Clear all jobs
 * 
 * ════════════════════════════════════════════════════════════════════════════════
 * 7. ROLE-BASED FILTERING
 * ════════════════════════════════════════════════════════════════════════════════
 * 
 * Each notification class can declare which roles should receive it:
 * 
 *   class MyNotification extends Notification implements ShouldQueue
 *   {
 *       use Queueable, RoleAwareNotification;
 *       
 *       // Only admins receive this notification
 *       protected array $allowedRoles = ['admin', 'super_admin'];
 *       
 *       public function via($notifiable)
 *       {
 *           // RoleAwareNotification trait checks shouldNotify()
 *           // If user doesn't have allowed role, returns []
 *           return ['database', 'mail'];
 *       }
 *   }
 * 
 * Available roles in system:
 * ├─ 'super_admin' - Full access, can manage all
 * ├─ 'admin' - Can approve/reject requests, manage vehicles
 * └─ 'employee' - Can create and view own requests
 * 
 * ════════════════════════════════════════════════════════════════════════════════
 * 8. TROUBLESHOOTING
 * ════════════════════════════════════════════════════════════════════════════════
 * 
 * Issue: Emails not sending
 * ─────────────────────────
 * 1. Check MAIL_MAILER in .env (should be 'smtp')
 * 2. Test credentials with: php artisan tinker
 *    → Mail::send('mail.test', [], function ($m) { ... })
 * 3. Check logs: storage/logs/laravel.log
 * 4. Try Mailtrap for testing first (free service)
 * 
 * Issue: Notifications not queuing
 * ────────────────────────────────
 * 1. Check QUEUE_CONNECTION in .env
 * 2. If using 'database', ensure jobs table exists: php artisan migrate
 * 3. Start queue worker: php artisan queue:work
 * 4. Check failed jobs: php artisan queue:failed
 * 
 * Issue: "Notification class not found"
 * ─────────────────────────────────────
 * 1. Ensure notification is in App\Notifications\
 * 2. Check namespace declaration
 * 3. Run: composer dump-autoload
 * 
 * Issue: Email sends but role filtering not working
 * ──────────────────────────────────────────────────
 * 1. Ensure notification uses RoleAwareNotification trait
 * 2. Check $allowedRoles array is set correctly
 * 3. Verify user has correct roles: $user->getRoleNames()
 * 4. Check via() method calls shouldNotify()
 * 
 * ════════════════════════════════════════════════════════════════════════════════
 * 9. TESTING THE SYSTEM
 * ════════════════════════════════════════════════════════════════════════════════
 * 
 * Quick Test via Artisan Tinker:
 * ──────────────────────────────
 * 
 *   php artisan tinker
 *   
 *   $user = User::find(1);
 *   $service = app(NotificationService::class);
 *   $service->notifyUser($user, new SystemUpdateNotification(
 *       'Test',
 *       'This is a test',
 *       url('/')
 *   ), false);
 * 
 * Check sent emails in Mailtrap:
 * ───────────────────────────────
 *   1. Visit https://mailtrap.io
 *   2. Select your Inbox
 *   3. View the "Mail" tab to see all sent messages
 *   4. Click on a message to see full headers and body
 * 
 * Database Notification:
 * ─────────────────────
 *   SELECT * FROM notifications ORDER BY created_at DESC LIMIT 10;
 *   
 * Audit Log:
 * ──────────
 *   SELECT * FROM notification_logs ORDER BY created_at DESC LIMIT 20;
 * 
 * ════════════════════════════════════════════════════════════════════════════════
 * 10. PRODUCTION BEST PRACTICES
 * ════════════════════════════════════════════════════════════════════════════════
 * 
 * ✓ Always use async queue (QUEUE_CONNECTION=database or redis)
 * ✓ Use professional email service (SendGrid, AWS SES, not Mailtrap)
 * ✓ Implement rate limiting for high-volume notifications
 * ✓ Set up proper error monitoring (Sentry, etc.)
 * ✓ Use supervisor to keep queue worker running
 * ✓ Regular backup of notification_logs table
 * ✓ Monitor failed jobs and retry regularly
 * ✓ Use proper TLS/SSL encryption for SMTP
 * ✓ Implement opt-out mechanism for users
 * ✓ Test email deliverability (SPF, DKIM, DMARC)
 * 
 * ════════════════════════════════════════════════════════════════════════════════
 */