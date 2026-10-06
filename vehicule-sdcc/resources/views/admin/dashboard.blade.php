@extends('layouts.app')
@section('title', 'SDCC - Tableau de bord Admin')

@section('content')
<style>
    /* Dashboard Content Wrapper - Using Global Unified Content Wrapper */
    /* All dashboard-specific content uses the global .content-wrapper */

    /* Dashboard-specific styles only */
    .dashboard-header {
        background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 25%, #FFA726 75%, #FFA500 100%);
        color: white;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .header-info h1 {
        font-size: 24px;
        margin-bottom: 5px;
    }

    .header-info p {
        font-size: 12px;
        opacity: 0.8;
    }

    .header-actions {
        display: flex;
        gap: 15px;
    }

    .export-btn {
        background: #4CAF50;
        color: white;
        border: 2px solid white;
        padding: 12px 24px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .export-btn:hover {
        background: #45a049;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        transform: translateY(-2px);
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
        width: 100%;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        border-left: 5px solid transparent;
        border-image: linear-gradient(180deg, #4CAF50 0%, #FFA726 100%) 1;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        pointer-events: auto;
    }

    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .stat-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
        border-radius: 10px;
        cursor: pointer;
        pointer-events: auto;
        position: relative;
        z-index: 10;
    }

    .stat-card-link:focus-visible {
        outline: 3px solid rgba(255, 167, 38, 0.45);
        outline-offset: 2px;
    }

    .stat-card.interactive {
        cursor: pointer;
        position: relative;
        overflow: hidden;
        z-index: 10;
        pointer-events: auto;
    }

    .stat-card.interactive::after {
        content: '\f061';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: 14px;
        top: 14px;
        color: rgba(76, 175, 80, 0.55);
        font-size: 13px;
        transition: transform 0.2s ease;
    }

    .stat-card.interactive:hover::after {
        transform: translateX(3px);
    }

    /* Active / selected state */
    .stat-card.active {
        transform: translateY(-4px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        outline: 2px solid rgba(0,0,0,0.04);
    }

    /* Color variants for stat cards */
    .stat-card.blue { border-left-color: #4CAF50; }
    .stat-card.orange { border-left-color: #FFA726; }
    .stat-card.green { border-left-color: #66BB6A; }
    .stat-card.purple { border-left-color: #FFA500; }

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

    .alert-banner {
        background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 30%, #FFA726 70%, #FFA500 100%);
        border: none;
        color: white;
        padding: 20px 25px;
        border-radius: 8px;
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .alert-text {
        color: white;
        font-size: 14px;
        font-weight: 500;
    }

    .alert-btn {
        background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 25%, #FFA726 75%, #FFA500 100%);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
    }

    /* Section Styles */
    .section {
        background: white;
        padding: 25px;
        border-radius: 10px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        width: 100%;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
    }

    .section-filter {
        color: #4CAF50;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
        font-weight: 500;
    }

    .section-filter:hover {
        color: #66BB6A;
    }

    /* Demands Table */
    .demands-table {
        width: 100%;
        border-collapse: collapse;
    }

    .demands-table thead {
        background: #f8f8f8;
    }

    .demands-table th {
        padding: 12px 15px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #666;
        border-bottom: 1px solid #e8e8e8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .demands-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }

    .demands-table tbody tr:hover {
        background: #fafafa;
    }

    .demands-table td {
        padding: 15px;
        font-size: 14px;
        color: #1a1a1a;
    }

    /* Employee Avatar & Info */
    .employee-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .employee-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 16px;
        flex-shrink: 0;
    }

    .employee-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .employee-name {
        font-weight: 600;
        color: #1a1a1a;
    }

    .employee-service {
        font-size: 12px;
        color: #999;
    }

    /* Destination Cell */
    .destination-cell {
        color: #4CAF50;
        font-weight: 500;
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        text-align: center;
        white-space: nowrap;
    }

    .status-approved {
        background: #E8F5E9;
        color: #2E7D32;
        border-left: 3px solid #4CAF50;
    }

    .status-pending {
        background: #FFF3E0;
        color: #E65100;
        border-left: 3px solid #FFA726;
    }

    .status-rejected {
        background: #FFEBEE;
        color: #C62828;
        border-left: 3px solid #ff4444;
    }

    /* Empty State */
    .empty-demands {
        text-align: center;
        padding: 40px 20px;
        color: #999;
    }

    .empty-demands i {
        font-size: 32px;
        margin-bottom: 10px;
        color: #ddd;
        display: block;
    }

    /* Analytics Grid */
    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
        width: 100%;
    }

    .analytics-card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .analytics-card-title {
        font-size: 14px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Destinations Chart */
    .destination-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .destination-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .destination-label {
        min-width: 110px;
        font-size: 12px;
        font-weight: 500;
        color: #666;
    }

    .destination-bar-container {
        flex: 1;
        height: 24px;
        background: #f0f0f0;
        border-radius: 4px;
        overflow: hidden;
    }

    .destination-bar {
        height: 100%;
        border-radius: 4px;
        transition: width 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 8px;
        color: white;
        font-size: 11px;
        font-weight: 600;
    }

    .destination-count {
        min-width: 25px;
        text-align: right;
        font-size: 12px;
        font-weight: 600;
        color: #1a1a1a;
    }

    /* Monthly Summary */
    .summary-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 12px;
        border-bottom: 1px solid #f0f0f0;
    }

    .summary-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .summary-label {
        font-size: 12px;
        font-weight: 500;
        color: #666;
    }

    .summary-value {
        font-size: 13px;
        font-weight: 700;
        color: #1a1a1a;
    }

    .summary-value.km {
        color: #2E7D32;
    }

    .summary-value.approval {
        color: #4CAF50;
    }

    .summary-value.duration {
        color: #FFA726;
    }

    .summary-value.vehicle {
        color: #FFB74D;
    }

    .summary-value.employee {
        color: #66BB6A;
    }

    .summary-value.total { color: #1e293b; }
    .summary-value.pending { color: #f59e0b; }
    .summary-value.approved { color: #16a34a; }
    .summary-value.rejected { color: #dc2626; }
    .summary-value.cancelled { color: #64748b; }
    .summary-value.vehicles { color: #0d9488; }

    .summary-label i {
        margin-right: 6px;
        width: 14px;
        text-align: center;
    }

    .analytics-card-subtitle {
        font-size: 12px;
        color: #94a3b8;
        font-weight: 500;
        margin: -12px 0 18px;
        text-transform: capitalize;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .dashboard-content-wrapper {
            max-width: 100%;
            padding: 0 20px;
        }
    }

    @media (max-width: 768px) {
        .dashboard-content-wrapper {
            max-width: 100%;
            padding: 0 15px;
        }

        .dashboard-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
            padding: 20px;
        }

        .header-info h1 {
            font-size: 20px;
        }

        .header-actions {
            width: 100%;
        }

        .export-btn {
            width: 100%;
        }

        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
        }

        .stat-card {
            padding: 15px;
        }

        .stat-value {
            font-size: 24px;
        }

        .section {
            padding: 20px;
        }

        .analytics-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .dashboard-content-wrapper {
            max-width: 100%;
            padding: 0 12px;
        }

        .dashboard-header {
            flex-direction: column;
            gap: 12px;
            padding: 15px;
        }

        .header-info h1 {
            font-size: 18px;
        }

        .header-info p {
            font-size: 11px;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .stat-card {
            padding: 12px;
        }

        .stat-value {
            font-size: 20px;
        }

        .stat-label {
            font-size: 10px;
        }

        .demands-table {
            font-size: 12px;
        }

        .demands-table th,
        .demands-table td {
            padding: 10px;
        }

        .section {
            padding: 15px;
        }

        .alert-banner {
            flex-direction: column;
            gap: 12px;
            align-items: flex-start;
            padding: 15px;
        }
    }
</style>

<!-- Content Wrapper - Unified -->
<div class="content-wrapper">
    <!-- Header -->
    <div class="dashboard-header">
    <div class="header-info">
        <h1>Bonjour, {{ Auth::user()->name }} <i class="fas fa-wave-hand" style="color: #FFA500;"></i></h1>
        <p>{{ now()->format('l d F Y') }} - Administrateur</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('planification.live-excel', request()->query()) }}" class="export-btn"><i class="fas fa-file-excel"></i> Export Excel</a>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <a href="{{ route('planification') }}" class="stat-card-link" title="Voir toutes les réservations">
        <div class="stat-card interactive {{ !request()->has('status') ? 'active' : '' }} blue">
            <div class="stat-value" data-target="{{ $stats['total'] ?? 0 }}">0</div>
            <div class="stat-label">Total Demandes</div>
            <div class="stat-subtitle">total</div>
        </div>
    </a>

    <a href="{{ route('planification', ['status' => 'en_attente']) }}" class="stat-card-link" title="Voir les demandes en attente">
        <div class="stat-card interactive {{ request('status') == 'en_attente' ? 'active' : '' }} orange">
            <div class="stat-value" data-target="{{ $stats['en_attente'] ?? 0 }}">0</div>
            <div class="stat-label">En Attente</div>
            <div class="stat-subtitle">à valider</div>
        </div>
    </a>

    <a href="{{ route('planification', ['status' => 'approuvee']) }}" class="stat-card-link" title="Voir les demandes approuvées">
        <div class="stat-card interactive {{ request('status') == 'approuvee' ? 'active' : '' }} green">
            <div class="stat-value" data-target="{{ $stats['approuvees'] ?? 0 }}">0</div>
            <div class="stat-label">Approuvées</div>
            <div class="stat-subtitle">validées</div>
        </div>
    </a>

    <a href="{{ route('planification', ['status' => 'annulee']) }}" class="stat-card-link" title="Voir les demandes annulées">
        <div class="stat-card interactive {{ request('status') == 'annulee' ? 'active' : '' }} purple">
            <div class="stat-value" data-target="{{ $stats['annulees'] ?? 0 }}">0</div>
            <div class="stat-label">Annulées</div>
            <div class="stat-subtitle">annulées</div>
        </div>
    </a>

    <a href="{{ route('cars.index') }}" class="stat-card-link" title="Voir les véhicules disponibles">
        <div class="stat-card interactive" style="border-left-color: #66BB6A;">
            <div class="stat-value" data-target="{{ $stats['vehicules_dispo'] ?? 0 }}">0</div>
            <div class="stat-label">Véhicules Dispo</div>
            <div class="stat-subtitle">disponibles</div>
        </div>
    </a>
</div>

<!-- Alert Banner -->
<div class="alert-banner">
    <span class="alert-text"><i class="fas fa-exclamation-triangle"></i> {{ $stats['en_attente'] }} demandes en attente d'approbation</span>
    <a href="{{ route('planification') }}" class="alert-btn">Valider maintenant <i class="fas fa-arrow-right"></i></a>
</div>

<!-- Dernières Demandes -->
<div class="section">
    <div class="section-header">
        <h2 class="section-title">Dernières demandes</h2>
        <a href="{{ route('planification') }}" class="section-filter">Tout voir <i class="fas fa-arrow-right"></i></a>
    </div>

    @if(count($demandes) > 0)
        <table class="demands-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Employé</th>
                    <th style="width: 25%;">Destination</th>
                    <th style="width: 15%;">Date Usage</th>
                    <th style="width: 20%;">Véhicule</th>
                    <th style="width: 15%;">Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandes as $demande)
                    <tr>
                        <td>
                            <div class="employee-cell">
                                <div class="employee-avatar" style="background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);">
                                    {{ substr($demande['employee'], 0, 1) }}
                                </div>
                                <div class="employee-info">
                                    <div class="employee-name">{{ $demande['employee'] }}</div>
                                    <div class="employee-service">{{ $demande['service'] ?? 'Service' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="destination-cell">{{ $demande['destination'] }}</span>
                        </td>
                        <td>{{ $demande['date_usage'] }}</td>
                        <td>{{ $demande['vehicle'] ?? '—' }}</td>
                        <td>
                            @if($demande['status'] == 'Approuvé')
                                <span class="status-badge status-approved"><i class="fas fa-check"></i> {{ $demande['status'] }}</span>
                            @elseif($demande['status'] == 'En attente')
                                <span class="status-badge status-pending"><i class="fas fa-clock"></i> {{ $demande['status'] }}</span>
                            @else
                                <span class="status-badge status-rejected"><i class="fas fa-times"></i> {{ $demande['status'] }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-demands">
            <i class="fas fa-inbox"></i>
            <p>Aucune demande</p>
        </div>
    @endif
</div>

<!-- Analytics Grid -->
<div class="analytics-grid">
    <!-- Destinations Chart -->
    <div class="analytics-card">
        <h3 class="analytics-card-title"><i class="fas fa-map-marker-alt"></i> Destinations les plus fréquentes</h3>
        <div class="destination-list">
            @forelse($analytics['destinations'] ?? [] as $dest)
                <div class="destination-item">
                    <div class="destination-label">{{ $dest['name'] }}</div>
                    <div class="destination-bar-container">
                        <div class="destination-bar" style="width: {{ ($dest['count'] / ($analytics['max_destination'] ?? 1)) * 100 }}%; background: {{ $dest['color'] }};">
                        </div>
                    </div>
                    <div class="destination-count">{{ $dest['count'] }}</div>
                </div>
            @empty
                <p style="color: #999; text-align: center; padding: 20px;">Aucune donnée disponible</p>
            @endforelse
        </div>
    </div>

    <!-- Monthly Summary -->
    @php $summary = $analytics['monthly_summary'] ?? []; @endphp
    <div class="analytics-card">
        <h3 class="analytics-card-title"><i class="fas fa-chart-line"></i> Résumé mensuel</h3>
        <p class="analytics-card-subtitle">{{ $analytics['month_label'] ?? '' }}</p>
        <div class="summary-list">
            <div class="summary-item">
                <span class="summary-label"><i class="fas fa-clipboard-list"></i>Total demandes</span>
                <span class="summary-value total">{{ $summary['total'] ?? 0 }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><i class="fas fa-check-circle"></i>Approuvées</span>
                <span class="summary-value approved">{{ $summary['approved'] ?? 0 }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><i class="fas fa-clock"></i>En attente</span>
                <span class="summary-value pending">{{ $summary['pending'] ?? 0 }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><i class="fas fa-times-circle"></i>Rejetées</span>
                <span class="summary-value rejected">{{ $summary['rejected'] ?? 0 }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><i class="fas fa-ban"></i>Annulées</span>
                <span class="summary-value cancelled">{{ $summary['cancelled'] ?? 0 }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><i class="fas fa-percent"></i>Taux d'approbation</span>
                <span class="summary-value approval">{{ $summary['approval_rate'] ?? 0 }}%</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><i class="fas fa-car"></i>Véhicules utilisés</span>
                <span class="summary-value vehicles">{{ $summary['vehicles_used'] ?? 0 }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Close content-wrapper -->
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function animateValue(el, target, duration = 800) {
            const start = 0;
            const end = parseInt(target, 10) || 0;
            if (end === 0) { el.textContent = '0'; return; }
            const range = end - start;
            let current = start;
            const increment = Math.max(1, Math.floor(range / (duration / 16)));
            const timer = setInterval(() => {
                current += increment;
                if (current >= end) {
                    el.textContent = end;
                    clearInterval(timer);
                } else {
                    el.textContent = current;
                }
            }, 16);
        }

        document.querySelectorAll('.stat-value').forEach(el => {
            const target = el.dataset.target ?? el.getAttribute('data-target');
            if (typeof target !== 'undefined') {
                animateValue(el, target);
            }
        });
    });
</script>

@endsection