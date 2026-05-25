# 📧 Quick Setup: Email Notifications for SDCC Vehicle Reservation System

## ⚡ Quick Start (Choose One)

### Option 1: Testing with Mailtrap (Recommended for Development)

**Time to setup: 3 minutes**

1. Go to https://mailtrap.io (free account)
2. Sign up and create a new Inbox
3. In your Inbox, click "Show Credentials" 
4. Copy the SMTP credentials
5. Update `.env`:
```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=paste_your_username_here
MAIL_PASSWORD=paste_your_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@sdcc.ma"
MAIL_FROM_NAME="SDCC — Réservation Véhicule"
```

6. Clear cache:
```bash
php artisan config:cache
```

7. Test:
```bash
php artisan notifications:test --user-id=1 --type=system
```

All test emails appear in https://mailtrap.io inbox!

---

### Option 2: Production with Gmail (App Password)

**Time to setup: 5 minutes**

1. Enable 2FA on your Google Account:
   - Visit https://myaccount.google.com/security
   - Enable "2-Step Verification"

2. Create App Password:
   - Visit https://myaccount.google.com/apppasswords
   - Select "Mail" and "Windows Computer"
   - Copy the 16-character password

3. Update `.env`:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your_16_char_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@sdcc.ma"
MAIL_FROM_NAME="SDCC — Réservation Véhicule"
```

4. Clear cache:
```bash
php artisan config:cache
```

5. Test:
```bash
php artisan notifications:test --user-id=1 --type=all
```

Check your Gmail inbox (and spam folder) for test emails!

---

### Option 3: Production with SendGrid (Recommended for Production)

**Time to setup: 5 minutes**

1. Create account at https://sendgrid.com

2. Create API Key:
   - Settings → API Keys
   - Create key with "Mail Send" permission
   - Copy the API key

3. Update `.env`:
```
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=your_sendgrid_api_key_here
MAIL_FROM_ADDRESS="noreply@sdcc.ma"
MAIL_FROM_NAME="SDCC — Réservation Véhicule"
```

4. Clear cache:
```bash
php artisan config:cache
```

5. Test:
```bash
php artisan notifications:test --user-id=1 --type=all
```

---

## 🚀 Queue Configuration

### Development (Synchronous - Process Immediately)

In `.env`:
```
QUEUE_CONNECTION=sync
```

Emails send immediately but may block requests. Good for testing.

### Production (Asynchronous - Background Processing)

In `.env`:
```
QUEUE_CONNECTION=database
```

Then start the queue worker:
```bash
php artisan queue:work
```

The worker processes notifications in the background. Much faster!

---

## 📝 Testing the System

### Quick Test (Single Notification)

```bash
php artisan notifications:test --user-id=1 --type=system
```

### Test All Notification Types

```bash
php artisan notifications:test --user-id=1 --type=all
```

### Test Specific User

```bash
php artisan notifications:test --user-id=3 --type=status
```

Available types: `all`, `system`, `submitted`, `status`, `reservation`

---

## 🔍 Where to Check Results

### 1. Check In-App Notifications
- Login to application
- Click notification bell icon 
- You should see the test notification

### 2. Check Email
- **Mailtrap**: Visit https://mailtrap.io → Your Inbox
- **Gmail**: Check inbox and spam folder
- **SendGrid**: Check your monitored email address

### 3. Check Database

Database notification:
```sql
SELECT * FROM notifications 
WHERE user_id = 1 
ORDER BY created_at DESC 
LIMIT 5;
```

Audit log:
```sql
SELECT * FROM notification_logs 
WHERE user_id = 1 
ORDER BY created_at DESC 
LIMIT 10;
```

---

## 🛠️ Troubleshooting

### "Notification not received"
1. Check `.env` MAIL_MAILER is set correctly
2. Run migrations: `php artisan migrate`
3. Check logs: `tail -f storage/logs/laravel.log`

### "Email sends but content looks strange"
1. Clear config cache: `php artisan config:cache`
2. Restart queue worker (if using database queue)

### "Queue not processing"
1. Check QUEUE_CONNECTION is set to 'database'
2. Run migrations: `php artisan migrate`
3. Start queue worker: `php artisan queue:work`

### "Test command not found"
1. Run: `composer dump-autoload`
2. Run: `php artisan list` (should see notifications:test)

---

## 📚 Complete System Documentation

For complete documentation including all notification types, role-based filtering, and advanced options:

```
See: NOTIFICATIONS_SYSTEM.md
```

---

## ✅ Verification Checklist

- [ ] `.env` file configured with mail credentials
- [ ] `php artisan config:cache` run
- [ ] Database migrations completed: `php artisan migrate`
- [ ] Test notification command successful
- [ ] Notification appears in in-app notifications
- [ ] Email received in configured email service
- [ ] Queue worker running (if using async): `php artisan queue:work`

---

## 🆘 Still Having Issues?

1. Check complete docs: `NOTIFICATIONS_SYSTEM.md`
2. Review `.env` configuration
3. Check `storage/logs/laravel.log` for errors
4. Try Mailtrap first (eliminates email service issues)
5. Verify roles: `SELECT * FROM role_has_permissions WHERE role_id = 1;`

---

**Need help?** Check the notification system logs and Laravel documentation at https://laravel.com/docs/notifications
