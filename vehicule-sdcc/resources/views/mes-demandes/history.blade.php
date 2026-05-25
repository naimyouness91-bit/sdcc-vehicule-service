@extends('layouts.app')

@section('title', 'SDCC - Mon Historique de Réservations')

@section('content')
<style>
    :root {
        --primary: #4CAF50;
        --primary-dark: #2E7D32;
        --primary-light: #66BB6A;
        --orange: #FFA726;
        --orange-dark: #E65100;
        --green-50: #F1F8E9;
        --green-100: #E8F5E9;
        --green-200: #C8E6C9;
        --green-300: #A5D6A7;
        --green-700: #2E7D32;
        --text-dark: #1a1a1a;
        --text-light: #666;
        --text-muted: #999;
        --border: #e5e7eb;
        --shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        --shadow-lg: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    * { box-sizing: border-box; }

    .main-content:has(.history-container) {
        padding-left: 24px;
        padding-right: 24px;
    }

    .history-container {
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0 0 32px;
        background: linear-gradient(135deg, var(--green-50) 0%, #f8fafc 100%);
        min-height: calc(100vh - 70px);
    }

    .history-page-section {
        width: 100%;
    }

    /* HEADER */
    .history-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 25%, var(--orange) 75%, #FFA500 100%);
        color: white;
        padding: 30px 32px;
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-lg);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        width: 100%;
    }

    .header-info h1 {
        margin: 0 0 8px 0;
        font-size: 28px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-info p {
        margin: 0;
        font-size: 14px;
        color: rgba(255, 255, 255, 0.95);
    }

    .header-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .export-btn {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.5);
        padding: 12px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .export-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.8);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* STATS */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 24px;
        width: 100%;
    }

    .stat-card {
        background: white;
        padding: 24px;
        min-height: 118px;
        border-radius: 12px;
        border-left: 5px solid var(--primary);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 100%;
    }

    .stat-card.interactive {
        cursor: pointer;
        user-select: none;
    }

    .stat-card.interactive:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-left-color: var(--orange);
    }

    .stat-card.interactive.active {
        background: linear-gradient(135deg, #ffffff 0%, var(--green-50) 100%);
        border-left-width: 6px;
        border-left-color: var(--primary-dark);
        box-shadow: 0 8px 24px rgba(46, 125, 50, 0.18);
        outline: 2px solid rgba(76, 175, 80, 0.25);
        transform: translateY(-2px);
    }

    .stat-card.orange { border-left-color: var(--orange); }
    .stat-card.orange.interactive.active { border-left-color: var(--orange-dark); outline-color: rgba(255, 167, 38, 0.35); }
    .stat-card.green-accent { border-left-color: var(--primary-light); }
    .stat-card.red-accent { border-left-color: #e53935; }
    .stat-card.red-accent.interactive.active { border-left-color: #c62828; outline-color: rgba(229, 57, 53, 0.25); }

    .stat-card-badge {
        display: none;
        position: absolute;
        top: 12px;
        right: 12px;
        background: var(--primary);
        color: white;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 12px;
        letter-spacing: 0.3px;
        animation: statBadgePop 0.35s ease;
    }

    .stat-card.interactive.active .stat-card-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    @keyframes statBadgePop {
        from { transform: scale(0.6); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .stat-label {
        font-size: 12px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .stat-value {
        font-size: 36px;
        font-weight: 700;
        color: var(--primary);
        line-height: 1.1;
    }

    /* FILTERS */
    .filters-section {
        background: white;
        padding: 24px;
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: var(--shadow);
        width: 100%;
    }

    .filters-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 16px 20px;
        margin-bottom: 16px;
        width: 100%;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--primary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-input,
    .filter-select {
        padding: 11px 13px;
        border: 2px solid var(--green-200);
        border-radius: 8px;
        font-size: 13px;
        font-family: inherit;
        background: var(--green-50);
        color: var(--text-dark);
        transition: all 0.3s ease;
    }

    .filter-input:focus,
    .filter-select:focus {
        outline: none;
        border-color: var(--primary);
        background: white;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 11px 22px;
        border: 2px solid var(--primary);
        background: var(--primary);
        color: white;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .filter-btn:hover {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }

    .filter-reset-btn {
        padding: 11px 22px;
        border: 2px solid var(--green-200);
        background: white;
        color: var(--primary);
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .filter-reset-btn:hover {
        background: var(--green-50);
        border-color: var(--primary);
    }

    /* TABLE */
    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow);
        margin-bottom: 24px;
        width: 100%;
    }

    .table-responsive {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: var(--green-50);
    }

    th {
        padding: 16px 14px;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        color: var(--green-700);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--green-200);
        white-space: nowrap;
    }

    td {
        padding: 16px 14px;
        border-bottom: 1px solid var(--green-100);
        font-size: 13px;
        color: var(--text-dark);
    }

    tbody tr:hover {
        background: var(--green-50);
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    #reservationsTableWrapper {
        transition: opacity 0.25s ease, transform 0.25s ease;
    }

    #reservationsTableWrapper.is-loading {
        opacity: 0.45;
        pointer-events: none;
        transform: translateY(4px);
    }

    #reservationsTableWrapper.is-updated {
        animation: tableFadeIn 0.35s ease;
    }

    @keyframes tableFadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* STATUS BADGES */
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

    .status-pending {
        background: #FFF3E0;
        color: #E65100;
    }

    .status-approved {
        background: #E8F5E9;
        color: #2E7D32;
    }

    .status-cancelled {
        background: #FFEBEE;
        color: #c62828;
    }

    .status-rejected {
        background: #FFEBEE;
        color: #c62828;
    }

    /* EMPTY STATE */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow);
        width: 100%;
    }

    .empty-state i {
        font-size: 48px;
        color: var(--primary);
        opacity: 0.3;
        margin-bottom: 16px;
        display: block;
    }

    .empty-state p {
        color: var(--text-light);
        font-size: 14px;
        margin: 0;
    }

    /* PAGINATION INFO */
    .pagination-info {
        text-align: center;
        padding: 20px;
        color: var(--text-muted);
        font-size: 13px;
    }

    /* BACK BUTTON */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .back-link:hover {
        color: var(--primary-dark);
        transform: translateX(-4px);
    }

    /* RESPONSIVE */
    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filters-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .main-content:has(.history-container) {
            padding-left: 16px;
            padding-right: 16px;
        }

        .history-header {
            flex-direction: column;
            align-items: flex-start;
            padding: 22px;
        }

        .header-actions {
            width: 100%;
        }

        .stats-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .filters-grid {
            grid-template-columns: 1fr;
        }

        th, td {
            font-size: 12px;
            padding: 12px 10px;
        }

        .stat-value {
            font-size: 28px;
        }

        .stat-card {
            min-height: auto;
            padding: 18px;
        }

        .history-header h1 {
            font-size: 22px;
        }
    }

    @media (min-width: 769px) and (max-width: 1024px) {
        .filters-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>

<div class="history-container">
    <!-- BACK BUTTON -->
    <a href="{{ route('mes-demandes.index') }}" class="back-link">
        <i class="fas fa-arrow-left"></i>
        Retour à mes demandes
    </a>

    <!-- HEADER -->
    <div class="history-header history-page-section">
        <div class="header-info">
            <h1>
                <i class="fas fa-history"></i>
                Mon Historique de Réservations
            </h1>
            <p>Consultez et gérez l'historique de vos demandes de réservation de véhicules</p>
        </div>
        <div class="header-actions">
            <form action="{{ route('mes-demandes.export-history') }}" method="GET" style="display: flex;">
                @foreach(['status' => $statusFilter, 'car_id' => $carFilter, 'date_from' => $dateFrom, 'date_to' => $dateTo, 'q' => $search] as $param => $value)
                    @if($value)
                        <input type="hidden" name="{{ $param }}" value="{{ $value }}">
                    @endif
                @endforeach
                <button type="submit" class="export-btn" title="Télécharger votre historique en format CSV">
                    <i class="fas fa-file-csv"></i>
                    Exporter CSV
                </button>
            </form>
        </div>
    </div>

    <!-- STATS -->
    @php $activeCard = $cardFilter ?? 'total'; @endphp
    <div class="stats-grid history-page-section" id="historyStatsGrid" role="tablist" aria-label="Filtrer par statut">
        <div
            class="stat-card interactive blue {{ $activeCard === 'total' ? 'active' : '' }}"
            data-card="total"
            role="tab"
            aria-selected="{{ $activeCard === 'total' ? 'true' : 'false' }}"
            tabindex="0"
            title="Afficher toutes vos demandes"
        >
            <span class="stat-card-badge"><i class="fas fa-filter"></i> <span class="stat-active-count"></span></span>
            <div class="stat-label">Total</div>
            <div class="stat-value" data-stat="total">{{ $stats['total'] }}</div>
        </div>
        <div
            class="stat-card interactive orange {{ $activeCard === 'en_attente' ? 'active' : '' }}"
            data-card="en_attente"
            role="tab"
            aria-selected="{{ $activeCard === 'en_attente' ? 'true' : 'false' }}"
            tabindex="0"
            title="Afficher les demandes en attente"
        >
            <span class="stat-card-badge"><i class="fas fa-filter"></i> <span class="stat-active-count"></span></span>
            <div class="stat-label">En Attente</div>
            <div class="stat-value" data-stat="pending">{{ $stats['pending'] }}</div>
        </div>
        <div
            class="stat-card interactive green-accent {{ $activeCard === 'approuvee' ? 'active' : '' }}"
            data-card="approuvee"
            role="tab"
            aria-selected="{{ $activeCard === 'approuvee' ? 'true' : 'false' }}"
            tabindex="0"
            title="Afficher les demandes approuvées"
        >
            <span class="stat-card-badge"><i class="fas fa-filter"></i> <span class="stat-active-count"></span></span>
            <div class="stat-label">Approuvées</div>
            <div class="stat-value" data-stat="approved">{{ $stats['approved'] }}</div>
        </div>
        <div
            class="stat-card interactive red-accent {{ $activeCard === 'annulee' ? 'active' : '' }}"
            data-card="annulee"
            role="tab"
            aria-selected="{{ $activeCard === 'annulee' ? 'true' : 'false' }}"
            tabindex="0"
            title="Afficher les demandes annulées"
        >
            <span class="stat-card-badge"><i class="fas fa-filter"></i> <span class="stat-active-count"></span></span>
            <div class="stat-label">Annulées</div>
            <div class="stat-value" data-stat="cancelled">{{ $stats['cancelled'] }}</div>
        </div>
    </div>

    <!-- FILTERS -->
    <div class="filters-section history-page-section">
        <div class="filters-title">
            <i class="fas fa-filter"></i>
            Filtres
        </div>

        <form method="GET" action="{{ route('mes-demandes.history') }}" id="historyFiltersForm">
            <div class="filters-grid">
                <!-- Search -->
                <div class="filter-group">
                    <label class="filter-label">Recherche</label>
                    <input type="text" name="q" class="filter-input" placeholder="Véhicule, destination, raison..." value="{{ $search }}">
                </div>

                <!-- Status Filter -->
                <div class="filter-group">
                    <label class="filter-label">Statut</label>
                    <select name="status" class="filter-select">
                        <option value="all" @selected($statusFilter === 'all')>Tous les statuts</option>
                        <option value="pending" @selected($statusFilter === 'pending')>En Attente</option>
                        <option value="approved" @selected($statusFilter === 'approved')>Approuvée</option>
                        <option value="cancelled" @selected($statusFilter === 'cancelled')>Annulée</option>
                        <option value="rejected" @selected($statusFilter === 'rejected')>Rejetée</option>
                    </select>
                </div>

                <!-- Vehicle Filter -->
                <div class="filter-group">
                    <label class="filter-label">Véhicule</label>
                    <select name="car_id" class="filter-select">
                        <option value="0" @selected($carFilter === 0)>Tous les véhicules</option>
                        @foreach($cars as $car)
                            <option value="{{ $car->id }}" @selected($carFilter === $car->id)>
                                {{ $car->name }} ({{ $car->matricule }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From -->
                <div class="filter-group">
                    <label class="filter-label">Date De</label>
                    <input type="date" name="date_from" class="filter-input" value="{{ $dateFrom }}">
                </div>

                <!-- Date To -->
                <div class="filter-group">
                    <label class="filter-label">Date Au</label>
                    <input type="date" name="date_to" class="filter-input" value="{{ $dateTo }}">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="filter-btn">
                    <i class="fas fa-search"></i>
                    Filtrer
                </button>
                <a href="{{ route('mes-demandes.history') }}" class="filter-reset-btn">
                    <i class="fas fa-redo"></i>
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- TABLE -->
    <div class="table-container history-page-section" id="reservationsTableWrapper" style="{{ $reservations->count() === 0 ? 'display:none;' : '' }}">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Date de Demande</th>
                        <th>Véhicule</th>
                        <th>Matricule</th>
                        <th>Destination</th>
                        <th>Départ</th>
                        <th>Retour</th>
                        <th>Raison</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody id="reservationsTableBody">
                    @include('mes-demandes.partials.history-table-body', ['reservations' => $reservations])
                </tbody>
            </table>
        </div>
        <div class="pagination-info">
            Total : <strong id="reservationsCount">{{ $reservations->count() }}</strong> réservation(s)
        </div>
    </div>
    <div class="empty-state history-page-section" id="reservationsEmptyState" style="{{ $reservations->count() > 0 ? 'display:none;' : '' }}">
        <i class="fas fa-inbox"></i>
        <p id="reservationsEmptyMessage">Vous n'avez pas encore de réservations ou aucune réservation ne correspond à vos critères de recherche.</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterUrl = @json(route('mes-demandes.history.filter'));
        const statsGrid = document.getElementById('historyStatsGrid');
        const filtersForm = document.getElementById('historyFiltersForm');
        const tableWrapper = document.getElementById('reservationsTableWrapper');
        const tableBody = document.getElementById('reservationsTableBody');
        const emptyState = document.getElementById('reservationsEmptyState');
        const countEl = document.getElementById('reservationsCount');
        const statusSelect = filtersForm ? filtersForm.querySelector('[name="status"]') : null;

        let activeCard = @json($activeCard ?? 'total');
        let fetchController = null;

        const cardToStatus = {
            total: 'all',
            en_attente: 'pending',
            approuvee: 'approved',
            annulee: 'cancelled',
            rejetee: 'rejected',
        };

        function setActiveCard(card) {
            activeCard = card;
            statsGrid.querySelectorAll('[data-card]').forEach(function(el) {
                const isActive = el.dataset.card === card;
                el.classList.toggle('active', isActive);
                el.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });
            if (statusSelect && cardToStatus[card]) {
                statusSelect.value = cardToStatus[card];
            }
        }

        function updateStats(stats, filteredCount) {
            const map = {
                total: stats.total,
                pending: stats.pending,
                approved: stats.approved,
                cancelled: stats.cancelled,
            };
            statsGrid.querySelectorAll('[data-stat]').forEach(function(el) {
                const key = el.dataset.stat;
                if (map[key] !== undefined) {
                    el.textContent = map[key];
                }
            });
            statsGrid.querySelectorAll('.stat-active-count').forEach(function(el) {
                el.textContent = '';
            });
            const activeBadge = statsGrid.querySelector('[data-card="' + activeCard + '"] .stat-active-count');
            if (activeBadge) {
                const label = filteredCount > 1 ? ' affichées' : ' affichée';
                activeBadge.textContent = filteredCount + label;
            }
        }

        function buildFilterParams(card) {
            const params = new URLSearchParams(new FormData(filtersForm));
            params.set('card', card);
            params.delete('status');
            return params;
        }

        async function filterByCard(card) {
            if (!tableBody || !filtersForm) return;

            setActiveCard(card);

            if (fetchController) {
                fetchController.abort();
            }
            fetchController = new AbortController();

            tableWrapper.classList.add('is-loading');

            try {
                const response = await fetch(filterUrl + '?' + buildFilterParams(card).toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: fetchController.signal,
                });

                if (!response.ok) {
                    throw new Error('Filtrage impossible');
                }

                const data = await response.json();
                tableBody.innerHTML = data.html;
                countEl.textContent = data.count;

                const hasRows = data.count > 0;
                tableWrapper.style.display = hasRows ? '' : 'none';
                emptyState.style.display = hasRows ? 'none' : 'block';

                if (data.stats) {
                    updateStats(data.stats, data.count);
                }

                tableWrapper.classList.remove('is-loading');
                tableWrapper.classList.remove('is-updated');
                void tableWrapper.offsetWidth;
                tableWrapper.classList.add('is-updated');
            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.error(error);
                }
                tableWrapper.classList.remove('is-loading');
            }
        }

        if (activeCard !== 'total') {
            updateStats({
                total: {{ $stats['total'] }},
                pending: {{ $stats['pending'] }},
                approved: {{ $stats['approved'] }},
                cancelled: {{ $stats['cancelled'] }},
            }, {{ $reservations->count() }});
        }

        statsGrid.querySelectorAll('[data-card]').forEach(function(cardEl) {
            cardEl.addEventListener('click', function() {
                filterByCard(cardEl.dataset.card);
            });
            cardEl.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    filterByCard(cardEl.dataset.card);
                }
            });
        });
    });
</script>

@endsection
