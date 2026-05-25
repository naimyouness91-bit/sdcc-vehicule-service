<?php if(Auth::user()->hasAnyRole(['admin', 'super_admin'])): ?>
    <?php $__env->startSection('title', 'SDCC - Gestion des Véhicules'); ?>
<?php else: ?>
    <?php $__env->startSection('title', 'SDCC - Véhicules'); ?>
<?php endif; ?>

<?php $__env->startSection('content'); ?>

<?php if(Auth::user()->hasAnyRole(['admin', 'super_admin'])): ?>
    <?php echo $__env->make('cars.admin-index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php else: ?>
    <?php echo $__env->make('cars.employee-index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\PC\Desktop\projet-sdcc\Reservation-Vehicule-Service\vehicule-sdcc\resources\views/cars/index.blade.php ENDPATH**/ ?>