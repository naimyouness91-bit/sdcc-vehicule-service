<?php $__env->startSection('content'); ?>
<style>
    /* Admin Data pages: green/orange-only theme */
    .admin-data-page {
        padding: 25px 10px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .admin-data-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .admin-data-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .admin-data-title i {
        font-size: 28px;
        background: linear-gradient(135deg, #2E7D32 0%, #FFA726 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .admin-data-title h1 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: #1f2937;
    }

    .admin-data-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .action-btn {
        background: #2E7D32;
        color: #fff;
        border: none;
        padding: 10px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 700;
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .action-btn:hover {
        background: #256628;
        transform: translateY(-1px);
        box-shadow: 0 10px 18px rgba(46, 125, 50, 0.18);
    }

    .action-btn.secondary {
        background: #FFA726;
        color: #1f2937;
    }

    .action-btn.secondary:hover {
        background: #FB8C00;
        box-shadow: 0 10px 18px rgba(255, 167, 38, 0.22);
    }

    /* Data Tables */
    .data-table-container {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px;
        border-bottom: 1px solid #eef2f7;
        gap: 14px;
        flex-wrap: wrap;
    }

    .search-box {
        flex: 1;
        min-width: 240px;
    }

    .search-box input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-size: 13px;
        transition: box-shadow 0.15s ease, border-color 0.15s ease;
    }

    .search-box input:focus {
        outline: none;
        border-color: rgba(46, 125, 50, 0.6);
        box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.12);
    }

    .filter-select {
        padding: 10px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-size: 13px;
        background: #fff;
    }

    .filter-select:focus {
        outline: none;
        border-color: rgba(255, 167, 38, 0.7);
        box-shadow: 0 0 0 4px rgba(255, 167, 38, 0.15);
    }

    .table-wrapper {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    thead {
        background: rgba(46, 125, 50, 0.06);
        border-bottom: 2px solid rgba(46, 125, 50, 0.12);
    }

    th {
        padding: 14px 14px;
        text-align: left;
        font-weight: 800;
        color: #1f2937;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.4px;
        border: none;
    }

    td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #111827;
    }

    tbody tr:hover {
        background: rgba(255, 167, 38, 0.06);
    }

    /* Status Badges */
    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        background: rgba(255, 167, 38, 0.14);
        color: #8a4d00;
    }

    .status-badge.disponible,
    .status-badge.approved {
        background: rgba(46, 125, 50, 0.16);
        color: #1b5e20;
    }

    .status-badge.maintenance,
    .status-badge.pending {
        background: rgba(255, 167, 38, 0.18);
        color: #9a4f00;
    }

    /* Action Buttons in Table */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .icon-btn {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        background: rgba(46, 125, 50, 0.12);
        color: #1b5e20;
    }

    .icon-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 18px rgba(0, 0, 0, 0.10);
        background: rgba(46, 125, 50, 0.18);
    }

    .icon-btn.edit {
        background: rgba(255, 167, 38, 0.18);
        color: #8a4d00;
    }

    .icon-btn.edit:hover {
        background: rgba(255, 167, 38, 0.26);
    }

    .icon-btn.delete {
        background: rgba(255, 167, 38, 0.22);
        color: #7a3e00;
    }

    .icon-btn.delete:hover {
        background: rgba(255, 167, 38, 0.32);
    }

    /* Empty State */
    .empty-state {
        padding: 60px 20px;
        text-align: center;
        color: #6b7280;
    }

    .empty-state i {
        font-size: 44px;
        color: rgba(46, 125, 50, 0.35);
        margin-bottom: 10px;
    }
</style>

<div class="admin-data-page">
    <div class="admin-data-header">
        <div class="admin-data-title">
            <i class="<?php echo e($icon ?? 'fas fa-database'); ?>"></i>
            <h1><?php echo e($title ?? 'Gestion des données'); ?></h1>
        </div>

        <div class="admin-data-actions">
            <button class="action-btn secondary" type="button" onclick="window.print()">
                <i class="fas fa-print"></i>
                Imprimer
            </button>
        </div>
    </div>

    <?php echo $__env->yieldContent('admin-data-content'); ?>
</div>

<script>
    // Minimal table helpers for all data pages.
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.search-box input').forEach((input) => {
            input.addEventListener('keyup', function () {
                const container = this.closest('.data-table-container');
                if (!container) return;
                const table = container.querySelector('table');
                if (!table) return;

                const rows = table.querySelectorAll('tbody tr');
                const q = (this.value || '').toLowerCase();
                rows.forEach((row) => {
                    const text = (row.textContent || '').toLowerCase();
                    row.style.display = text.includes(q) ? '' : 'none';
                });
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\PC\Desktop\projet-sdcc\Reservation-Vehicule-Service\vehicule-sdcc\resources\views/layouts/admin-data.blade.php ENDPATH**/ ?>