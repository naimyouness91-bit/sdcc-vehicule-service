{{-- Véhicules - Admin View (included by cars/index.blade.php) --}}
<style>
    :root {
        --primary: #4CAF50;
        --primary-dark: #2E7D32;
        --primary-light: #66BB6A;
        --primary-pale: #E8F5E9;
        --green-700: #2E7D32;
        --green-600: #388E3C;
        --green-500: #4CAF50;
        --green-400: #66BB6A;
        --green-300: #81C784;
        --green-200: #C8E6C9;
        --green-100: #E8F5E9;
        --green-50: #F1F8E9;
        --text-dark: #1a2e1a;
        --text-light: #6b7280;
        --bg-light: #f8fafc;
    }

    * { box-sizing: border-box; }

    .content-wrapper {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
        background: linear-gradient(135deg, #F1F8E9 0%, #f8fafc 100%);
        min-height: 100vh;
    }

    /* HEADER */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        gap: 30px;
        background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 25%, #FFA726 75%, #FFA500 100%);
        padding: 30px;
        border-radius: 16px;
        color: white;
        box-shadow: 0 8px 24px rgba(76, 175, 80, 0.25);
    }

    .page-header-info h1 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .page-header-info p {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.95);
        font-weight: 500;
    }

    .add-vehicle-btn {
        background: rgba(255, 255, 255, 0.25);
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.6);
        padding: 12px 28px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
    }

    .add-vehicle-btn:hover {
        background: rgba(255, 255, 255, 0.35);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        border-color: rgba(255, 255, 255, 0.8);
    }

    /* STATS */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        border-left: 5px solid var(--primary);
        box-shadow: 0 2px 12px rgba(76, 175, 80, 0.08);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(76, 175, 80, 0.15);
        border-left-color: #FFA726;
    }

    .stat-card-clickable {
        cursor: pointer;
        position: relative;
    }

    .stat-card-clickable:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 12px 32px rgba(76, 175, 80, 0.25);
        border-left-color: #FFA726;
    }

    .stat-card-clickable:active {
        transform: translateY(-4px) scale(0.98);
    }

    .stat-label {
        font-size: 12px;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--green-700);
    }

    /* FILTERS */
    .filters-section {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        display: flex;
        gap: 12px;
        align-items: flex-end;
        flex-wrap: wrap;
        box-shadow: 0 2px 12px rgba(76, 175, 80, 0.08);
    }

    .search-box {
        flex: 1;
        position: relative;
        min-width: 250px;
    }

    .search-box input {
        width: 100%;
        padding: 12px 15px 12px 40px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px;
        transition: all 0.3s;
        background: var(--green-50);
    }

    .search-box input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.15);
        background: white;
    }

    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary);
    }

    .filter-btn {
        padding: 12px 18px;
        border: 2px solid #e5e7eb;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
        color: var(--text-light);
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: linear-gradient(135deg, var(--primary) 0%, #FFA726 100%);
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2);
    }

    /* TABLE */
    .table-view {
        background: white;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 2px 12px rgba(76, 175, 80, 0.08);
        overflow-x: auto;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 2px solid var(--green-100);
        padding-bottom: 15px;
    }

    .section-title {
        font-size: 16px;
        font-weight: 700;
        color: #FFA726;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead { background: var(--green-50); }

    th {
        text-align: left;
        padding: 14px;
        font-size: 12px;
        font-weight: 700;
        color: var(--green-700);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--green-100);
    }

    td {
        padding: 16px 14px;
        border-bottom: 1px solid var(--green-50);
        font-size: 13px;
    }

    tbody tr:hover { background: var(--green-50); }

    .vehicle-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .vehicle-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
    }

    .vehicle-info {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .vehicle-name {
        font-weight: 600;
        color: var(--green-700);
    }

    .vehicle-subtitle {
        font-size: 11px;
        color: #999;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-available {
        background: var(--green-100);
        color: var(--green-700);
    }

    .status-maintenance {
        background: var(--green-200);
        color: var(--green-700);
    }

    .availability-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .availability-weekend {
        background: var(--green-100);
        color: var(--green-700);
        border-left: 3px solid var(--primary);
    }

    .availability-both {
        background: var(--green-100);
        color: var(--green-700);
        border-left: 3px solid var(--primary);
    }

    .availability-weekday {
        background: var(--green-100);
        color: var(--green-700);
        border-left: 3px solid var(--primary);
    }

    .availability-unavailable {
        background: var(--green-200);
        color: var(--green-700);
        border-left: 3px solid var(--primary-dark);
    }

    .action-menu {
        position: relative;
    }

    .action-trigger {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--primary);
        font-size: 18px;
        padding: 5px;
        transition: color 0.3s ease;
    }

    .action-trigger:hover {
        color: var(--primary-dark);
    }

    .action-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border: 2px solid var(--green-100);
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(76, 175, 80, 0.15);
        z-index: 100;
        min-width: 180px;
        margin-top: 8px;
    }

    .action-dropdown.show {
        display: block;
    }

    .action-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        cursor: pointer;
        transition: background 0.2s ease;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        font-size: 13px;
        color: var(--text-dark);
    }

    .action-item:hover {
        background: var(--green-50);
    }

    .action-item.danger:hover {
        background: var(--green-100);
        color: var(--primary-dark);
    }

    .action-item i {
        width: 16px;
        text-align: center;
    }

    /* INLINE ACTION BUTTONS */
    .action-buttons {
        display: flex;
        gap: 8px;
        align-items: center;
        justify-content: flex-start;
    }

    .action-btn {
        background: none;
        border: 2px solid var(--green-300);
        padding: 6px 12px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
    }

    .action-btn i {
        font-size: 14px;
    }

    .action-btn.edit {
        color: var(--primary);
        border-color: var(--primary);
        background: rgba(16, 185, 129, 0.05);
    }

    .action-btn.edit:hover {
        background: rgba(76, 175, 80, 0.15);
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(76, 175, 80, 0.2);
        color: var(--primary-dark);
        border-color: var(--primary-dark);
    }

    .action-btn.delete {
        color: var(--primary-dark);
        border-color: var(--primary-dark);
        background: rgba(16, 185, 129, 0.08);
    }

    .action-btn.delete:hover {
        background: rgba(76, 175, 80, 0.2);
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(76, 175, 80, 0.25);
        color: var(--green-700);
        border-color: var(--green-700);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.3;
        color: var(--primary);
    }

    .empty-state p {
        color: var(--text-light);
    }

    .hidden-row {
        display: none;
    }

    /* MODALS */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-overlay.show {
        display: flex;
        animation: fadeIn 0.2s ease;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        box-shadow: 0 12px 40px rgba(76, 175, 80, 0.2);
        max-width: 500px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideUp 0.3s ease;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24px;
        border-bottom: 2px solid var(--green-100);
    }

    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: #f97316;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: var(--text-light);
        transition: color 0.3s ease;
    }

    .modal-close:hover {
        color: var(--primary);
    }

    .modal-body {
        padding: 24px;
    }

    .form-group {
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: var(--green-700);
    }

    .form-group input,
    .form-group select {
        padding: 11px 13px;
        border: 2px solid var(--green-200);
        border-radius: 8px;
        font-size: 13px;
        font-family: inherit;
        transition: all 0.3s ease;
        background: var(--green-50);
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        background: white;
    }

    .availability-info {
        background: var(--green-50);
        border-left: 4px solid var(--primary);
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 12px;
        color: var(--green-700);
        line-height: 1.5;
    }

    .availability-info strong {
        color: var(--green-700);
        display: block;
        margin-bottom: 6px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .modal-footer {
        padding: 20px 24px;
        border-top: 2px solid var(--green-100);
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn {
        padding: 11px 22px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, #FFA726 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(76, 175, 80, 0.3);
    }

    .btn-secondary {
        background: var(--green-50);
        color: var(--green-700);
        border: 2px solid var(--green-200);
    }

    .btn-secondary:hover {
        background: var(--green-100);
        border-color: var(--primary);
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* TOASTS */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 2000;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .toast {
        background: white;
        padding: 16px 20px;
        border-radius: 10px;
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.2);
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 300px;
        animation: slideInRight 0.3s ease;
    }

    .toast.success { 
        border-left: 4px solid var(--primary);
        background: var(--green-50);
    }
    
    .toast.error { 
        border-left: 4px solid var(--primary-dark);
        background: var(--green-100);
    }
    
    .toast i { 
        font-size: 18px; 
        flex-shrink: 0; 
        color: var(--primary);
    }
    
    .toast.error i { 
        color: var(--primary-dark);
    }

    .loading {
        position: relative;
        color: transparent;
    }

    .loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid rgba(76, 175, 80, 0.3);
        border-radius: 50%;
        border-top-color: var(--primary);
        animation: spin 0.6s linear infinite;
    }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideInRight { from { opacity: 0; transform: translateX(100px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes spin { to { transform: rotate(360deg); } }

    @media (max-width: 1024px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .add-vehicle-btn {
            width: 100%;
            justify-content: center;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .filters-section {
            flex-direction: column;
        }

        .filter-btn {
            width: 100%;
        }

        table {
            font-size: 13px;
        }

        th, td {
            padding: 12px;
        }
    }

    @media (max-width: 768px) {
        .content-wrapper {
            padding: 12px;
        }

        .page-header {
            padding: 18px;
            flex-direction: column;
            gap: 12px;
        }

        .page-header-info h1 {
            font-size: 22px;
        }

        .page-header-info p {
            font-size: 12px;
        }

        .add-vehicle-btn {
            width: 100%;
            padding: 12px 16px;
            font-size: 13px;
            min-height: 40px;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .stat-card {
            padding: 14px;
        }

        .stat-value {
            font-size: 22px;
        }

        .stat-label {
            font-size: 12px;
        }

        .filters-section {
            flex-direction: column;
            gap: 10px;
        }

        .filter-btn {
            width: 100%;
            padding: 10px 14px;
            font-size: 12px;
            min-height: 40px;
        }

        .table-view {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 8px;
        }

        table {
            font-size: 12px;
        }

        th, td {
            padding: 10px;
            min-width: 80px;
        }

        thead {
            position: sticky;
            top: 0;
            z-index: 10;
        }
    }

    @media (max-width: 640px) {
        .page-header {
            padding: 16px;
        }

        .page-header-info h1 {
            font-size: 20px;
        }

        .add-vehicle-btn {
            padding: 10px 14px;
            font-size: 12px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .stat-card {
            padding: 12px;
            flex-direction: column;
        }

        .stat-card > div:last-child {
            font-size: 18px;
        }

        .stat-value {
            font-size: 20px;
        }

        .filters-section {
            gap: 8px;
        }

        .filter-btn {
            font-size: 11px;
            padding: 8px 12px;
        }

        .table-responsive {
            min-width: 100%;
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            min-width: 600px;
            font-size: 11px;
        }

        th, td {
            padding: 8px;
            min-width: 70px;
        }

        .action-btn {
            padding: 6px 10px;
            font-size: 10px;
        }
    }

    @media (max-width: 480px) {
        .content-wrapper {
            padding: 10px;
        }

        .page-header {
            padding: 14px;
            gap: 10px;
        }

        .page-header-info h1 {
            font-size: 18px;
            margin-bottom: 4px;
        }

        .page-header-info p {
            font-size: 11px;
        }

        .add-vehicle-btn {
            width: 100%;
            padding: 10px 12px;
            font-size: 11px;
            min-height: 40px;
            gap: 6px;
        }

        .add-vehicle-btn i {
            font-size: 14px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
            gap: 8px;
            margin-bottom: 15px;
        }

        .stat-card {
            padding: 10px;
            gap: 8px;
        }

        .stat-value {
            font-size: 18px;
        }

        .stat-label {
            font-size: 11px;
        }

        .stat-card > div:last-child {
            font-size: 16px;
        }

        .filters-section {
            gap: 6px;
            margin-bottom: 12px;
        }

        .filter-btn {
            font-size: 11px;
            padding: 8px 10px;
            width: 100%;
            min-height: 38px;
        }

        /* Scrollable table wrapper */
        .table-responsive {
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 12px;
        }

        table {
            min-width: 500px;
            font-size: 10px;
            border-collapse: collapse;
        }

        th {
            padding: 6px;
            min-width: 60px;
            font-size: 10px;
            font-weight: 600;
        }

        td {
            padding: 6px;
            min-width: 60px;
        }

        tbody tr {
            border-bottom: 1px solid #e8e8e8;
        }

        .action-btn {
            padding: 5px 8px;
            font-size: 9px;
            min-width: auto;
            min-height: 32px;
            border-radius: 4px;
        }

        .action-btn i {
            font-size: 10px;
        }

        .modal-content {
            width: calc(100vw - 20px);
            max-height: 80vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .modal-header {
            padding: 14px 16px;
        }

        .modal-header h2 {
            font-size: 16px;
        }

        .modal-body {
            padding: 14px 16px;
        }

        .modal-body label {
            font-size: 12px;
        }

        .modal-body input,
        .modal-body select,
        .modal-body textarea {
            font-size: 14px;
            padding: 10px 12px;
            min-height: 40px;
        }

        .modal-footer {
            padding: 12px 16px;
            gap: 8px;
            flex-direction: column;
        }

        .modal-footer button {
            width: 100%;
            padding: 10px;
            font-size: 13px;
            min-height: 40px;
        }
    }
</style>

<div class="content-wrapper">
    <div class="page-header">
        <div class="page-header-info">
            <h1><i class="fas fa-garage" style="margin-right: 12px;"></i>Gestion des Véhicules</h1>
            <p><i class="fas fa-sitemap" style="margin-right: 8px; font-size: 12px;"></i>Flotte de véhicules SDCC</p>
        </div>
        <button class="add-vehicle-btn" id="addVehicleBtn">
            <i class="fas fa-plus-circle"></i> Ajouter un véhicule
        </button>
    </div>

    <div class="stats-grid">
        <div class="stat-card stat-card-clickable" data-filter="all" title="Afficher toute la flotte">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div class="stat-label"><i class="fas fa-gauge" style="margin-right: 6px;"></i>Flotte Totale</div>
                    <div class="stat-value" id="totalCount">{{ $cars->count() }}</div>
                </div>
                <i class="fas fa-car" style="font-size: 28px; color: var(--primary); opacity: 0.15;"></i>
            </div>
        </div>
        <div class="stat-card stat-card-clickable" data-filter="disponible" title="Afficher les véhicules disponibles">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div class="stat-label"><i class="fas fa-check-circle" style="margin-right: 6px;"></i>Disponibles</div>
                    <div class="stat-value" id="availCount">{{ $cars->where('status', 'disponible')->count() }}</div>
                </div>
                <i class="fas fa-car-circle-check" style="font-size: 28px; color: var(--primary); opacity: 0.15;"></i>
            </div>
        </div>
        <div class="stat-card stat-card-clickable" data-filter="maintenance" title="Afficher les véhicules en maintenance">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div class="stat-label"><i class="fas fa-tools" style="margin-right: 6px;"></i>Maintenance</div>
                    <div class="stat-value" id="maintCount">{{ $cars->where('status', 'maintenance')->count() }}</div>
                </div>
                <i class="fas fa-wrench" style="font-size: 28px; color: #fbbf24; opacity: 0.15;"></i>
            </div>
        </div>
        <div class="stat-card stat-card-clickable" data-filter="all" title="Afficher toute la flotte">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div class="stat-label"><i class="fas fa-road" style="margin-right: 6px;"></i>KM Total</div>
                    <div class="stat-value" id="kmCount">{{ number_format($cars->sum('km'), 0, ',', ' ') }}</div>
                </div>
                <i class="fas fa-tachometer-alt" style="font-size: 28px; color: #f97316; opacity: 0.15;"></i>
            </div>
        </div>
    </div>

    <div class="filters-section">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Marque, modèle, matricule...">
        </div>
        <button class="filter-btn active" data-filter="all"><i class="fas fa-list" style="margin-right: 6px;"></i>Tous</button>
        <button class="filter-btn" data-filter="disponible"><i class="fas fa-check-circle" style="margin-right: 6px;"></i>Disponible</button>
        <button class="filter-btn" data-filter="maintenance"><i class="fas fa-tools" style="margin-right: 6px;"></i>Maintenance</button>
    </div>

    <div class="table-view">
        <div class="section-header">
            <h3 class="section-title"><i class="fas fa-list-check" style="margin-right: 10px;"></i>Véhicules</h3>
            <span id="vehicleCount" style="font-size: 13px; color: #999;"><i class="fas fa-circle-info" style="margin-right: 4px;"></i>{{ $cars->count() }} véhicules</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Marque</th>
                    <th>Modèle</th>
                    <th>Disponibilité</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="vehiclesTableBody">
                @forelse($cars as $car)
                <tr data-id="{{ $car->id }}" data-status="{{ $car->status }}" data-availability="{{ $car->availability_type ?? 'both' }}" data-search="{{ strtolower($car->name.' '.$car->model.' '.$car->matricule) }}">
                    <td>
                        <span style="font-weight: 600; color: #666;">{{ $car->id }}</span>
                    </td>
                    <td>
                        <div class="vehicle-cell">
                            <div class="vehicle-avatar">{{ strtoupper(substr($car->name, 0, 1)) }}</div>
                            <div class="vehicle-info">
                                <div class="vehicle-name">{{ $car->name }}</div>
                                <div class="vehicle-subtitle">{{ $car->matricule }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="color: #555;">{{ $car->model }}</span>
                    </td>
                    <td>
                        @php
                            $availabilityClass = match($car->availability_type ?? 'both') {
                                'weekend' => 'availability-weekend',
                                'unavailable' => 'availability-unavailable',
                                'both' => 'availability-both',
                                default => 'availability-both'
                            };
                        @endphp
                        <span class="availability-badge {{ $availabilityClass }}">
                            @switch($car->availability_type ?? 'both')
                                @case('weekend')
                                    <i class="fas fa-sun" style="margin-right: 6px;"></i>Week-end
                                    @break
                                @case('unavailable')
                                    <i class="fas fa-ban" style="margin-right: 6px;"></i>Indisponible
                                    @break
                                @default
                                    <i class="fas fa-repeat" style="margin-right: 6px;"></i>Toute la semaine
                            @endswitch
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn edit edit-action" 
                                data-id="{{ $car->id }}" 
                                data-name="{{ $car->name }}"
                                data-matricule="{{ $car->matricule }}"
                                data-model="{{ $car->model }}"
                                data-year="{{ $car->year }}"
                                data-km="{{ $car->km }}"
                                data-status="{{ $car->status }}"
                                data-availability="{{ $car->availability_type ?? 'both' }}" 
                                title="Modifier le véhicule">
                                <i class="fas fa-pen-to-square"></i> Modifier
                            </button>
                            <button class="action-btn delete delete-action" data-id="{{ $car->id }}" data-name="{{ $car->name }}" title="Supprimer le véhicule">
                                <i class="fas fa-trash-can"></i> Supprimer
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5"><div class="empty-state"><i class="fas fa-inbox"></i><p>Aucun véhicule dans la flotte</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ADD/EDIT MODAL -->
<div class="modal-overlay" id="vehicleModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="modalTitle">Ajouter un véhicule</h2>
            <button class="modal-close" id="closeModalBtn">&times;</button>
        </div>
        <form id="vehicleForm">
            @csrf
            <input type="hidden" name="_method" value="PUT" id="methodField">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Marque *</label>
                        <input type="text" name="name" required placeholder="ex: Toyota">
                    </div>
                    <div class="form-group">
                        <label>Matricule *</label>
                        <input type="text" name="matricule" required placeholder="ex: AB123CD">
                    </div>
                </div>
                <div class="form-group">
                    <label>Modèle *</label>
                    <input type="text" name="model" required placeholder="ex: Corolla">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Année *</label>
                        <input type="number" name="year" required min="1900" placeholder="{{ date('Y') }}">
                    </div>
                    <div class="form-group">
                        <label>KM *</label>
                        <input type="number" name="km" required min="0" placeholder="0">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Statut *</label>
                        <select name="status" required>
                            <option value="disponible"><i class="fas fa-check-circle"></i> Disponible</option>
                            <option value="maintenance"><i class="fas fa-tools"></i> Maintenance</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Disponibilité *</label>
                        <select name="availability_type" required id="availabilityTypeSelect">
                            <option value="both"><i class="fas fa-repeat"></i> Disponible toute la semaine</option>
                            <option value="weekend"><i class="fas fa-sun"></i> Week-end uniquement</option>
                            <option value="unavailable"><i class="fas fa-ban"></i> Indisponible</option>
                        </select>
                    </div>
                </div>
                <div class="availability-info">
                    <strong id="availabilityDescription">Disponible toute la semaine</strong>
                    <span id="availabilityHelper">Les clients peuvent réserver ce véhicule tous les jours de la semaine (lundi au dimanche).</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelBtn">Annuler</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- DETAILS MODAL -->
<div class="modal-overlay" id="detailsModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Détails du véhicule</h2>
            <button class="modal-close" id="closeDetailsBtn">&times;</button>
        </div>
        <div class="modal-body" id="detailsContent"></div>
    </div>
</div>

<!-- DELETE MODAL -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>Confirmer la suppression</h2>
            <button class="modal-close" id="closeDeleteBtn">&times;</button>
        </div>
        <div class="modal-body">
            <p style="color: #666; line-height: 1.6;">Êtes-vous sûr de vouloir supprimer <strong>ce véhicule</strong> ? Cette action ne peut pas être annulée.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="cancelDeleteBtn">Annuler</button>
            <button type="button" class="btn btn-primary" style="background: linear-gradient(135deg, var(--primary-dark) 0%, var(--green-700) 100%);" id="confirmDeleteBtn">Supprimer</button>
        </div>
    </div>
</div>

<!-- STATUS MODAL -->
<div class="modal-overlay" id="statusModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Changer le statut</h2>
            <button class="modal-close" id="closeStatusBtn">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Nouveau statut *</label>
                <select id="newStatus" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="disponible">✓ Disponible</option>
                    <option value="maintenance">⚙ Maintenance</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="cancelStatusBtn">Annuler</button>
            <button type="button" class="btn btn-primary" id="confirmStatusBtn">Changer</button>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
    const CSRF = '{{ csrf_token() }}';
    let currentEditId = null, currentDeleteId = null, currentStatusId = null;

    // Availability Type Handler
    availabilityDescriptions = {
        both: {
            title: 'Disponible toute la semaine',
            helper: 'Les clients peuvent réserver ce véhicule tous les jours de la semaine (lundi au dimanche).'
        },
        weekend: {
            title: 'Disponible le week-end seulement',
            helper: 'Les clients ne peuvent réserver ce véhicule que le samedi et dimanche. Les réservations des jours de semaine seront automatiquement rejetées.'
        },
        unavailable: {
            title: 'Véhicule indisponible',
            helper: 'Ce véhicule ne peut pas être réservé actuellement. Les clients ne le verront pas dans la liste des véhicules disponibles.'
        }
    };

    document.getElementById('availabilityTypeSelect').addEventListener('change', (e) => {
        const type = e.target.value;
        const desc = availabilityDescriptions[type] || availabilityDescriptions.both;

        const infoDiv = document.querySelector('.availability-info');

        if (type === 'unavailable') {
            // Render a Bootstrap-style alert for unavailable vehicles
            // Include the expected IDs so other scripts can safely reference them
            infoDiv.innerHTML = `
                <div class="alert alert-danger d-flex align-items-start" role="alert">
                    <i class="fas fa-exclamation-triangle me-2" style="font-size:18px; margin-top:3px;"></i>
                    <div>
                        <strong id="availabilityDescription">${desc.title}</strong>
                        <div id="availabilityHelper" style="margin-top:4px;">${desc.helper}</div>
                    </div>
                </div>`;
        } else {
            // Restore default compact view for other types
            infoDiv.innerHTML = `<strong id="availabilityDescription">${desc.title}</strong> <span id="availabilityHelper">${desc.helper}</span>`;
        }
    });

    function showToast(msg, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        const icons = { success: 'fas fa-check-circle', error: 'fas fa-exclamation-circle' };
        toast.innerHTML = `<i class="${icons[type]}"></i><span>${msg}</span>`;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    function openModal(id) { document.getElementById(id).classList.add('show'); }
    function closeModal(id) { document.getElementById(id).classList.remove('show'); }
    function closeAll() { document.querySelectorAll('.modal-overlay.show').forEach(m => m.classList.remove('show')); }

    // ADD BUTTON
    document.getElementById('addVehicleBtn').addEventListener('click', () => {
        currentEditId = null;
        currentVehicleData = null;
        document.getElementById('vehicleForm').reset();
        document.getElementById('methodField').value = 'POST'; // Clear method spoofing for new records
        document.getElementById('modalTitle').textContent = 'Ajouter un véhicule';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-plus"></i> Ajouter';
        // Set default availability description
        document.getElementById('availabilityTypeSelect').dispatchEvent(new Event('change'));
        openModal('vehicleModal');
    });

    // CLOSE
    document.getElementById('closeModalBtn').addEventListener('click', () => closeModal('vehicleModal'));
    document.getElementById('cancelBtn').addEventListener('click', () => closeModal('vehicleModal'));
    document.getElementById('closeDetailsBtn').addEventListener('click', () => closeModal('detailsModal'));
    document.getElementById('closeDeleteBtn').addEventListener('click', () => closeModal('deleteModal'));
    document.getElementById('cancelDeleteBtn').addEventListener('click', () => closeModal('deleteModal'));
    document.getElementById('closeStatusBtn').addEventListener('click', () => closeModal('statusModal'));
    document.getElementById('cancelStatusBtn').addEventListener('click', () => closeModal('statusModal'));

    // FORM SUBMIT
    document.getElementById('vehicleForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        btn.classList.add('loading');
        btn.disabled = true;

        // Read form data
        const formData = new FormData(e.target);
        
        // Ensure method field is set correctly for PUT requests
        if (currentEditId) {
            formData.set('_method', 'PUT');
        }
        
        // DEBUG: Log what we're sending
        console.log('[FORM] Sending Form Data:');
        console.log('  _token:', formData.get('_token') ? '[OK] Present' : '[FAIL] Missing');
        console.log('  _method:', formData.get('_method'));
        console.log('  name:', formData.get('name'));
        console.log('  matricule:', formData.get('matricule'));
        console.log('  model:', formData.get('model'));
        console.log('  year:', formData.get('year'));
        console.log('  km:', formData.get('km'));
        console.log('  status:', formData.get('status'));
        console.log('  availability_type:', formData.get('availability_type'));
        console.log('  currentEditId:', currentEditId);

        // Always use POST with method spoofing for Laravel
        const url = currentEditId ? `/cars/${currentEditId}` : '/cars';
        const method = 'POST';

        try {
            const res = await fetch(url, {
                method,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            let response;
            try {
                response = await res.json();
            } catch (parseErr) {
                response = { message: 'Erreur serveur' };
            }
            
            console.log('[RESPONSE] Server Response:', response);
            
            if (!res.ok) {
                const errorMsg = response.errors 
                    ? Object.values(response.errors).flat().join(', ') 
                    : (response.message || 'Erreur lors de la sauvegarde');
                throw new Error(errorMsg);
            }
            
            showToast(response.message || (currentEditId ? 'Véhicule modifié!' : 'Véhicule ajouté!'), 'success');
            setTimeout(() => location.reload(), 500);
        } catch (err) {
            console.error('[ERROR] Error:', err.message);
            showToast(err.message || 'Erreur lors de la sauvegarde', 'error');
            btn.classList.remove('loading');
            btn.disabled = false;
        }
    });

    // DELETE
    document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
        try {
            const res = await fetch(`/cars/${currentDeleteId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error('Erreur');
            showToast('Véhicule supprimé!');
            setTimeout(() => location.reload(), 500);
        } catch (err) {
            showToast(err.message, 'error');
        }
    });

    // STATUS
    document.getElementById('confirmStatusBtn').addEventListener('click', async () => {
        const status = document.getElementById('newStatus').value;
        if (!status) return;

        try {
            const res = await fetch(`/cars/${currentStatusId}`, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ status })
            });
            if (!res.ok) throw new Error('Erreur');
            showToast('Statut mis à jour!');
            setTimeout(() => location.reload(), 500);
        } catch (err) {
            showToast(err.message, 'error');
        }
    });

    // ACTION BUTTONS
    let currentVehicleData = null; // Store current edit data
    
    document.querySelectorAll('.edit-action').forEach(btn => {
        btn.addEventListener('click', () => {
            // Store all vehicle data from button attributes
            currentVehicleData = {
                id: btn.dataset.id,
                name: btn.dataset.name || '',
                matricule: btn.dataset.matricule || '',
                model: btn.dataset.model || '',
                year: btn.dataset.year || new Date().getFullYear(),
                km: btn.dataset.km || '0',
                status: btn.dataset.status || 'disponible',
                availability_type: btn.dataset.availability || 'both'
            };
            
            // Set all form fields with stored data
            document.querySelector('[name="name"]').value = currentVehicleData.name;
            document.querySelector('[name="matricule"]').value = currentVehicleData.matricule;
            document.querySelector('[name="model"]').value = currentVehicleData.model;
            document.querySelector('[name="year"]').value = currentVehicleData.year;
            document.querySelector('[name="km"]').value = currentVehicleData.km;
            document.querySelector('[name="status"]').value = currentVehicleData.status;
            
            const availabilitySelect = document.querySelector('[name="availability_type"]');
            availabilitySelect.value = currentVehicleData.availability_type;
            availabilitySelect.dispatchEvent(new Event('change'));
            
            currentEditId = btn.dataset.id;
            document.getElementById('modalTitle').textContent = 'Modifier le véhicule';
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Modifier';
            closeAll();
            openModal('vehicleModal');
        });
    });

    document.querySelectorAll('.delete-action').forEach(btn => {
        btn.addEventListener('click', () => {
            const carName = btn.dataset.name || 'ce véhicule';
            currentDeleteId = btn.dataset.id;
            document.querySelector('#deleteModal .modal-body p').innerHTML = `Êtes-vous sûr de vouloir supprimer <strong>${carName}</strong> ? Cette action ne peut pas être annulée.`;
            closeAll();
            openModal('deleteModal');
        });
    });

    // FILTER AND SEARCH
    let activeFilter = 'all';
    const applyFilters = () => {
        const query = document.getElementById('searchInput').value.toLowerCase();
        let count = 0;
        document.querySelectorAll('#vehiclesTableBody tr[data-id]').forEach(row => {
            const match = (activeFilter === 'all' || row.dataset.status === activeFilter) && (!query || row.dataset.search.includes(query));
            row.classList.toggle('hidden-row', !match);
            if (match) count++;
        });
        document.getElementById('vehicleCount').textContent = `${count} véhicule${count !== 1 ? 's' : ''}`;
    };

    // STAT CARDS - Make them clickable to trigger filters
    document.querySelectorAll('.stat-card-clickable').forEach(card => {
        card.addEventListener('click', () => {
            const filterValue = card.dataset.filter;
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            const targetButton = document.querySelector(`.filter-btn[data-filter="${filterValue}"]`);
            if (targetButton) {
                targetButton.classList.add('active');
            }
            activeFilter = filterValue;
            applyFilters();
        });
    });

    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            activeFilter = btn.dataset.filter;
            applyFilters();
        });
    });

    document.getElementById('searchInput').addEventListener('input', applyFilters);
</script>
