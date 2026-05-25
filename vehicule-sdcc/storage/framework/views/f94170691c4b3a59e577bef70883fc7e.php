<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SDCC — Notification</title>
    <style>
        /* ── Reset ──────────────────────────────────────────────── */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f0f4f0;
            color: #1f2937;
            -webkit-text-size-adjust: 100%;
        }

        /* ── Outer wrapper ──────────────────────────────────────── */
        .email-wrapper {
            width: 100%;
            background-color: #f0f4f0;
            padding: 40px 16px;
        }

        /* ── Container ──────────────────────────────────────────── */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(46, 125, 50, 0.12);
        }

        /* ── Header ─────────────────────────────────────────────── */
        .email-header {
            background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 45%, #388e3c 100%);
            padding: 32px 40px;
            text-align: center;
        }

        .email-header .logo-badge {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 14px;
            padding: 10px 20px;
            margin-bottom: 12px;
        }

        .email-header .logo-text {
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 2px;
        }

        .email-header .logo-sub {
            display: block;
            font-size: 11px;
            color: rgba(255,255,255,0.8);
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* ── Accent bar ─────────────────────────────────────────── */
        .accent-bar {
            height: 4px;
            background: linear-gradient(90deg, #ffa726 0%, #fb8c00 50%, #ffa726 100%);
        }

        /* ── Body ───────────────────────────────────────────────── */
        .email-body {
            padding: 40px 40px 32px;
        }

        .greeting {
            font-size: 20px;
            font-weight: 700;
            color: #1b5e20;
            margin-bottom: 20px;
        }

        /* Paragraphs / lines */
        .email-body p {
            font-size: 15px;
            line-height: 1.7;
            color: #374151;
            margin-bottom: 14px;
        }

        /* ── CTA Button ─────────────────────────────────────────── */
        .btn-wrapper {
            text-align: center;
            margin: 28px 0;
        }

        .btn-cta {
            display: inline-block;
            background: linear-gradient(135deg, #2e7d32 0%, #388e3c 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            padding: 14px 32px;
            border-radius: 50px;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 14px rgba(46, 125, 50, 0.35);
        }

        /* ── Info panel ─────────────────────────────────────────── */
        .info-panel {
            background: #f0faf0;
            border-left: 4px solid #2e7d32;
            border-radius: 0 8px 8px 0;
            padding: 16px 20px;
            margin: 20px 0;
            font-size: 14px;
            color: #1f2937;
            line-height: 1.6;
        }

        /* ── Divider ────────────────────────────────────────────── */
        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 28px 0;
        }

        /* ── Subcopy ─────────────────────────────────────────────── */
        .subcopy {
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 20px 40px;
            font-size: 12px;
            color: #6b7280;
            line-height: 1.6;
        }

        .subcopy a {
            color: #2e7d32;
            word-break: break-all;
        }

        /* ── Footer ─────────────────────────────────────────────── */
        .email-footer {
            background: #1b5e20;
            padding: 24px 40px;
            text-align: center;
        }

        .email-footer p {
            font-size: 12px;
            color: rgba(255,255,255,0.65);
            line-height: 1.7;
        }

        .email-footer .footer-brand {
            color: #ffa726;
            font-weight: 700;
            font-size: 13px;
        }

        /* ── Responsive ─────────────────────────────────────────── */
        @media (max-width: 600px) {
            .email-body    { padding: 28px 24px 20px; }
            .email-header  { padding: 24px 20px; }
            .subcopy       { padding: 16px 24px; }
            .email-footer  { padding: 20px 24px; }
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="email-container">

        
        <div class="email-header">
            <div class="logo-badge">
                <span class="logo-text">SDCC</span>
                <span class="logo-sub">Réservation Véhicule de Service</span>
            </div>
        </div>

        
        <div class="accent-bar"></div>

        
        <div class="email-body">
            <?php echo e($slot); ?>

        </div>

        
        <?php if(isset($subcopy)): ?>
        <div class="subcopy">
            <?php echo e($subcopy); ?>

        </div>
        <?php endif; ?>

        
        <div class="email-footer">
            <p class="footer-brand">SDCC — Système de Réservation de Véhicules</p>
            <p>Cet e-mail a été envoyé automatiquement. Merci de ne pas y répondre.</p>
            <p style="margin-top:8px;">© <?php echo e(date('Y')); ?> SDCC. Tous droits réservés.</p>
        </div>

    </div>
</div>
</body>
</html>
<?php /**PATH C:\Users\PC\Desktop\projet-sdcc\Reservation-Vehicule-Service\vehicule-sdcc\resources\views/vendor/mail/html/layout.blade.php ENDPATH**/ ?>