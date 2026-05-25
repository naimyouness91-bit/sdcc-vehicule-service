
<?php $__env->startSection('title', 'SDCC - Gestion des utilisateurs'); ?>
<?php $__env->startSection('content'); ?>

<style>
    :root {
        --primary: #10b981;
        --primary-light: #d1fae5;
        --primary-dark: #047857;
        --accent: #f97316;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --bg-light: #f9fafb;
        --bg-white: #ffffff;
        --border: #e5e7eb;
    }

    .content-wrapper {
        max-width: none;
        width: 100%;
        margin: 0;
        padding: 16px 20px;
        box-sizing: border-box;
    }

    /* ============ PAGE HEADER ============ */
    .page-header {
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
    }

    .page-header h1 {
        margin: 0 0 8px 0;
        font-size: 32px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .page-header p {
        margin: 0;
        color: var(--text-secondary);
        font-size: 14px;
    }

    /* ============ STATS GRID ============ */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--bg-white);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        border-top: 4px solid var(--primary);
        transition: all 0.3s ease;
        cursor: pointer;
        user-select: none;
    }

    .stat-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
        border-color: var(--primary);
    }

    .stat-card:focus {
        outline: none;
        box-shadow: 0 0 0 4px rgba(16,185,129,0.12);
        transform: translateY(-2px);
    }

    .stat-card.active {
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25);
        background: rgba(16, 185, 129, 0.05);
        border: 2px solid var(--primary);
        border-top: 4px solid var(--primary);
    }

    .stat-card.secondary.active {
        box-shadow: 0 6px 16px rgba(139, 92, 246, 0.25);
        background: rgba(139, 92, 246, 0.05);
        border: 2px solid #8b5cf6;
        border-top: 4px solid #8b5cf6;
    }

    .stat-card.danger.active {
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.25);
        background: rgba(59, 130, 246, 0.05);
        border: 2px solid #3b82f6;
        border-top: 4px solid #3b82f6;
    }

    .stat-card.accent.active {
        box-shadow: 0 6px 16px rgba(249, 115, 22, 0.25);
        background: rgba(249, 115, 22, 0.05);
        border: 2px solid var(--accent);
        border-top: 4px solid var(--accent);
    }

    .stat-card.secondary {
        border-top-color: #8b5cf6;
    }

    .stat-card.danger {
        border-top-color: #3b82f6;
    }

    .stat-card.accent {
        border-top-color: var(--accent);
    }

    .stat-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-secondary);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-primary);
    }

    /* ============ PANEL ============ */
    .panel {
        background: var(--bg-white);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border);
    }

    .panel-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* ============ BUTTONS ============ */
    .btn {
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--accent));
        color: white;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
    }

    .btn-secondary {
        background: var(--bg-white);
        color: var(--text-primary);
        border: 1px solid var(--border);
    }

    .btn-success {
        background: var(--success);
        color: white;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
        font-size: 12px;
        padding: 8px 12px;
    }

    .btn-success:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }

    .btn-warning {
        background: var(--warning);
        color: white;
        box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
        font-size: 12px;
        padding: 8px 12px;
    }

    .btn-warning:hover {
        background: #d97706;
        transform: translateY(-1px);
    }

    .btn-danger {
        background: var(--danger);
        color: white;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);
        font-size: 12px;
        padding: 8px 12px;
    }

    .btn-danger:hover {
        background: #dc2626;
        transform: translateY(-1px);
    }

    .btn-sm {
        padding: 6px 10px;
        font-size: 11px;
    }

    .btn i {
        font-size: 14px;
    }

    /* ============ FORM STYLING ============ */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.full {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-primary);
        margin-bottom: 6px;
    }

    .form-control {
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    /* ============ SEARCH & FILTER ============ */
    .search-filter {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
        align-items: center;
        padding: 16px;
        background: var(--bg-light);
        border-radius: 8px;
    }

    .search-wrapper {
        flex: 1;
        position: relative;
        min-width: 250px;
    }

    .search-wrapper i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        pointer-events: none;
    }

    .search-wrapper input {
        width: 100%;
        padding-left: 36px;
    }

    .filter-group {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .filter-group label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
    }

    /* ============ TABLE ============ */
    .table-wrapper {
        overflow-x: auto;
        border-radius: 8px;
        border: 1px solid var(--border);
    }

    .users-table {
        width: 100%;
        border-collapse: collapse;
    }

    .users-table thead {
        background: var(--bg-light);
        border-bottom: 2px solid var(--border);
    }

    .users-table th {
        padding: 14px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-secondary);
    }

    .users-table th i {
        margin-right: 6px;
        color: var(--primary);
    }

    .users-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background 0.2s ease;
    }

    .users-table tbody tr:hover {
        background: var(--bg-light);
    }

    .users-table td {
        padding: 14px 16px;
        font-size: 14px;
        color: var(--text-primary);
        vertical-align: middle;
    }

    .user-cell {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .user-name {
        font-weight: 600;
        color: var(--text-primary);
    }

    .user-email {
        color: var(--text-secondary);
        font-size: 12px;
    }

    /* ============ BADGES ============ */
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    .badge-primary {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-success {
        background: var(--primary-light);
        color: var(--primary-dark);
    }

    .badge-warning {
        background: #fef3c7;
        color: #b45309;
    }

    .badge-danger {
        background: #fecaca;
        color: #991b1b;
    }

    .badge-active {
        background: #dcfce7;
        color: #15803d;
    }

    .badge-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    /* ============ ACTIONS CELL ============ */
    .actions-cell {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .action-btn {
        padding: 6px 10px;
        font-size: 11px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .action-edit {
        background: #dbeafe;
        color: #1e40af;
    }

    .action-edit:hover {
        background: #bfdbfe;
    }

    .action-reset {
        background: #fed7aa;
        color: #b45309;
    }

    .action-reset:hover {
        background: #fdba74;
    }

    .action-toggle {
        background: #fecaca;
        color: #991b1b;
    }

    .action-toggle:hover {
        background: #fca5a5;
    }

    .action-delete {
        background: #fee2e2;
        color: #7f1d1d;
    }

    .action-delete:hover {
        background: #fecaca;
    }

    /* ============ NO DATA ============ */
    .no-data {
        padding: 48px;
        text-align: center;
        color: var(--text-secondary);
    }

    .no-data i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.3;
    }

    /* ============ RESPONSIVE ============ */
    @media (max-width: 1024px) {
        .form-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .search-filter {
            flex-direction: column;
            align-items: stretch;
        }

        .search-wrapper {
            min-width: unset;
        }

        .actions-cell {
            flex-direction: column;
        }

        .action-btn {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .users-table {
            font-size: 12px;
        }

        .users-table th,
        .users-table td {
            padding: 10px 8px;
        }

        .panel-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
    }
</style>

<div class="content-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1><i class="fas fa-users" style="margin-right: 8px;"></i>Gestion des Utilisateurs</h1>
            <p>Gérez les accès et les rôles de tous les collaborateurs</p>
        </div>
        <a href="<?php echo e(route('utilisateurs.create')); ?>" class="btn btn-primary" style="align-self: flex-start;">
            <i class="fas fa-user-plus"></i>
            Créer Utilisateur
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="panel" style="border-left:4px solid #16a34a;margin-bottom:16px;">
            <div style="padding:12px 16px;color:#064e3b;">✅ <?php echo e(session('success')); ?></div>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="panel" style="border-left:4px solid #dc2626;margin-bottom:16px;">
            <div style="padding:12px 16px;color:#7f1d1d;">❌ <?php echo e(session('error')); ?></div>
        </div>
    <?php endif; ?>

    <!-- Stats Section -->
    <div class="stats-grid">
        <?php
            $totalUsers = $users->count();
            $superAdmins = $users->filter(fn($u) => $u->hasRole('super_admin'))->count();
            $admins = $users->filter(fn($u) => $u->hasRole('admin'))->count();
            $employees = $users->filter(fn($u) => $u->hasRole('employee'))->count();
        ?>
        
        <div class="stat-card" data-filter="" title="Afficher tous les utilisateurs" role="button" tabindex="0" aria-pressed="false">
            <div class="stat-label"><i class="fas fa-users"></i>Total comptes</div>
            <div class="stat-value"><?php echo e($totalUsers); ?></div>
        </div>
        
        <div class="stat-card secondary" data-filter="super_admin" title="Afficher les Super Admins" role="button" tabindex="0" aria-pressed="false">
            <div class="stat-label"><i class="fas fa-user-shield"></i>Super Admins</div>
            <div class="stat-value"><?php echo e($superAdmins); ?></div>
        </div>
        
        <div class="stat-card danger" data-filter="admin" title="Afficher les Admins" role="button" tabindex="0" aria-pressed="false">
            <div class="stat-label"><i class="fas fa-user-tie"></i>Admins</div>
            <div class="stat-value"><?php echo e($admins); ?></div>
        </div>
        
        <div class="stat-card accent" data-filter="employee" title="Afficher les Employés" role="button" tabindex="0" aria-pressed="false">
            <div class="stat-label"><i class="fas fa-user-clock"></i>Employés</div>
            <div class="stat-value"><?php echo e($employees); ?></div>
        </div>
    </div>

    <!-- Users Listing -->
    <div class="panel">
        <div class="panel-header">
            <h3 class="panel-title"><i class="fas fa-table"></i>Liste des utilisateurs</h3>
        </div>

        <!-- Search & Filter -->
        <div class="search-filter">
            <div class="search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control" id="userSearch" placeholder="Rechercher par nom, email...">
            </div>
            <div class="filter-group">
                <label><i class="fas fa-filter"></i>Filtrer par rôle</label>
                <select class="form-control" id="roleFilter">
                    <option value="">Tous les rôles</option>
                    <option value="super_admin">Super Admin</option>
                    <option value="admin">Admin</option>
                    <option value="employee">Employé</option>
                </select>
                <button id="resetFilterBtn" type="button" class="btn btn-sm btn-secondary" style="margin-left:8px;">Réinitialiser</button>
            </div>
        </div>

        <!-- Table -->
        <?php if($users->isEmpty()): ?>
            <div class="no-data">
                <i class="fas fa-inbox"></i>
                <p>Aucun utilisateur trouvé.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user"></i>Nom</th>
                            <th><i class="fas fa-envelope"></i>Email</th>
                            <th><i class="fas fa-shield-halved"></i>Rôle</th>
                            <th><i class="fas fa-briefcase"></i>Service</th>
                            <th><i class="fas fa-circle-check"></i>État</th>
                            <th><i class="fas fa-sliders"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $role = $user->hasRole('super_admin') ? 'super_admin' : ($user->hasRole('admin') ? 'admin' : 'employee');
                                $roleLabel = $role === 'super_admin' ? 'Super Admin' : ($role === 'admin' ? 'Admin' : 'Employé');
                                $badgeClass = $role === 'super_admin' ? 'badge-primary' : ($role === 'admin' ? 'badge-warning' : 'badge-success');
                                $isActive = $user->is_active ?? true;
                            ?>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-name"><?php echo e($user->name); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="user-email"><?php echo e($user->email); ?></div>
                                </td>
                                <td>
                                    <span class="badge <?php echo e($badgeClass); ?>"><?php echo e($roleLabel); ?></span>
                                </td>
                                <td><?php echo e($user->service ?? '-'); ?></td>
                                <td>
                                    <span class="badge <?php echo e($isActive ? 'badge-active' : 'badge-inactive'); ?>">
                                        <?php echo e($isActive ? '✓ Actif' : '✗ Inactif'); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <!-- Edit: redirect to edit form (simple inline form fallback) -->
                                        <form method="GET" action="<?php echo e(route('utilisateurs.edit', $user)); ?>" style="display:inline-block;margin:0;">
                                            <?php echo csrf_field(); ?>
                                            <button type="button" class="action-btn action-edit" title="Modifier" onclick="location.href='<?php echo e(route('utilisateurs.edit', $user)); ?>'">
                                                <i class="fas fa-edit"></i>Modifier
                                            </button>
                                        </form>

                                        <!-- Reset password: redirect to form -->
                                        <a href="<?php echo e(route('utilisateurs.reset-password-form', $user)); ?>" class="action-btn action-reset" title="Réinitialiser mot de passe">
                                            <i class="fas fa-key"></i>Reset Pass
                                        </a>

                                        <!-- Toggle active/inactive -->
                                        <?php if($isActive): ?>
                                            <form method="POST" action="<?php echo e(route('utilisateurs.deactivate', $user)); ?>" style="display:inline-block;margin:0;">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="action-btn action-toggle" title="Désactiver">
                                                    <i class="fas fa-ban"></i>Désactiver
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" action="<?php echo e(route('utilisateurs.reactivate', $user)); ?>" style="display:inline-block;margin:0;">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="action-btn action-toggle" title="Activer">
                                                    <i class="fas fa-check"></i>Activer
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- Delete -->
                                        <form method="POST" action="<?php echo e(route('utilisateurs.destroy', $user)); ?>" style="display:inline-block;margin:0;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="action-btn action-delete" title="Supprimer">
                                                <i class="fas fa-trash"></i>Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('userSearch');
        const roleFilter = document.getElementById('roleFilter');
        const statCards = document.querySelectorAll('.stat-card');
        const table = document.querySelector('.users-table tbody');
        
        if (!table) return;
        
        const rows = Array.from(table.querySelectorAll('tr'));
        let currentFilter = '';
        
        function applyFilters() {
            const searchTerm = (searchInput?.value || '').toLowerCase().trim();
            const selectedRole = roleFilter?.value || '';
            
            rows.forEach(row => {
                const nameEmail = (row.textContent || '').toLowerCase();
                const rowRole = row.getAttribute('data-role') || '';
                
                const matchesSearch = !searchTerm || nameEmail.includes(searchTerm);
                const matchesRole = !selectedRole || rowRole === selectedRole;
                
                row.style.display = (matchesSearch && matchesRole) ? '' : 'none';
            });
        }
        
        function updateStatCardStates() {
            statCards.forEach(card => {
                const filter = card.getAttribute('data-filter') || '';
                if (filter === currentFilter) {
                    card.classList.add('active');
                    card.setAttribute('aria-pressed', 'true');
                } else {
                    card.classList.remove('active');
                    card.setAttribute('aria-pressed', 'false');
                }
            });
        }
        
        // Add data-role attribute to rows for filtering
        rows.forEach(row => {
            const roleCell = row.querySelector('.badge');
            if (roleCell) {
                const roleText = roleCell.textContent.trim().toLowerCase();
                const role = roleText.includes('super admin') ? 'super_admin' : (roleText.includes('admin') ? 'admin' : 'employee');
                row.setAttribute('data-role', role);
            }
        });
        
        // Stat card click & keyboard handler
        statCards.forEach(card => {
            card.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter') || '';
                currentFilter = filter;
                roleFilter.value = filter;
                updateStatCardStates();
                applyFilters();
            });

            // allow Enter and Space to activate the card
            card.addEventListener('keydown', function(ev) {
                if (ev.key === 'Enter' || ev.key === ' ') {
                    ev.preventDefault();
                    this.click();
                }
            });
        });
        
        // Search input handler
        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
        }
        
        // Role filter dropdown handler
        if (roleFilter) {
            roleFilter.addEventListener('change', function() {
                currentFilter = this.value;
                updateStatCardStates();
                applyFilters();
            });
        }
        
        // Reset Filter button
        const resetBtn = document.getElementById('resetFilterBtn');
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                if (searchInput) searchInput.value = '';
                if (roleFilter) {
                    roleFilter.value = '';
                    currentFilter = '';
                }
                updateStatCardStates();
                applyFilters();
            });
        }
    });
</script>

<script>
    // User management filters and search functionality
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\PC\Desktop\projet-sdcc\Reservation-Vehicule-Service\vehicule-sdcc\resources\views/utilisateurs/index.blade.php ENDPATH**/ ?>