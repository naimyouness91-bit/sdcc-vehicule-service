@extends('layouts.app')

@section('title', 'SDCC - Tableau de bord')

@section('content')
<style>
    /* Dashboard Header */
    .dashboard-header {
        background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 25%, #FFA726 75%, #FFA500 100%);
        color: white;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .header-info h1 {
        font-size: 24px;
        margin-bottom: 5px;
        color: white;
    }

    .header-info h1 i {
        margin-left: 8px;
    }

    .header-info p {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.9);
    }

    .header-actions {
        display: flex;
        gap: 15px;
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
    }

    .export-btn:hover {
        background: linear-gradient(135deg, #2E7D32 0%, #E65100 100%);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
        transform: translateY(-2px);
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
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
        cursor: pointer;
        pointer-events: auto;
        position: relative;
        z-index: 10;
        border-radius: 10px;
    }

    .stat-card-link:focus-visible {
        outline: 3px solid rgba(255, 167, 38, 0.45);
        outline-offset: 2px;
    }

    .stat-card.interactive {
        cursor: pointer;
        position: relative;
        overflow: hidden;
        user-select: none;
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

    /* Stat cards remain visual-only on dashboard; global shortcut cards handle navigation. */

    .stat-card.blue { border-left-color: #4CAF50; }
    .stat-card.orange { border-left-color: #FFA726; }
    .stat-card.green { border-left-color: #66BB6A; }
    .stat-card.purple { border-left-color: #FFA500; }

    .stat-card-link { display: block; }
    .stat-card:active { transform: translateY(-2px); }

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

    /* Alert Banner */
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
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .alert-btn:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    /* Demands Section */
    .section {
        background: white;
        padding: 25px;
        border-radius: 10px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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

    /* Demands List */
    .demands-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .demand-item {
        padding: 16px;
        border: none;
        border-left: 4px solid #E0E0E0;
        border-radius: 0;
        background: white;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
        transition: all 0.3s;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    }

    .demand-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left-color: #4CAF50;
    }

    .demand-content {
        flex: 1;
    }

    .demand-header {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 4px;
    }

    .demand-title {
        font-weight: 600;
        color: #1a1a1a;
        font-size: 14px;
    }

    .demand-dates {
        font-size: 13px;
        color: #666;
    }

    .demand-footer {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .demand-employee {
        font-size: 12px;
        color: #999;
    }

    .demand-vehicle {
        font-size: 12px;
        color: #999;
    }

    .demand-status-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }

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

    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .stat-card {
            padding: 18px;
        }

        .dashboard-header h1 {
            font-size: 24px;
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .stat-card {
            padding: 16px;
        }

        .stat-value {
            font-size: 28px;
        }

        .dashboard-header {
            flex-direction: column;
            text-align: left;
            gap: 15px;
        }

        .dashboard-header h1 {
            font-size: 22px;
            margin-bottom: 5px;
        }

        .dashboard-header p {
            font-size: 12px;
        }

        .content-wrapper {
            gap: 15px;
        }
    }

    @media (max-width: 640px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .stat-card {
            padding: 14px;
        }

        .stat-value {
            font-size: 24px;
        }

        .stat-label {
            font-size: 11px;
        }

        .dashboard-header {
            gap: 12px;
        }

        .dashboard-header h1 {
            font-size: 20px;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .stat-card {
            padding: 12px;
            flex-direction: column;
        }

        .stat-value {
            font-size: 22px;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 11px;
            font-weight: 600;
        }

        .stat-card > div:last-child {
            font-size: 20px;
            opacity: 0.1;
        }

        .dashboard-header {
            gap: 10px;
            padding: 15px;
            margin-bottom: 10px;
        }

        .dashboard-header h1 {
            font-size: 18px;
            line-height: 1.3;
        }

        .dashboard-header p {
            font-size: 11px;
        }

        .header-actions button {
            width: 100%;
            font-size: 13px;
            padding: 10px 14px;
        }

        .content-wrapper {
            gap: 12px;
        }

        /* Demands section adjustments */
        .demands-section {
            padding: 15px;
        }

        .demands-table-header {
            gap: 8px;
        }

        .demands-table-header button {
            width: 100%;
            font-size: 12px;
            padding: 10px;
        }
    }
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-info">
            <h1>Bonjour, {{ Auth::user()->name }} <i class="fas fa-wave-hand" style="color: #FFA500;"></i></h1>
            <p>{{ now()->format('l d F Y') }} - {{ Auth::user()->service ?? 'SDCC' }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('mes-demandes.create') }}" class="export-btn"><i class="fas fa-plus"></i> Nouvelle demande</a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        @php
            $role = Auth::user()->role ?? null;
            $isEmployeeLike = $role && !in_array($role, ['admin', 'super_admin', 'superadmin']);
        @endphp

        @if(Auth::user() && $isEmployeeLike)
            <!-- Employee Stats -->
            <a href="{{ route('mes-demandes.index') }}" class="stat-card-link" title="Voir toutes mes demandes">
                <div class="stat-card interactive blue {{ !request()->has('status') ? 'active' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <div class="stat-value" data-target="{{ $stats['total'] ?? $stats['total_demandes'] ?? 0 }}">0</div>
                            <div class="stat-label">Total</div>
                            <div class="stat-subtitle">mes demandes</div>
                        </div>
                        <i class="fas fa-file-alt" style="font-size: 24px; color: #4CAF50; opacity: 0.3;"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('mes-demandes.index', ['status' => 'en_attente']) }}" class="stat-card-link" title="Voir mes demandes en attente">
                <div class="stat-card interactive orange {{ request('status') == 'en_attente' ? 'active' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <div class="stat-value" data-target="{{ $stats['en_attente'] ?? 0 }}">0</div>
                            <div class="stat-label">En attente</div>
                            <div class="stat-subtitle">à valider</div>
                        </div>
                        <i class="fas fa-hourglass-half" style="font-size: 24px; color: #FFA726; opacity: 0.3;"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('mes-demandes.index', ['status' => 'approuvee']) }}" class="stat-card-link" title="Voir mes demandes approuvées">
                <div class="stat-card interactive green {{ request('status') == 'approuvee' ? 'active' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <div class="stat-value" data-target="{{ $stats['approuvees'] ?? 0 }}">0</div>
                            <div class="stat-label">Approuvées</div>
                            <div class="stat-subtitle">validées</div>
                        </div>
                        <i class="fas fa-check-circle" style="font-size: 24px; color: #4CAF50; opacity: 0.3;"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('mes-demandes.index', ['status' => 'annulee']) }}" class="stat-card-link" title="Voir mes demandes annulées">
                <div class="stat-card interactive purple {{ request('status') == 'annulee' ? 'active' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <div class="stat-value" data-target="{{ $stats['annulees'] ?? 0 }}">0</div>
                            <div class="stat-label">Annulées</div>
                            <div class="stat-subtitle">annulées</div>
                        </div>
                        <i class="fas fa-times-circle" style="font-size: 24px; color: #e53935; opacity: 0.3;"></i>
                    </div>
                </div>
            </a>
        @else
            <!-- Admin Stats -->
            <a href="{{ route('planification') }}" class="stat-card-link" title="Voir toutes les réservations">
                <div class="stat-card interactive blue {{ !request()->has('status') ? 'active' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <div class="stat-value" data-target="{{ $stats['total'] ?? $stats['total_demandes'] ?? 0 }}">0</div>
                            <div class="stat-label">Total</div>
                            <div class="stat-subtitle">demandes</div>
                        </div>
                        <i class="fas fa-file-alt" style="font-size: 24px; color: #4CAF50; opacity: 0.3;"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('planification', ['status' => 'en_attente']) }}" class="stat-card-link" title="Voir les demandes en attente">
                <div class="stat-card interactive orange {{ request('status') == 'en_attente' ? 'active' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <div class="stat-value" data-target="{{ $stats['en_attente'] ?? 0 }}">0</div>
                            <div class="stat-label">En Attente</div>
                            <div class="stat-subtitle">à valider</div>
                        </div>
                        <i class="fas fa-hourglass-half" style="font-size: 24px; color: #FFA726; opacity: 0.3;"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('planification', ['status' => 'approuvee']) }}" class="stat-card-link" title="Voir les demandes approuvées">
                <div class="stat-card interactive green {{ request('status') == 'approuvee' ? 'active' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <div class="stat-value" data-target="{{ $stats['approuvees'] ?? 0 }}">0</div>
                            <div class="stat-label">Approuvées</div>
                            <div class="stat-subtitle">validées</div>
                        </div>
                        <i class="fas fa-check-circle" style="font-size: 24px; color: #4CAF50; opacity: 0.3;"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('planification', ['status' => 'annulee']) }}" class="stat-card-link" title="Voir les demandes annulées">
                <div class="stat-card interactive purple {{ request('status') == 'annulee' ? 'active' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <div class="stat-value" data-target="{{ $stats['annulees'] ?? 0 }}">0</div>
                            <div class="stat-label">Annulées</div>
                            <div class="stat-subtitle">annulées</div>
                        </div>
                        <i class="fas fa-times-circle" style="font-size: 24px; color: #e53935; opacity: 0.3;"></i>
                    </div>
                </div>
            </a>
        @endif
    </div>

    <!-- Alert Banner (Employee Only) -->
    @if(Auth::user() && Auth::user()->hasRole('employee') && $stats['en_attente'] > 0)
    <div class="alert-banner">
        <span class="alert-text"><i class="fas fa-exclamation-triangle"></i> {{ $stats['en_attente'] }} demande(s) en attente d'approbation</span>
        <a href="{{ route('mes-demandes.index') }}" class="alert-btn">Voir <i class="fas fa-arrow-right"></i></a>
    </div>
    @endif

    <!-- Dernières Demandes -->
    <div class="section">
        <div class="section-header">
            <h2 class="section-title">Mes dernières demandes</h2>
            <a href="{{ route('mes-demandes.index') }}" class="section-filter">Tout voir <i class="fas fa-arrow-right"></i></a>
        </div>

        @if(count($demandes) > 0)
            <div class="demands-list">
                @foreach($demandes as $demande)
                    <div class="demand-item">
                        <div class="demand-content">
                            <div class="demand-header">
                                <span class="demand-title">{{ $demande['destination'] }}</span>
                                <span class="demand-dates">{{ $demande['date_usage'] }}</span>
                            </div>
                            <div class="demand-footer">
                                <span class="demand-employee">{{ $demande['employee'] }}</span>
                                <span class="demand-vehicle">{{ $demande['vehicle'] }}</span>
                            </div>
                        </div>
                        <div class="demand-status-container">
                            @if($demande['status'] == 'Approuvé')
                                <span class="status-badge status-approved"><i class="fas fa-check"></i> {{ $demande['status'] }}</span>
                            @elseif($demande['status'] == 'En attente')
                                <span class="status-badge status-pending"><i class="fas fa-clock"></i> {{ $demande['status'] }}</span>
                            @else
                                <span class="status-badge status-rejected"><i class="fas fa-times"></i> {{ $demande['status'] }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-demands">
                <i class="fas fa-inbox"></i>
                <p>Aucune demande</p>
            </div>
        @endif
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
