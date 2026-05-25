<?php $__env->startSection('title', 'SDCC - Mon Profil'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .profile-page {
        max-width: 820px;
        margin: 0 auto;
    }

    .profile-header {
        background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 30%, #FFA726 100%);
        color: #fff;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }

    .profile-header h1 {
        font-size: 26px;
        margin: 0 0 6px 0;
    }

    .profile-header p {
        margin: 0;
        font-size: 13px;
        opacity: 0.95;
    }

    .profile-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        padding: 24px;
    }

    .profile-top {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 22px;
        padding-bottom: 18px;
        border-bottom: 1px solid #eee;
    }

    .profile-avatar {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 22px;
    }

    .profile-name {
        font-size: 22px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
    }

    .profile-role {
        margin: 2px 0 0 0;
        font-size: 13px;
        color: #666;
        text-transform: capitalize;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .profile-field {
        background: #f9fafb;
        border: 1px solid #eceff1;
        border-radius: 10px;
        padding: 12px;
    }

    .profile-label {
        font-size: 11px;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 6px;
    }

    .profile-value {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a1a;
        word-break: break-word;
    }

    @media (max-width: 700px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="content-wrapper profile-page">
    <div class="profile-header">
        <h1>Mon Profil</h1>
        <p>Informations du compte connecté</p>
    </div>

    <div class="profile-card">
        <div class="profile-top">
            <div class="profile-avatar"><?php echo e(strtoupper(substr($user->name ?? 'U', 0, 1))); ?></div>
            <div>
                <h2 class="profile-name"><?php echo e($user->name ?? 'Utilisateur'); ?></h2>
                <p class="profile-role"><?php echo e($user->primaryRole()); ?></p>
            </div>
        </div>

        <div class="profile-grid">
            <div class="profile-field">
                <div class="profile-label">Nom complet</div>
                <div class="profile-value"><?php echo e($user->name ?? '-'); ?></div>
            </div>
            <div class="profile-field">
                <div class="profile-label">Email</div>
                <div class="profile-value"><?php echo e($user->email ?? '-'); ?></div>
            </div>
            <div class="profile-field">
                <div class="profile-label">Rôle</div>
                <div class="profile-value"><?php echo e($user->primaryRole()); ?></div>
            </div>
            <div class="profile-field">
                <div class="profile-label">Service</div>
                <div class="profile-value"><?php echo e($user->service ?? 'Non renseigné'); ?></div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\PC\Desktop\projet-sdcc\Reservation-Vehicule-Service\vehicule-sdcc\resources\views/profile/show.blade.php ENDPATH**/ ?>