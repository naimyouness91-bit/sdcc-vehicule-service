@extends('layouts.app')

@section('content')
<style>
    .main-content:has(.planning-container) {
        padding-left: 24px;
        padding-right: 24px;
    }

    .planning-container {
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0 0 32px;
    }

    .header-section {
        background: linear-gradient(135deg, #2E7D32 0%, #4CAF50 100%);
        border-radius: 12px;
        padding: 32px;
        margin-bottom: 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 8px 24px rgba(46, 125, 50, 0.15);
        color: white;
    }

    .header-content h1 {
        margin: 0;
        font-size: 32px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .header-content p {
        margin: 8px 0 0 0;
        color: rgba(255, 255, 255, 0.95);
        font-size: 15px;
    }

    .header-stats {
        text-align: right;
    }

    .header-stats .stat-number {
        font-size: 48px;
        font-weight: 800;
        line-height: 1;
    }

    .header-stats .stat-label {
        font-size: 13px;
        opacity: 0.9;
        margin-top: 8px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 32px;
        width: 100%;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        border-left: 5px solid #4CAF50;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        gap: 18px;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .stat-card-clickable {
        cursor: pointer;
    }

    .stat-card-clickable:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 12px 32px rgba(76, 175, 80, 0.25);
    }

    .stat-card-clickable:active {
        transform: translateY(-4px) scale(0.98);
    }

    .stat-card.pending {
        border-left-color: #FFA726;
    }

    .stat-card.approved {
        border-left-color: #4CAF50;
    }

    .stat-card.cancelled {
        border-left-color: #e53935;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }

    .stat-card.pending .stat-icon {
        background: #fff3e0;
        color: #FFA726;
    }

    .stat-card.approved .stat-icon {
        background: #e8f5e9;
        color: #4CAF50;
    }

    .stat-card.cancelled .stat-icon {
        background: #ffebee;
        color: #e53935;
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        color: #888;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        color: #222;
        font-size: 32px;
        font-weight: 800;
        line-height: 1;
        margin-top: 6px;
    }

    .filter-section {
        background: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 28px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        width: 100%;
    }

    .filter-form {
        display: grid;
        grid-template-columns: 1fr auto auto auto;
        gap: 16px;
        align-items: end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        display: block;
        font-weight: 600;
        font-size: 13px;
        color: #555;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-input, .form-select {
        padding: 12px 14px;
        border: 1.5px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
    }

    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: #4CAF50;
        box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1);
    }

    .form-buttons {
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 12px 18px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4CAF50, #66BB6A);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }

    .btn-secondary {
        background: #f0f0f0;
        color: #666;
    }

    .btn-secondary:hover {
        background: #e8e8e8;
    }

    .export-loader {
        color: white;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
    }

    .btn-primary:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 1024px) {
        .filter-form {
            grid-template-columns: 1fr auto;
        }

        .form-buttons {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 768px) {
        .main-content:has(.planning-container) {
            padding-left: 16px;
            padding-right: 16px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
        .filter-form {
            grid-template-columns: 1fr;
        }

        .header-section {
            flex-direction: column;
            text-align: center;
        }

        .header-stats {
            text-align: center;
            margin-top: 16px;
        }
    }
</style>

<div class="planning-container">
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-content">
            <h1>
                <i class="fas fa-calendar-check"></i>
                Gestion des Réservations
            </h1>
            <p>
                <i class="fas fa-tasks"></i>
                Visualisez et approuvez toutes les demandes de réservation de véhicules
            </p>
        </div>
        <div class="header-stats">
            <div class="stat-number">{{ $stats['total'] }}</div>
            <div class="stat-label">Total des demandes</div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <!-- Total Pending -->
        <div class="stat-card pending stat-card-clickable" data-filter="pending" title="Afficher les demandes en attente">
            <div class="stat-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">En Attente</div>
                <div class="stat-value">{{ $stats['pending'] }}</div>
            </div>
        </div>

        <!-- Total Approved -->
        <div class="stat-card approved stat-card-clickable" data-filter="approved" title="Afficher les demandes approuvées">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Approuvées</div>
                <div class="stat-value">{{ $stats['approved'] }}</div>
            </div>
        </div>

        <!-- Total Cancelled -->
        <div class="stat-card cancelled stat-card-clickable" data-filter="cancelled" title="Afficher les demandes annulées">
            <div class="stat-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Annulées</div>
                <div class="stat-value">{{ $stats['cancelled'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filters and Search Section -->
    <div class="filter-section">
        <form action="{{ route('admin.reservations.index') }}" method="GET" class="filter-form">
            <!-- Search Input -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-search"></i> Rechercher
                </label>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ $search }}" 
                    placeholder="Employé, véhicule, destination..."
                    class="form-input"
                >
            </div>

            <!-- Status Filter -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-filter"></i> Statut
                </label>
                <select name="status" class="form-select" onchange="this.form.submit();">
                    <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Tous</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>En Attente</option>
                    <option value="approved" {{ $statusFilter === 'approved' ? 'selected' : '' }}>Approuvées</option>
                    <option value="cancelled" {{ $statusFilter === 'cancelled' ? 'selected' : '' }}>Annulées</option>
                </select>
            </div>

            <!-- Per Page -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-list"></i> Par page
                </label>
                <select name="per_page" class="form-select" onchange="this.form.submit();">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Chercher
                </button>
                <a href="{{ route('admin.reservations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Réinitialiser
                </a>
                <a href="{{ route('admin.reservations.export') }}" class="btn btn-primary" id="exportBtn" title="Exporter toutes les demandes en Excel">
                    <i class="fas fa-file-excel"></i>
                    <span class="export-text">Exporter Excel</span>
                    <span class="export-loader" style="display: none; margin-left: 8px;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </a>
            </div>
        </form>
    </div>

    <!-- Reservations List - Modern Card View -->
    <div>
        @if($reservations->count() > 0)
            <style>
                .reservations-list {
                    display: flex;
                    flex-direction: column;
                    gap: 16px;
                }

                .reservation-card {
                    background: white;
                    border-radius: 12px;
                    overflow: hidden;
                    border-left: 5px solid;
                    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
                    transition: all 0.3s ease;
                    display: grid;
                    grid-template-columns: auto 1fr auto;
                    gap: 0;
                }

                .reservation-card:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
                }

                .reservation-card.pending {
                    border-left-color: #FFA726;
                }

                .reservation-card.approved {
                    border-left-color: #4CAF50;
                }

                .reservation-card.cancelled {
                    border-left-color: #e53935;
                }

                .reservation-left {
                    padding: 24px;
                    display: flex;
                    align-items: center;
                    gap: 16px;
                    background: #fafafa;
                }

                .user-avatar {
                    width: 56px;
                    height: 56px;
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: 700;
                    font-size: 18px;
                    color: white;
                    flex-shrink: 0;
                }

                .reservation-card.pending .user-avatar {
                    background: linear-gradient(135deg, #FFB74D, #FFA726);
                }

                .reservation-card.approved .user-avatar {
                    background: linear-gradient(135deg, #66BB6A, #4CAF50);
                }

                .reservation-card.cancelled .user-avatar {
                    background: linear-gradient(135deg, #EF5350, #e53935);
                }

                .user-info {
                    display: flex;
                    flex-direction: column;
                    gap: 4px;
                }

                .user-name {
                    font-weight: 700;
                    color: #222;
                    font-size: 15px;
                }

                .user-service {
                    color: #888;
                    font-size: 12px;
                }

                .reservation-center {
                    padding: 24px;
                    display: flex;
                    flex-direction: column;
                    gap: 16px;
                }

                .reservation-info-row {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 24px;
                }

                .info-item {
                    display: flex;
                    flex-direction: column;
                    gap: 4px;
                }

                .info-label {
                    font-size: 11px;
                    font-weight: 700;
                    color: #888;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }

                .info-value {
                    font-size: 14px;
                    color: #222;
                    font-weight: 600;
                    display: flex;
                    align-items: center;
                    gap: 6px;
                }

                .info-value-secondary {
                    font-size: 12px;
                    color: #999;
                    margin-top: 4px;
                }

                .license-plate {
                    background: #fff8e1;
                    padding: 4px 8px;
                    border-radius: 4px;
                    border: 1px solid #ffe082;
                    display: inline-block;
                    font-weight: 600;
                    font-size: 13px;
                    font-family: 'Courier New', monospace;
                }

                .reservation-right {
                    padding: 24px;
                    display: flex;
                    flex-direction: column;
                    align-items: flex-end;
                    gap: 12px;
                    background: white;
                }

                .status-badge {
                    padding: 8px 14px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: 700;
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }

                .status-badge.pending {
                    background: #fff3e0;
                    color: #f57c00;
                }

                .status-badge.approved {
                    background: #e8f5e9;
                    color: #2e7d32;
                }

                .status-badge.cancelled {
                    background: #ffebee;
                    color: #c62828;
                }

                .action-buttons {
                    display: flex;
                    gap: 8px;
                    flex-wrap: wrap;
                    justify-content: flex-end;
                }

                .action-button {
                    padding: 8px 14px;
                    border: none;
                    border-radius: 6px;
                    font-weight: 600;
                    font-size: 12px;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    display: inline-flex;
                    align-items: center;
                    gap: 4px;
                    text-decoration: none;
                }

                .action-button.approve {
                    background: #e8f5e9;
                    color: #2e7d32;
                }

                .action-button.approve:hover {
                    background: #4CAF50;
                    color: white;
                }

                .action-button.cancel {
                    background: #ffebee;
                    color: #c62828;
                }

                .action-button.cancel:hover {
                    background: #e53935;
                    color: white;
                }

                .action-button.details {
                    background: #e3f2fd;
                    color: #1565c0;
                }

                .action-button.details:hover {
                    background: #1565c0;
                    color: white;
                }

                .action-button.edit-btn-link {
                    background: #fff8e1;
                    color: #e65100;
                }

                .action-button.edit-btn-link:hover {
                    background: #FFA726;
                    color: white;
                }

                .action-button.delete-btn {
                    background: #fce4ec;
                    color: #880e4f;
                }

                .action-button.delete-btn:hover {
                    background: #c2185b;
                    color: white;
                }

                @media (max-width: 1024px) {
                    .reservation-card {
                        grid-template-columns: 1fr;
                    }

                    .reservation-left {
                        grid-column: 1;
                    }

                    .reservation-center {
                        grid-column: 1;
                    }

                    .reservation-right {
                        grid-column: 1;
                        align-items: flex-start;
                        flex-direction: row;
                        gap: 16px;
                    }

                    .action-buttons {
                        width: 100%;
                    }
                }

                @media (max-width: 768px) {
                    .reservation-card {
                        grid-template-columns: 1fr;
                    }

                    .reservation-left {
                        padding: 16px;
                    }

                    .reservation-center {
                        padding: 16px;
                    }

                    .reservation-right {
                        padding: 16px;
                    }

                    .reservation-info-row {
                        grid-template-columns: 1fr;
                        gap: 12px;
                    }

                    .action-buttons {
                        justify-content: flex-start;
                    }
                }
            </style>

            <div class="reservations-list">
                @foreach($reservations as $reservation)
                    <div class="reservation-card {{ $reservation->status }}">
                        <!-- Left Section: User Info -->
                        <div class="reservation-left">
                            <div class="user-avatar">{{ substr($reservation->user?->name ?? '?', 0, 1) }}</div>
                            <div class="user-info">
                                <div class="user-name">{{ $reservation->user?->name ?? 'Utilisateur supprimé' }}</div>
                                <div class="user-service">
                                    <i class="fas fa-building" style="margin-right: 4px;"></i>
                                    {{ $reservation->user?->service ?? 'Non défini' }}
                                </div>
                            </div>
                        </div>

                        <!-- Center Section: Reservation Details -->
                        <div class="reservation-center">
                            <div class="reservation-info-row">
                                <!-- Vehicle -->
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-car"></i> Véhicule
                                    </div>
                                    <div class="info-value">
                                        {{ $reservation->car?->name ?? 'Véhicule supprimé' }}
                                    </div>
                                    <div class="info-value-secondary">
                                        <span class="license-plate">{{ $reservation->car?->matricule ?? 'N/A' }}</span>
                                    </div>
                                </div>

                                <!-- Dates -->
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-calendar"></i> Période
                                    </div>
                                    <div class="info-value">
                                        {{ $reservation->start_date ? \Carbon\Carbon::parse($reservation->start_date)->format('d/m/Y') : 'N/A' }} → {{ $reservation->end_date ? \Carbon\Carbon::parse($reservation->end_date)->format('d/m/Y') : 'N/A' }}
                                    </div>
                                    <div class="info-value-secondary">
                                        <i class="fas fa-clock"></i>
                                        {{ $reservation->start_time }} à {{ $reservation->end_time ?? '--:--' }}
                                    </div>
                                </div>

                                <!-- Destination -->
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-map-marker-alt"></i> Destination
                                    </div>
                                    <div class="info-value">
                                        {{ $reservation->destination ?? 'Non spécifiée' }}
                                    </div>
                                    @if($reservation->reason)
                                        <div class="info-value-secondary">
                                            <i class="fas fa-sticky-note"></i>
                                            {{ Str::limit($reservation->reason, 35) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Right Section: Status & Actions -->
                        <div class="reservation-right">
                            <span class="status-badge {{ $reservation->status }}">
                                @if($reservation->status === 'pending')
                                    <i class="fas fa-hourglass-half"></i>En Attente
                                @elseif($reservation->status === 'approved')
                                    <i class="fas fa-check-circle"></i>Approuvée
                                @elseif($reservation->status === 'cancelled')
                                    <i class="fas fa-times-circle"></i>Annulée
                                @endif
                            </span>

                            <div class="action-buttons" id="actions-{{ $reservation->id }}">
                                @if($reservation->status !== 'approved')
                                    <button
                                        class="action-button approve approve-btn"
                                        data-id="{{ $reservation->id }}"
                                        data-car="{{ $reservation->car?->name ?? '' }}"
                                        data-employee="{{ $reservation->user?->name ?? '' }}"
                                    >
                                        <i class="fas fa-check"></i> Approuver
                                    </button>
                                @endif

                                @if($reservation->status !== 'cancelled')
                                    <button
                                        class="action-button cancel cancel-btn"
                                        data-id="{{ $reservation->id }}"
                                        data-car="{{ $reservation->car?->name ?? '' }}"
                                        data-employee="{{ $reservation->user?->name ?? '' }}"
                                    >
                                        <i class="fas fa-ban"></i> Annuler
                                    </button>
                                @endif

                                <a
                                    href="{{ route('admin.reservations.edit', $reservation->id) }}"
                                    class="action-button edit-btn-link"
                                    title="Modifier la réservation"
                                >
                                    <i class="fas fa-edit"></i> Modifier
                                </a>

                                <a
                                    href="{{ route('admin.reservations.show', $reservation->id) }}"
                                    class="action-button details"
                                    title="Voir les détails"
                                >
                                    <i class="fas fa-eye"></i> Détails
                                </a>

                                <button
                                    class="action-button delete-btn"
                                    data-id="{{ $reservation->id }}"
                                    data-employee="{{ $reservation->user?->name ?? '' }}"
                                    data-destination="{{ $reservation->destination }}"
                                    title="Supprimer définitivement"
                                >
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($reservations->hasPages())
                <div style="margin-top: 32px; padding: 24px; background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); display: flex; justify-content: center; align-items: center; gap: 8px;">
                    @if($reservations->onFirstPage())
                        <span style="padding: 10px 12px; background: #f0f0f0; color: #ccc; border-radius: 6px; cursor: not-allowed;">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $reservations->previousPageUrl() }}" style="padding: 10px 12px; background: white; color: #4CAF50; border: 1.5px solid #e0e0e0; border-radius: 6px; text-decoration: none; transition: all 0.3s; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif

                    @foreach($reservations->getUrlRange(1, $reservations->lastPage()) as $page => $url)
                        @if($page == $reservations->currentPage())
                            <span style="min-width: 40px; padding: 10px 12px; background: linear-gradient(135deg, #4CAF50, #66BB6A); color: white; border-radius: 6px; text-align: center; font-weight: 600;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" style="min-width: 40px; padding: 10px 12px; background: white; color: #4CAF50; border: 1.5px solid #e0e0e0; border-radius: 6px; text-decoration: none; transition: all 0.3s; text-align: center;">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($reservations->hasMorePages())
                        <a href="{{ $reservations->nextPageUrl() }}" style="padding: 10px 12px; background: white; color: #4CAF50; border: 1.5px solid #e0e0e0; border-radius: 6px; text-decoration: none; transition: all 0.3s; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span style="padding: 10px 12px; background: #f0f0f0; color: #ccc; border-radius: 6px; cursor: not-allowed;">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </div>

                <!-- Page Info -->
                <div style="margin-top: 16px; text-align: center; color: #999; font-size: 12px;">
                    Affichage {{ $reservations->firstItem() }} à {{ $reservations->lastItem() }} sur {{ $reservations->total() }} réservations
                </div>
            @endif
        @else
            <div style="background: white; border-radius: 12px; padding: 80px 40px; text-align: center; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
                <div style="font-size: 64px; color: #e0e0e0; margin-bottom: 20px;">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3 style="color: #999; font-size: 22px; margin: 0 0 12px 0; font-weight: 700;">Aucune réservation trouvée</h3>
                <p style="color: #bbb; margin: 0 0 24px 0; font-size: 14px;">
                    {{ $search ? 'Essayez de modifier vos critères de recherche' : 'Aucune réservation pour le moment' }}
                </p>
                @if($search || $statusFilter !== 'all')
                    <a 
                        href="{{ route('admin.reservations.index') }}"
                        class="btn btn-primary"
                    >
                        <i class="fas fa-redo"></i> Réinitialiser les filtres
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Action Button Scripts -->
<script>
    // CSRF token setup
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     document.querySelector('input[name="_token"]')?.value || '';

    // Handle Approve Button
    document.querySelectorAll('.approve-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const carName = this.getAttribute('data-car');
            const employeeName = this.getAttribute('data-employee');

            Swal.fire({
                icon: 'question',
                title: 'Approuver la réservation',
                html: `
                    <div style="text-align: left; margin: 20px 0; font-size: 14px;">
                        <p style="margin: 8px 0;"><strong>Employé:</strong> ${employeeName}</p>
                        <p style="margin: 8px 0;"><strong>Véhicule:</strong> ${carName}</p>
                        <p style="margin: 12px 0; color: #999; font-size: 13px;">
                            <i class="fas fa-info-circle"></i> Cette action approuvera la réservation.
                        </p>
                    </div>
                `,
                confirmButtonText: 'Approuver',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#4CAF50',
                cancelButtonColor: '#757575',
                showCancelButton: true,
                allowOutsideClick: false,
                allowEscapeKey: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/admin/reservations/${id}/approve`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({})
                        });

                        const data = await response.json();

                        if (response.ok) {
                            showToast('success', data.message);
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showToast('error', data.message || 'Une erreur est survenue.');
                        }
                    } catch (error) {
                        showToast('error', 'Erreur réseau. Veuillez réessayer.');
                        console.error('Error:', error);
                    }
                }
            });
        });
    });

    // Handle Cancel Button
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const carName = this.getAttribute('data-car');
            const employeeName = this.getAttribute('data-employee');

            Swal.fire({
                icon: 'warning',
                title: 'Annuler la réservation',
                html: `
                    <div style="text-align: left; margin: 20px 0; font-size: 14px;">
                        <p style="margin: 8px 0;"><strong>Employé:</strong> ${employeeName}</p>
                        <p style="margin: 8px 0;"><strong>Véhicule:</strong> ${carName}</p>
                        <p style="margin: 12px 0; color: #999; font-size: 13px;">
                            <i class="fas fa-warning"></i> Cette action annulera la réservation.
                        </p>
                    </div>
                `,
                confirmButtonText: 'Annuler la demande',
                cancelButtonText: 'Fermer',
                confirmButtonColor: '#e53935',
                cancelButtonColor: '#757575',
                showCancelButton: true,
                allowOutsideClick: false,
                allowEscapeKey: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/admin/reservations/${id}/cancel`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({})
                        });

                        const data = await response.json();

                        if (response.ok) {
                            showToast('success', data.message);
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showToast('error', data.message || 'Une erreur est survenue.');
                        }
                    } catch (error) {
                        showToast('error', 'Erreur réseau. Veuillez réessayer.');
                        console.error('Error:', error);
                    }
                }
            });
        });
    });

    // Handle Delete Button
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            const id          = this.getAttribute('data-id');
            const employee    = this.getAttribute('data-employee');
            const destination = this.getAttribute('data-destination');

            Swal.fire({
                icon: 'warning',
                title: 'Supprimer la réservation ?',
                html: `
                    <div style="text-align:left; margin:16px 0; font-size:14px;">
                        <p style="margin:8px 0;"><strong>Employé:</strong> ${employee}</p>
                        <p style="margin:8px 0;"><strong>Destination:</strong> ${destination}</p>
                        <p style="margin:14px 0 0 0; color:#c62828; font-size:13px; font-weight:600;">
                            <i class="fas fa-exclamation-triangle"></i>
                            Cette action est irréversible. La réservation sera définitivement supprimée.
                        </p>
                    </div>
                `,
                confirmButtonText: '<i class="fas fa-trash"></i> Oui, supprimer',
                cancelButtonText:  'Annuler',
                confirmButtonColor: '#c62828',
                cancelButtonColor:  '#757575',
                showCancelButton:   true,
                allowOutsideClick:  false,
                focusCancel:        true,
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/admin/reservations/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            // Remove the card from the DOM immediately
                            const card = document.querySelector(`#actions-${id}`)?.closest('.reservation-card');
                            if (card) {
                                card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                                card.style.opacity = '0';
                                card.style.transform = 'translateX(30px)';
                                setTimeout(() => card.remove(), 420);
                            }
                            showToast('success', data.message);
                        } else {
                            showToast('error', data.message || 'Erreur lors de la suppression.');
                        }
                    } catch (error) {
                        showToast('error', 'Erreur réseau. Veuillez réessayer.');
                        console.error('Delete error:', error);
                    }
                }
            });
        });
    });

    // Handle Export Excel Button
    document.getElementById('exportBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Show loading state
        const exportBtn = this;
        const exportText = exportBtn.querySelector('.export-text');
        const exportLoader = exportBtn.querySelector('.export-loader');
        const originalText = exportText.textContent;
        
        exportBtn.disabled = true;
        exportLoader.style.display = 'inline-flex';
        exportText.textContent = 'Exportation en cours...';

        // Show success toast notification
        showToast('success', 'Exportation des demandes en cours...');

        // Trigger download
        setTimeout(() => {
            window.location.href = exportBtn.href;
            
            // Reset button after 2 seconds
            setTimeout(() => {
                exportBtn.disabled = false;
                exportLoader.style.display = 'none';
                exportText.textContent = originalText;
                showToast('success', 'Fichier Excel généré avec succès!');
            }, 2000);
        }, 500);
    });

    // Real-time polling for new requests (every 5 seconds)
    let lastTotalCount = {{ $stats['total'] ?? 0 }};
    let pollingInterval;

    function startPolling() {
        pollingInterval = setInterval(async () => {
            try {
                const response = await fetch('{{ route("admin.reservations.api.stats") }}');
                const data = await response.json();

                // Check if there are new requests
                if (data.total > lastTotalCount) {
                    const newRequestCount = data.total - lastTotalCount;
                    
                    // Update the stats display
                    updateStatsDisplay(data);
                    
                    // Show notification
                    showToast('success', `${newRequestCount} nouvelle${newRequestCount > 1 ? 's' : ''} demande${newRequestCount > 1 ? 's' : ''} reçue${newRequestCount > 1 ? 's' : ''}!`);
                    
                    // Play notification sound if available
                    playNotificationSound();
                }

                lastTotalCount = data.total;
            } catch (error) {
                console.error('Polling error:', error);
            }
        }, 5000); // Poll every 5 seconds
    }

    function updateStatsDisplay(stats) {
        // Update stat cards if they exist
        const totalElement = document.querySelector('.header-stats .stat-number');
        if (totalElement) {
            totalElement.textContent = stats.total;
        }

        // Update individual stat cards
        const statCards = {
            'pending': document.querySelector('.stat-card.pending .stat-value'),
            'approved': document.querySelector('.stat-card.approved .stat-value'),
            'cancelled': document.querySelector('.stat-card.cancelled .stat-value'),
        };

        if (statCards.pending) statCards.pending.textContent = stats.pending;
        if (statCards.approved) statCards.approved.textContent = stats.approved;
        if (statCards.cancelled) statCards.cancelled.textContent = stats.cancelled;
    }

    function playNotificationSound() {
        // Create a simple beep sound (no external file needed)
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.5);
        } catch (error) {
            // Silently fail if audio context is not available
            console.debug('Audio notification not available:', error);
        }
    }

    // Make stat cards clickable
    document.querySelectorAll('.stat-card-clickable').forEach(card => {
        card.addEventListener('click', () => {
            const filterValue = card.dataset.filter;
            const statusSelect = document.querySelector('select[name="status"]');
            if (statusSelect) {
                statusSelect.value = filterValue;
                statusSelect.form.submit();
            }
        });
    });

    // Start polling when page loads
    document.addEventListener('DOMContentLoaded', () => {
        startPolling();
    });

    // Clean up polling when page unloads
    window.addEventListener('beforeunload', () => {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
    });
</script>

<!-- CSS for Responsive Design -->
<style>
    @media (max-width: 1024px) {
        .reservation-card {
            grid-template-columns: 1fr;
        }

        .reservation-left {
            grid-column: 1;
        }

        .reservation-center {
            grid-column: 1;
        }

        .reservation-right {
            grid-column: 1;
            align-items: flex-start;
            flex-direction: row;
            gap: 16px;
        }

        .action-buttons {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .planning-container {
            padding: 12px;
        }

        .header-section {
            padding: 24px;
            flex-direction: column;
            text-align: center;
        }

        .header-content h1 {
            font-size: 24px;
        }

        .header-stats {
            text-align: center;
            margin-top: 16px;
        }

        .header-stats .stat-number {
            font-size: 36px;
        }

        .filter-form {
            grid-template-columns: 1fr;
        }

        .form-buttons {
            grid-column: 1 / -1;
        }

        .reservation-card {
            grid-template-columns: 1fr;
        }

        .reservation-left {
            padding: 16px;
        }

        .reservation-center {
            padding: 16px;
        }

        .reservation-right {
            padding: 16px;
            flex-direction: column;
            align-items: flex-start;
        }

        .reservation-info-row {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .action-buttons {
            justify-content: flex-start;
            width: 100%;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
