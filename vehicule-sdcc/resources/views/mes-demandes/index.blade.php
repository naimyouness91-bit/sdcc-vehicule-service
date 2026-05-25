@extends('layouts.app')
@section('title', 'SDCC - Mes Demandes')
@section('content')
<style>
    /* ==================== PAGE STYLES ==================== */
    
    /* Page Title Section */
    .page-title-section {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
    }
    
    .page-title {
        font-size: 32px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
    }
    
    .page-subtitle {
        font-size: 14px;
        color: #666;
        margin-top: 5px;
    }
    
    .new-request-btn {
        background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
    }
    
    .new-request-btn:hover {
        background: linear-gradient(135deg, #2E7D32 0%, #E65100 100%);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
        transform: translateY(-2px);
    }
    
    /* Filter Tabs */
    .filter-tabs {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }
    
    .filter-tab {
        background: white;
        border: none;
        padding: 10px 18px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        color: #666;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }
    
    .filter-tab:hover {
        color: #4CAF50;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
    }
    
    .filter-tab.active {
        background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
    }
    
    /* Requests List */
    .requests-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    /* Request Item */
    .request-item {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .request-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        border-left-color: #4CAF50;
    }
    
    .request-item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 18px 20px;
    }
    
    .request-destination {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 10px;
    }
    
    .request-dates {
        font-size: 13px;
        color: #666;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .request-dates-icon {
        color: #4CAF50;
        width: 16px;
    }
    
    .request-time {
        color: #999;
        font-size: 12px;
    }
    
    .request-motif {
        padding: 0 20px 12px 20px;
        font-size: 13px;
        color: #666;
    }
    
    .request-motif strong {
        color: #1a1a1a;
    }
    
    .request-vehicle-section {
        background: #f0fef0;
        padding: 12px 20px;
        font-size: 13px;
        color: #2E7D32;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
    }
    
    .request-vehicle-section i {
        color: #4CAF50;
    }
    
    .request-rejection-reason {
        background: #ffebee;
        padding: 12px 20px;
        font-size: 13px;
        color: #c62828;
        margin: 0 20px 12px 20px;
        border-radius: 4px;
        border-left: 3px solid #c62828;
    }
    
    .request-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-top: 1px solid #f5f5f5;
        background: #fafafa;
    }
    
    .request-status-badges {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 14px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }
    
    .badge-approved {
        background: #e8f5e9;
        color: #2e7d32;
    }
    
    .badge-pending {
        background: #fff3e0;
        color: #e65100;
    }
    
    .badge-rejected {
        background: #ffebee;
        color: #c62828;
    }
    
    .badge-cancelled {
        background: #f3e5f5;
        color: #6a1b9a;
    }
    
    .action-link {
        color: #4CAF50;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .action-link:hover {
        color: #2E7D32;
        text-decoration: underline;
    }
    
    .action-link-reject {
        color: #c62828;
    }
    
    .action-link-reject:hover {
        color: #ad1d1d;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }
    
    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        color: #ddd;
        display: block;
    }
    
    .empty-state h3 {
        font-size: 18px;
        margin-bottom: 8px;
        color: #1a1a1a;
    }
    
    .empty-state p {
        font-size: 13px;
        margin: 0;
        color: #999;
    }
    
    /* Responsive */
    @media (max-width: 1024px) {
        .page-title-section {
            gap: 15px;
        }

        .title-group h1 {
            font-size: 24px;
        }

        .title-actions {
            gap: 12px;
        }

        .filter-tabs {
            gap: 8px;
        }

        .request-item {
            padding: 16px;
        }
    }

    @media (max-width: 768px) {
        .page-title-section {
            flex-direction: column;
            gap: 12px;
            padding: 18px;
        }
        
        .title-group h1 {
            font-size: 22px;
        }

        .title-group p {
            font-size: 12px;
        }

        .title-actions {
            width: 100%;
            gap: 10px;
            flex-wrap: wrap;
        }

        .title-actions button {
            flex: 1;
            min-width: 150px;
            padding: 10px 14px;
            font-size: 12px;
            min-height: 40px;
        }
        
        .request-item-header {
            flex-direction: column;
            gap: 12px;
        }

        .request-item-header-left {
            width: 100%;
        }

        .request-item-header-right {
            width: 100%;
            gap: 8px;
        }
        
        .filter-tabs {
            flex-wrap: wrap;
            gap: 8px;
            padding: 0 18px;
            margin-bottom: 15px;
        }

        .filter-tab {
            padding: 8px 12px;
            font-size: 12px;
            min-height: 36px;
        }

        .request-item {
            padding: 14px;
            margin-bottom: 12px;
            border-radius: 8px;
        }

        .request-item-body {
            flex-direction: column;
            gap: 10px;
        }

        .request-item-details {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .detail-row {
            gap: 8px;
        }

        .request-item-destination {
            padding: 10px 12px;
            border-radius: 6px;
        }

        .empty-state {
            padding: 40px 20px;
        }

        .empty-state-icon {
            font-size: 48px;
        }

        .empty-state h3 {
            font-size: 18px;
        }

        .empty-state p {
            font-size: 13px;
        }
    }

    @media (max-width: 640px) {
        .page-title-section {
            padding: 16px;
            gap: 12px;
        }

        .title-group h1 {
            font-size: 20px;
        }

        .title-group p {
            font-size: 11px;
        }

        .title-actions {
            gap: 8px;
        }

        .title-actions button {
            min-width: 120px;
            padding: 10px 12px;
            font-size: 11px;
        }

        .filter-tabs {
            padding: 0 16px;
            gap: 6px;
            margin-bottom: 12px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .filter-tab {
            padding: 8px 10px;
            font-size: 11px;
            min-height: 36px;
            min-width: max-content;
        }

        .request-item {
            padding: 12px;
            margin: 0 16px 10px 16px;
            border-radius: 6px;
        }

        .request-item-header {
            gap: 10px;
            margin-bottom: 8px;
        }

        .request-item-destination {
            padding: 8px 10px;
            font-size: 12px;
            border-radius: 4px;
        }

        .request-item-body {
            gap: 8px;
        }

        .request-item-details {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .detail-row {
            flex-direction: column;
            gap: 6px;
            font-size: 11px;
        }

        .detail-label {
            font-weight: 600;
            font-size: 10px;
        }

        .request-item-actions {
            gap: 6px;
        }

        .request-item-actions button {
            flex: 1;
            padding: 8px 10px;
            font-size: 10px;
            min-height: 36px;
        }
    }

    @media (max-width: 480px) {
        .page-title-section {
            padding: 14px;
            gap: 10px;
            margin-bottom: 10px;
        }

        .title-group h1 {
            font-size: 18px;
            margin-bottom: 4px;
            line-height: 1.3;
        }

        .title-group p {
            font-size: 10px;
        }

        .title-actions {
            width: 100%;
            flex-direction: column;
            gap: 6px;
        }

        .title-actions button {
            width: 100%;
            min-width: auto;
            padding: 10px;
            font-size: 12px;
            min-height: 40px;
        }

        .filter-tabs {
            padding: 0 12px;
            gap: 4px;
            margin: 0 0 12px 0;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .filter-tab {
            padding: 8px 10px;
            font-size: 11px;
            min-height: 36px;
            min-width: max-content;
            white-space: nowrap;
        }

        .filter-tab.active {
            font-weight: 600;
        }

        .request-item {
            padding: 10px;
            margin: 0 10px 10px 10px;
            border-radius: 6px;
        }

        .request-item-header {
            gap: 8px;
            margin-bottom: 8px;
            flex-direction: column;
        }

        .request-item-header-left h3 {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .request-item-header-left p {
            font-size: 10px;
        }

        .request-item-header-right {
            width: 100%;
            gap: 6px;
            flex-direction: column;
        }

        .status-badge {
            width: 100%;
            padding: 8px;
            font-size: 11px;
            text-align: center;
        }

        .request-item-destination {
            padding: 8px;
            font-size: 11px;
            border-radius: 4px;
            word-break: break-word;
        }

        .request-item-body {
            gap: 6px;
        }

        .request-item-details {
            grid-template-columns: 1fr;
            gap: 6px;
        }

        .detail-row {
            flex-direction: column;
            gap: 4px;
            font-size: 10px;
            padding: 6px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #666;
        }

        .detail-value {
            font-size: 11px;
            color: #1a1a1a;
        }

        .request-item-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            margin-top: 8px;
        }

        .request-item-actions button {
            padding: 8px;
            font-size: 10px;
            min-height: 36px;
            border-radius: 4px;
        }

        .empty-state {
            padding: 30px 15px;
            text-align: center;
        }

        .empty-state-icon {
            font-size: 36px;
            margin-bottom: 8px;
        }

        .empty-state h3 {
            font-size: 16px;
            margin-bottom: 4px;
        }

        .empty-state p {
            font-size: 12px;
        }
    }
</style>

<div class="content-wrapper">
    <!-- Page Title Section -->
    <div class="page-title-section">
        <div>
            <h1 class="page-title">Mes demandes</h1>
            <div class="page-subtitle">{{ $totalCount }} demandes au total</div>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('mes-demandes.history') }}" class="new-request-btn">
                <i class="fas fa-history"></i> Mon Historique
            </a>
            <a href="{{ route('mes-demandes.create') }}" class="new-request-btn">
                <i class="fas fa-plus"></i> Nouvelle demande
            </a>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <a href="{{ route('mes-demandes.index') }}" class="filter-tab {{ empty($statusFilter) || $statusFilter == 'all' ? 'active' : '' }}">Toutes ({{ $totalCount }})</a>
        <a href="{{ route('mes-demandes.index', ['status' => 'en_attente']) }}" class="filter-tab {{ ($statusFilter == 'en_attente') ? 'active' : '' }}">En attente ({{ $pendingCount }})</a>
        <a href="{{ route('mes-demandes.index', ['status' => 'approuvee']) }}" class="filter-tab {{ ($statusFilter == 'approuvee') ? 'active' : '' }}">Approuvées ({{ $approvedCount }})</a>
        <a href="{{ route('mes-demandes.index', ['status' => 'rejetee']) }}" class="filter-tab {{ ($statusFilter == 'rejetee') ? 'active' : '' }}">Rejetées ({{ $rejectedCount }})</a>
        <a href="{{ route('mes-demandes.index', ['status' => 'annulee']) }}" class="filter-tab {{ ($statusFilter == 'annulee') ? 'active' : '' }}">Annulées ({{ $cancelledCount }})</a>
    </div>

    <!-- Requests List -->
    <div class="requests-list" id="requestsContainer">
        @if($demandes && count($demandes) > 0)
            @foreach($demandes as $demande)
                <div class="request-item status-{{ $demande->status }}" data-status="{{ $demande->status }}">
                    <!-- Header with destination and status -->
                    <div class="request-item-header">
                        <div>
                            <div class="request-destination">{{ $demande->destination }}</div>
                            <div class="request-dates">
                                <i class="fas fa-calendar request-dates-icon"></i>
                                {{ optional($demande->start_date)->format('d M. Y') }} → {{ optional($demande->end_date)->format('d M. Y') }}
                                <span class="request-time">
                                    <i class="fas fa-clock"></i> {{ $demande->start_time ?? '08:00' }}
                                </span>
                            </div>
                        </div>
                        <div class="request-status-badges">
                            @if($demande->status == 'approved')
                                <span class="status-badge badge-approved"><i class="fas fa-check"></i> Approuvé</span>
                            @elseif($demande->status == 'pending')
                                <span class="status-badge badge-pending"><i class="fas fa-clock"></i> En attente</span>
                                <a href="#" class="action-link action-link-reject" onclick="cancelReservation({{ $demande->id }}, event)">Annuler</a>
                            @elseif($demande->status == 'rejected')
                                <span class="status-badge badge-rejected"><i class="fas fa-times"></i> Rejeté</span>
                            @elseif($demande->status == 'cancelled')
                                <span class="status-badge badge-cancelled"><i class="fas fa-ban"></i> Annulée</span>
                            @endif
                        </div>
                    </div>

                    <!-- Motif -->
                    <div class="request-motif">
                        <strong>Motif :</strong> {{ $demande->reason }}
                    </div>

                    <!-- Vehicle Info -->
                    <div class="request-vehicle-section">
                        <i class="fas fa-car"></i> {{ $demande->car_id ? ('Véhicule ID #' . $demande->car_id) : 'Véhicule à attribuer' }}
                    </div>

                    <!-- Rejection Reason (if rejected) -->
                    @if($demande->status == 'rejected')
                    <div class="request-rejection-reason">
                        <strong>MG :</strong> Usage non professionnel — refusé.
                    </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Aucune demande</h3>
                <p>Vous n'avez pas encore créé de demande de véhicule.</p>
            </div>
        @endif
    </div>
</div>

<script>
    // Server-side filtering applied via query param `status`.

    /**
     * Cancel a reservation with confirmation
     */
    function cancelReservation(demandeId, event) {
        event.preventDefault();

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            showToast('error', 'Token CSRF manquant. Veuillez rafraîchir la page.');
            return;
        }

        // Show confirmation dialog
        Swal.fire({
            title: 'Annuler la demande ?',
            text: 'Êtes-vous sûr de vouloir annuler cette demande de réservation ? Cette action ne peut pas être annulée.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d32f2f',
            cancelButtonColor: '#757575',
            confirmButtonText: 'Oui, annuler',
            cancelButtonText: 'Non, conserver',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Annulation en cours...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send cancellation request
                fetch(`/mes-demandes/${demandeId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({})
                })
                .then(response => {
                    console.log('Cancel response status:', response.status);
                    return response.json().then(data => ({
                        status: response.status,
                        body: data
                    }));
                })
                .then(({ status, body }) => {
                    console.log('Cancel response:', { status, body });

                    if (body.success) {
                        // Close loading dialog
                        Swal.close();

                        // Show success message
                        showToast('success', body.message || 'Demande annulée avec succès.');

                        // Reload page after 1.5 seconds
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        // Close loading dialog
                        Swal.close();

                        // Show error message
                        showToast('error', body.message || 'Erreur lors de l\'annulation.');
                    }
                })
                .catch(error => {
                    console.error('Cancel error:', error);
                    Swal.close();
                    showToast('error', 'Erreur réseau : ' + error.message);
                });
            }
        });
    }

    /**
     * Show toast notification (fallback if not available globally)
     */
    function showToast(type, message, duration = 3000) {
        if (typeof showToast !== 'undefined' && window.showToast !== showToast) {
            window.showToast(type, message, duration);
        } else {
            // Fallback to Swal alert
            Swal.fire({
                icon: type,
                title: type === 'success' ? 'Succès' : 'Erreur',
                text: message,
                timer: duration,
                timerProgressBar: true,
            });
        }
    }
</script>
@endsection
