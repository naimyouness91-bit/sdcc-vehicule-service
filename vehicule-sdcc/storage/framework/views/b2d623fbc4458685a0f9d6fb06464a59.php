

<?php $__env->startSection('title', 'SDCC - Planification des affectations'); ?>

<?php $__env->startSection('content'); ?>
<style>
    /* ===== ROOT STYLES ===== */
    :root {
        --color-primary: #4CAF50;
        --color-primary-light: #66BB6A;
        --color-primary-dark: #2E7D32;
        --color-orange: #FFA726;
        --color-orange-light: #FFB74D;
        --color-orange-dark: #E65100;
        --color-red: #e53935;
        --color-red-dark: #c62828;
        --color-green: #43a047;
        --color-gray-light: #f5f5f5;
        --color-gray-medium: #ddd;
        --color-gray-dark: #666;
        --color-text-primary: #1a1a1a;
        --color-text-secondary: #666;
        --color-text-tertiary: #999;
        --shadow-sm: 0 1px 4px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 2px 8px rgba(0, 0, 0, 0.08);
        --shadow-lg: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    * { box-sizing: border-box; }
    body { margin: 0; }

    /* ===== PAGE CONTAINER (USE FULL AVAILABLE WIDTH) ===== */
    .planning-page {
        max-width: none;
        width: 100%;
        margin: 0; /* use full width of content area */
        padding: 16px 20px; /* small outer padding to breathe */
        display: flex;
        flex-direction: column;
        gap: 0;
        box-sizing: border-box;
    }

    /* ===== HEADER SECTION (Dashboard Style) ===== */
    .planning-header {
        background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 25%, #FFA726 75%, #FFA500 100%);
        color: white;
        padding: 20px 24px;
        border-radius: 10px;
        margin-bottom: 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .header-left h1 {
        margin: 0 0 5px 0;
        font-size: 24px;
        font-weight: 700;
        color: white;
    }

    .header-left h1 i {
        margin-right: 8px;
    }

    .header-left p {
        margin: 0;
        font-size: 13px;
        color: rgba(255, 255, 255, 0.9);
    }

    .header-right {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .export-btn {
        background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .export-btn:hover {
        background: linear-gradient(135deg, #2E7D32 0%, #E65100 100%);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
        transform: translateY(-2px);
    }

    .date-selector {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.4);
        color: white;
        padding: 12px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .date-selector:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.6);
    }

    #datePickerInput {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.4);
        color: white;
        padding: 12px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    #datePickerInput:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.6);
    }

    #datePickerInput::-webkit-calendar-picker-indicator {
        cursor: pointer;
        filter: invert(1);
    }

    .date-selector option {
        background: white;
        color: #1a1a1a;
    }

    /* ===== STATS GRID (Dashboard Style) ===== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 18px;
        margin-bottom: 20px;
        margin-top: 18px;
        padding: 0; /* remove inner paddings so cards stretch */
        width: 100%;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        border-left: 5px solid transparent;
        border-image: linear-gradient(180deg, #4CAF50 0%, #FFA726 100%) 1;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s;
        display: block;
        text-decoration: none;
        color: inherit;
        cursor: pointer;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 16px rgba(76, 175, 80, 0.15);
    }

    .stat-card:focus-visible {
        outline: 3px solid rgba(255, 167, 38, 0.45);
        outline-offset: 2px;
    }

    .stat-card.orange {
        border-left-color: #FFA726;
    }

    .stat-card.green {
        border-left-color: #66BB6A;
    }

    .stat-card.red {
        border-left-color: #e53935;
    }

    .stat-card.gray {
        border-left-color: #999;
    }

    .stat-value {
        font-size: 28px;
        font-weight: bold;
        color: #1a1a1a;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-subtitle {
        font-size: 11px;
        color: #999;
        margin-top: 5px;
    }
    .filter-tabs {
        display: flex;
        gap: 12px;
        margin-bottom: 18px;
        border-bottom: 1px solid var(--color-gray-medium);
        flex-wrap: wrap;
        padding: 0; /* align to edges */
        width: 100%;
        box-sizing: border-box;
    }

    .filter-tab {
        background: none;
        border: none;
        padding: 12px 18px;
        font-weight: 600;
        font-size: 13px;
        color: var(--color-text-secondary);
        cursor: pointer;
        position: relative;
        transition: all 0.3s ease;
        border-bottom: 3px solid transparent;
        margin-bottom: -1px;
    }

    .filter-tab:hover {
        color: var(--color-text-primary);
    }

    .filter-tab.active {
        color: var(--color-primary);
        border-bottom-color: var(--color-primary);
    }

    .filter-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--color-gray-light);
        border-radius: 12px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 700;
        margin-left: 6px;
        min-width: 20px;
    }

    .filter-tab.active .filter-badge {
        background: var(--color-primary);
        color: white;
    }

    /* ===== TIMELINE & CARDS ===== */
    .reservations-timeline {
        display: flex;
        flex-direction: column;
        gap: 16px;
        padding: 0 0 20px 0; /* let cards fill width */
        width: 100%;
        box-sizing: border-box;
    }

    .reservation-card {
        background: white;
        border-radius: 10px;
        border-left: 5px solid var(--color-primary);
        padding: 0;
        display: grid;
        grid-template-columns: 90px 1fr 260px; /* fixed avatar, flexible content, actions column */
        gap: 0;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .reservation-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .reservation-card.pending {
        border-left-color: var(--color-orange);
    }

    .reservation-card.approved {
        border-left-color: var(--color-primary);
    }

    .reservation-card.rejected {
        border-left-color: var(--color-red);
    }

    .reservation-card.cancelled {
        border-left-color: #999;
    }

    /* Card Avatar Section */
    .card-avatar-section {
        padding: 18px;
        background: #fafafa;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 90px;
        border-right: 1px solid var(--color-gray-medium);
    }

    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 18px;
        flex-shrink: 0;
    }

    .reservation-card.pending .user-avatar {
        background: linear-gradient(135deg, var(--color-orange-light), var(--color-orange));
    }

    .reservation-card.approved .user-avatar {
        background: linear-gradient(135deg, var(--color-primary-light), var(--color-primary));
    }

    .reservation-card.rejected .user-avatar {
        background: linear-gradient(135deg, #ef5350, var(--color-red));
    }

    .reservation-card.cancelled .user-avatar {
        background: linear-gradient(135deg, #bbb, #999);
    }

    /* Card Content Section */
    .card-content-section {
        padding: 16px 22px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 10px;
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-name {
        font-weight: 700;
        font-size: 15px;
        color: var(--color-text-primary);
    }

    .user-email {
        font-size: 12px;
        color: var(--color-text-tertiary);
    }

    .card-title {
        font-weight: 600;
        font-size: 14px;
        color: var(--color-text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-title i {
        color: var(--color-primary);
        font-size: 13px;
    }

    .card-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 18px;
        font-size: 13px;
        color: var(--color-text-secondary);
        margin-top: 8px;
    }

    .card-detail-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .card-detail-item i {
        color: var(--color-primary);
        font-size: 11px;
        min-width: 12px;
    }

    .card-vehicle {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #fff8e1;
        padding: 4px 10px;
        border-radius: 4px;
        border: 1px solid #ffe082;
        font-family: 'Courier New', monospace;
        font-size: 11px;
        font-weight: 600;
        width: fit-content;
        margin-top: 6px;
    }

    /* Card Actions Section */
    .card-actions-section {
        padding: 14px 18px;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: center;
        gap: 12px;
        border-left: 1px solid var(--color-gray-medium);
        min-width: 220px; /* larger to keep actions readable */
    }

    .status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .status-badge.pending {
        background: #fff3e0;
        color: var(--color-orange-dark);
    }

    .status-badge.approved {
        background: #e8f5e9;
        color: var(--color-primary-dark);
    }

    .status-badge.rejected {
        background: #ffebee;
        color: var(--color-red-dark);
    }

    .status-badge.cancelled {
        background: #f5f5f5;
        color: #666;
    }

    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-end;
    }

    .action-btn {
        padding: 8px 14px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        white-space: nowrap;
    }

    .action-btn.approve {
        background: #e8f5e9;
        color: var(--color-primary-dark);
        border: 1px solid #c8e6c9;
    }

    .action-btn.approve:hover {
        background: var(--color-primary);
        color: white;
        border-color: var(--color-primary);
    }

    .action-btn.reject {
        background: #ffebee;
        color: var(--color-red-dark);
        border: 1px solid #ffcdd2;
    }

    .action-btn.reject:hover {
        background: var(--color-red);
        color: white;
        border-color: var(--color-red);
    }

    .action-btn.cancel {
        background: #f5f5f5;
        color: #666;
        border: 1px solid #ddd;
    }

    .action-btn.cancel:hover {
        background: #e8e8e8;
        border-color: #999;
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        text-align: center;
        padding: 60px 40px;
        background: white;
        border-radius: 10px;
        color: var(--color-text-tertiary);
    }

    .empty-state-icon {
        font-size: 56px;
        color: #e0e0e0;
        margin-bottom: 16px;
    }

    .empty-state h3 {
        margin: 0 0 8px;
        font-size: 18px;
        color: var(--color-text-secondary);
        font-weight: 700;
    }

    .empty-state p {
        margin: 0;
        font-size: 13px;
        color: #ccc;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
        .planning-header {
            margin-bottom: 20px;
            padding: 20px;
        }

        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 16px;
            padding: 0 20px;
            margin-top: 20px;
        }

        .filter-tabs {
            padding: 0 20px;
        }

        .reservations-timeline {
            padding: 0 20px 20px 20px;
        }

        .card-details {
            grid-template-columns: auto auto;
            gap: 16px;
        }

        .card-actions-section {
            min-width: auto;
        }

        .action-buttons {
            gap: 6px;
        }

        .action-btn {
            padding: 7px 12px;
            font-size: 11px;
        }
    }

    @media (max-width: 768px) {
        .planning-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .header-left h1 {
            font-size: 22px;
        }

        .header-right {
            width: 100%;
            gap: 8px;
        }

        .export-btn {
            flex: 1;
            justify-content: center;
        }

        .date-selector {
            width: 100%;
        }

        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 12px;
            margin-bottom: 24px;
            padding: 0 15px;
            margin-top: 20px;
        }

        .stat-value {
            font-size: 22px;
        }

        .stat-label {
            font-size: 11px;
        }

        .stat-subtitle {
            font-size: 10px;
        }

        .filter-tabs {
            padding: 0 15px;
            gap: 8px;
        }

        .filter-tab {
            padding: 10px 14px;
            font-size: 12px;
        }

        .reservations-timeline {
            padding: 0 15px 15px 15px;
        }

        .reservation-card {
            grid-template-columns: 1fr;
        }

        .card-avatar-section {
            border-right: none;
            border-bottom: 1px solid var(--color-gray-medium);
            padding: 12px;
            min-width: auto;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            font-size: 14px;
        }

        .card-content-section {
            padding: 14px;
        }

        .card-details {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .card-actions-section {
            border-left: none;
            border-top: 1px solid var(--color-gray-medium);
            padding: 12px;
            align-items: flex-start;
            gap: 8px;
            flex-direction: row;
            justify-content: space-between;
        }

        .action-buttons {
            gap: 6px;
            order: 2;
        }

        .status-badge {
            order: 1;
            padding: 5px 12px;
            font-size: 11px;
        }
    }

    @media (max-width: 480px) {
        .planning-header {
            padding: 16px;
            gap: 12px;
        }

        .header-left h1 {
            font-size: 18px;
        }

        .header-left p {
            font-size: 11px;
        }

        .header-right {
            flex-direction: column;
            width: 100%;
            gap: 10px;
        }

        .export-btn {
            width: 100%;
            justify-content: center;
            padding: 10px 16px;
            font-size: 12px;
        }

        .date-selector {
            width: 100%;
            padding: 10px 12px;
            font-size: 12px;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 20px;
            padding: 0 12px;
            margin-top: 15px;
        }

        .stat-value {
            font-size: 20px;
        }

        .stat-label {
            font-size: 10px;
        }

        .stat-subtitle {
            font-size: 9px;
        }

        .filter-tabs {
            gap: 4px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding: 0 12px;
        }

        .filter-tab {
            padding: 8px 12px;
            font-size: 11px;
            min-width: max-content;
        }

        .reservations-timeline {
            padding: 0 12px 12px 12px;
        }

        .card-details {
            grid-template-columns: 1fr;
            gap: 6px;
            font-size: 11px;
        }

        .card-detail-item {
            gap: 4px;
        }

        .action-btn {
            padding: 6px 10px;
            font-size: 10px;
            flex: 1;
        }

        .action-buttons {
            width: 100%;
        }
    }
</style>

<div class="planning-page">
    <!-- Header Section -->
    <div class="planning-header">
        <div class="header-left">
            <h1><i class="fas fa-tasks"></i> Bonjour, Super Admin</h1>
            <p id="dateDisplay">Sunday 19 April 2026 - Administrateur</p>
        </div>
        <div class="header-right">
            <a href="<?php echo e(route('planification.history')); ?>" class="export-btn">
                <i class="fas fa-history"></i> Historique
            </a>
            <a href="<?php echo e(route('planification.export.excel')); ?>" class="export-btn">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <input type="date" id="datePickerInput" class="date-selector" title="Sélectionner une date">
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <?php
            $totalReservations = isset($reservations) ? count($reservations) : 0;
            $pendingReservations = isset($reservations) ? collect($reservations)->where('status', 'pending')->count() : 0;
            $approvedReservations = isset($reservations) ? collect($reservations)->where('status', 'approved')->count() : 0;
            $rejectedReservations = isset($reservations) ? collect($reservations)->where('status', 'rejected')->count() : 0;
            $cancelledReservations = isset($reservations) ? collect($reservations)->where('status', 'cancelled')->count() : 0;
            $availableCars = isset($cars) ? count($cars) : 0;
        ?>

        <a class="stat-card" href="<?php echo e(route('admin.data.reservations')); ?>" aria-label="Ouvrir la liste des demandes (réservations)">
            <div class="stat-value" id="totalCount" data-target="<?php echo e($totalReservations); ?>">0</div>
            <div class="stat-label"><i class="fas fa-list"></i> Total Demandes</div>
            <div class="stat-subtitle">total</div>
        </a>
        <a class="stat-card orange" href="<?php echo e(route('admin.data.requests')); ?>" aria-label="Ouvrir les demandes en attente">
            <div class="stat-value" id="pendingCount" data-target="<?php echo e($pendingReservations); ?>">0</div>
            <div class="stat-label"><i class="fas fa-hourglass-half"></i> En Attente</div>
            <div class="stat-subtitle">à valider</div>
        </a>
        <a class="stat-card green" href="<?php echo e(route('admin.data.reservations', ['status' => 'approved'])); ?>" aria-label="Ouvrir les demandes approuvées">
            <div class="stat-value" id="approvedCount" data-target="<?php echo e($approvedReservations); ?>">0</div>
            <div class="stat-label"><i class="fas fa-check-circle"></i> Approuvées</div>
            <div class="stat-subtitle">validées</div>
        </a>
        <a class="stat-card red" href="<?php echo e(route('admin.data.reservations', ['status' => 'rejected'])); ?>" aria-label="Ouvrir les demandes rejetées">
            <div class="stat-value" id="rejectedCount" data-target="<?php echo e($rejectedReservations); ?>">0</div>
            <div class="stat-label"><i class="fas fa-times-circle"></i> Rejetées</div>
            <div class="stat-subtitle">non approuvées</div>
        </a>

        <a class="stat-card gray" href="<?php echo e(route('admin.data.reservations', ['status' => 'cancelled'])); ?>" aria-label="Ouvrir les demandes annulées">
            <div class="stat-value" id="cancelledCount" data-target="<?php echo e($cancelledReservations); ?>">0</div>
            <div class="stat-label"><i class="fas fa-times-circle"></i> Annulées</div>
            <div class="stat-subtitle">annulées</div>
        </a>
        <a class="stat-card gray" href="<?php echo e(route('cars.index')); ?>" aria-label="Ouvrir les véhicules disponibles">
            <div class="stat-value" id="vehicleCount" data-target="<?php echo e($availableCars); ?>">0</div>
            <div class="stat-label"><i class="fas fa-car"></i> Véhicules Dispo</div>
            <div class="stat-subtitle">disponibles</div>
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <button class="filter-tab active" data-status="all">
            Toutes <span class="filter-badge" id="count-all">8</span>
        </button>
        <button class="filter-tab" data-status="pending">
            En attente <span class="filter-badge" id="count-pending">1</span>
        </button>
        <button class="filter-tab" data-status="approved">
            Approuvées <span class="filter-badge" id="count-approved">5</span>
        </button>
        <button class="filter-tab" data-status="rejected">
            Rejetées <span class="filter-badge" id="count-rejected">1</span>
        </button>
        <button class="filter-tab" data-status="cancelled">
            Annulées <span class="filter-badge" id="count-cancelled">1</span>
        </button>
    </div>

    <!-- Reservations Timeline -->
    <div class="reservations-timeline" id="reservationsTimeline">
        <!-- Cards will be rendered here -->
    </div>
</div>

<script>
    // Reservations data - Loaded from server
    const reservations = <?php echo json_encode($reservations ?? [], 15, 512) ?>;

    function getInitial(name) {
        return name.charAt(0).toUpperCase();
    }

    function renderReservationCard(reservation) {
        const card = document.createElement('div');
        card.className = 'reservation-card ' + reservation.status;
        card.dataset.reservationId = reservation.id;

        card.innerHTML = '<div class="card-avatar-section"><div class="user-avatar">' + getInitial(reservation.name) + '</div></div>' +
            '<div class="card-content-section">' +
            '<div class="card-header"><div><div class="user-name">' + reservation.name + '</div>' +
            '<div class="user-email">' + reservation.email + '</div></div></div>' +
            '<div class="card-title"><i class="fas fa-map-marker-alt"></i> ' + reservation.destination + '</div>' +
            '<div class="card-details">' +
            '<div class="card-detail-item"><i class="fas fa-calendar"></i> ' + reservation.date + '</div>' +
            '<div class="card-detail-item"><i class="fas fa-clock"></i> ' + reservation.time + '</div>' +
            '<div class="card-detail-item"><i class="fas fa-road"></i> ' + reservation.km + '</div>' +
            '<div class="card-vehicle"><i class="fas fa-car"></i> ' + reservation.vehicle + '</div></div></div>' +
            '<div class="card-actions-section"><span class="status-badge ' + reservation.status + '">' +
            (reservation.status === 'approved' ? '✓ Approuvé' : 
             reservation.status === 'pending' ? '⏳ En attente' :
             reservation.status === 'rejected' ? '✗ Rejeté' : '⭘ Annulé') +
            '</span><div class="action-buttons">' +
            (reservation.status === 'pending' ? 
                '<button class="action-btn approve" data-action="approve"><i class="fas fa-check"></i> Approuver</button>' +
                '<button class="action-btn reject" data-action="reject"><i class="fas fa-ban"></i> Rejeter</button>' :
                '<button class="action-btn cancel" data-action="cancel"><i class="fas fa-times"></i> Annuler</button>') +
            '</div></div>';

        // Add event listeners for action buttons
        card.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.dataset.action;
                handleReservationAction(reservation.id, action, this);
            });
        });

        return card;
    }

    // Get CSRF token - with better error checking
    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!token) {
            console.error('CSRF token not found! Make sure meta[name="csrf-token"] exists in layout.');
            showToast('error', 'Erreur de sécurité: Token CSRF manquant');
            return null;
        }
        return token;
    }

    const csrfToken = getCsrfToken();

    async function handleReservationAction(reservationId, action, button) {
        const reservation = reservations.find(r => r.id === reservationId);
        if (!reservation) {
            console.error('Reservation not found:', reservationId);
            showToast('error', 'Demande introuvable');
            return;
        }

        // Verify CSRF token exists before proceeding
        if (!csrfToken) {
            console.error('No CSRF token available');
            showToast('error', 'Erreur de sécurité: Token CSRF manquant');
            return;
        }

        let confirmMsg = '';
        let endpoint = '';
        let method = 'POST';
        let body = {};

        if (action === 'approve') {
            confirmMsg = 'Approuver la demande de ' + reservation.name + ' pour ' + reservation.destination + ' ?';
            endpoint = `/admin/reservations/${reservationId}/approve`;
            body = {};
        } else if (action === 'reject') {
            confirmMsg = 'Rejeter la demande de ' + reservation.name + ' ?';
            endpoint = `/admin/reservations/${reservationId}/status`;
            body = { status: 'rejected' };
        } else if (action === 'cancel') {
            confirmMsg = 'Annuler la demande de ' + reservation.name + ' ?';
            endpoint = `/admin/reservations/${reservationId}/cancel`;
            body = {};
        }

        if (!confirm(confirmMsg)) {
            console.log('Action cancelled by user');
            return;
        }

        button.disabled = true;
        button.style.opacity = '0.6';
        
        console.log(`Sending ${action} request to ${endpoint}`, {
            method,
            body,
            csrfToken: csrfToken ? 'present' : 'MISSING!'
        });

        try {
            const response = await fetch(endpoint, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(body)
            });

            console.log(`Response status: ${response.status}`, {
                ok: response.ok,
                statusText: response.statusText
            });

            // Try to parse JSON
            let data = {};
            try {
                data = await response.json();
                console.log('Response data:', data);
            } catch (parseError) {
                console.error('Failed to parse response as JSON:', parseError);
                console.log('Response text:', await response.text());
                showToast('error', 'Erreur: Réponse invalide du serveur');
                button.disabled = false;
                button.style.opacity = '1';
                return;
            }

            if (response.ok && (data.success || !data.success === false)) {
                console.log('Success! Updating local state...');
                
                // Update status locally
                if (action === 'approve') {
                    reservation.status = 'approved';
                } else if (action === 'reject') {
                    reservation.status = 'rejected';
                } else if (action === 'cancel') {
                    reservation.status = 'cancelled';
                }

                // Update UI
                const card = button.closest('.reservation-card');
                if (card) {
                    card.className = 'reservation-card ' + reservation.status;

                    // Update action buttons
                    const actionsDiv = card.querySelector('.action-buttons');
                    if (action === 'approve') {
                        actionsDiv.innerHTML = '<button class="action-btn cancel" data-action="cancel"><i class="fas fa-times"></i> Annuler</button>';
                        actionsDiv.querySelector('.cancel').addEventListener('click', function() {
                            handleReservationAction(reservationId, 'cancel', this);
                        });
                    } else if (action === 'reject' || action === 'cancel') {
                        actionsDiv.innerHTML = '';
                    }

                    // Update status badge
                    const badge = card.querySelector('.status-badge');
                    badge.className = 'status-badge ' + reservation.status;
                    badge.innerHTML = reservation.status === 'approved' ? '✓ Approuvé' : 
                                       reservation.status === 'pending' ? '⏳ En attente' :
                                       reservation.status === 'rejected' ? '✗ Rejeté' : '⭘ Annulé';
                }

                // Update counts
                updateCounts();
                updateStatsAndDate();

                // Show success message
                showToast('success', data.message || ('✓ Demande ' + (action === 'approve' ? 'approuvée' : action === 'reject' ? 'rejetée' : 'annulée') + ' avec succès!'));
            } else {
                console.error('Server returned error:', data);
                showToast('error', data.message || 'Une erreur est survenue.');
                button.disabled = false;
                button.style.opacity = '1';
            }
        } catch (error) {
            console.error('Network or fetch error:', error);
            console.error('Error stack:', error.stack);
            showToast('error', 'Erreur réseau: ' + (error.message || 'Veuillez réessayer.'));
            button.disabled = false;
            button.style.opacity = '1';
        }
    }

    function animateValue(el, target, duration = 600) {
        const start = parseInt(el.textContent || '0', 10) || 0;
        const end = parseInt(target || 0, 10) || 0;
        if (start === end) { el.textContent = end; return; }
        const range = end - start;
        const stepTime = 16;
        const steps = Math.max(1, Math.floor(duration / stepTime));
        const increment = Math.ceil(Math.abs(range) / steps) * (range > 0 ? 1 : -1);
        let current = start;
        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                el.textContent = end;
                clearInterval(timer);
            } else {
                el.textContent = current;
            }
        }, stepTime);
    }

    function updateCounts() {
        const counts = {
            all: reservations.length,
            pending: 0,
            approved: 0,
            rejected: 0,
            cancelled: 0
        };

        for (let r of reservations) {
            if (r.status === 'pending') counts.pending++;
            if (r.status === 'approved') counts.approved++;
            if (r.status === 'rejected') counts.rejected++;
            if (r.status === 'cancelled') counts.cancelled++;
        }

        // Animate badges
        document.getElementById('count-all').textContent = counts.all;
        document.getElementById('count-pending').textContent = counts.pending;
        document.getElementById('count-approved').textContent = counts.approved;
        document.getElementById('count-rejected').textContent = counts.rejected;
        document.getElementById('count-cancelled').textContent = counts.cancelled;

        // Animate stat cards (smooth)
        const totalEl = document.getElementById('totalCount');
        const pendingEl = document.getElementById('pendingCount');
        const approvedEl = document.getElementById('approvedCount');
        const rejectedEl = document.getElementById('rejectedCount');
        const cancelledEl = document.getElementById('cancelledCount');
        if (totalEl) animateValue(totalEl, counts.all);
        if (pendingEl) animateValue(pendingEl, counts.pending);
        if (approvedEl) animateValue(approvedEl, counts.approved);
        if (rejectedEl) animateValue(rejectedEl, counts.rejected);
        if (cancelledEl) animateValue(cancelledEl, counts.cancelled);
    }

    function filterReservations(status) {
        const timeline = document.getElementById('reservationsTimeline');
        timeline.innerHTML = '';

        const filtered = status === 'all' ? reservations : reservations.filter(r => r.status === status);

        if (filtered.length === 0) {
            timeline.innerHTML = '<div class="empty-state"><div class="empty-state-icon"><i class="fas fa-inbox"></i></div><h3>Aucune demande</h3><p>Aucune demande ne correspond à ce filtre</p></div>';
            return;
        }

        for (let reservation of filtered) {
            timeline.appendChild(renderReservationCard(reservation));
        }
    }

    // Initialize
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            filterReservations(this.dataset.status);
        });
    });

    // Update stats and date
    function updateStatsAndDate() {
        // Update stats counts
        const total = reservations.length;
        const pending = reservations.filter(r => r.status === 'pending').length;
        const approved = reservations.filter(r => r.status === 'approved').length;
        const rejected = reservations.filter(r => r.status === 'rejected').length;

        document.getElementById('totalCount').textContent = total;
        document.getElementById('pendingCount').textContent = pending;
        document.getElementById('approvedCount').textContent = approved;
        document.getElementById('rejectedCount').textContent = rejected;

        // Update date display
        const today = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const dateStr = today.toLocaleDateString('en-US', options);
        const dayMonth = dateStr.charAt(0).toUpperCase() + dateStr.slice(1);
        document.getElementById('dateDisplay').textContent = dayMonth + ' - Administrateur';
    }

    // Get today's date in YYYY-MM-DD format
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    const todayString = `${year}-${month}-${day}`;
    let selectedDate = todayString;

    // Set the date input to today
    const dateInput = document.getElementById('datePickerInput');
    dateInput.value = todayString;
    dateInput.max = todayString; // Prevent selecting future dates

    // Initial render
    updateCounts();
    updateStatsAndDate();
    filterReservations('all');

    // Animate available vehicles counter (one-time)
    const vehicleEl = document.getElementById('vehicleCount');
    if (vehicleEl) {
        const target = vehicleEl.dataset.target ?? vehicleEl.getAttribute('data-target');
        if (typeof target !== 'undefined') animateValue(vehicleEl, target);
    }

    // Date picker event listener for daily filtering
    dateInput.addEventListener('change', function() {
        selectedDate = this.value; // Format: YYYY-MM-DD from input
        
        // Update date display with selected date
        const [selectedYear, selectedMonth, selectedDay] = selectedDate.split('-');
        const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                           'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        const monthName = monthNames[parseInt(selectedMonth) - 1];
        const date = new Date(selectedDate + 'T00:00:00'); // Ensure UTC parsing
        const dayOfWeek = date.toLocaleDateString('fr-FR', { weekday: 'long' });
        const formattedDate = `${dayOfWeek.charAt(0).toUpperCase() + dayOfWeek.slice(1)} ${parseInt(selectedDay)} ${monthName} ${selectedYear}`;
        document.getElementById('dateDisplay').textContent = formattedDate + ' - Administrateur';
        
        // Filter reservations by selected date (both use YYYY-MM-DD format now)
        const filteredReservations = reservations.filter(r => {
            // Both r.date and selectedDate are in YYYY-MM-DD format
            return r.date === selectedDate;
        });
        const timeline = document.getElementById('reservationsTimeline');
        timeline.innerHTML = '';
        
        if (filteredReservations.length === 0) {
            timeline.innerHTML = '<div style="text-align: center; padding: 40px; color: #999; background: white; border-radius: 10px; margin-top: 20px;"><i class="fas fa-inbox" style="font-size: 36px; margin-bottom: 12px; display: block; opacity: 0.5;"></i><p>Aucune demande pour cette date</p></div>';
        } else {
            filteredReservations.forEach(reservation => {
                timeline.appendChild(renderReservationCard(reservation));
            });
        }
    });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\PC\Desktop\projet-sdcc\Reservation-Vehicule-Service\vehicule-sdcc\resources\views/planification/index.blade.php ENDPATH**/ ?>