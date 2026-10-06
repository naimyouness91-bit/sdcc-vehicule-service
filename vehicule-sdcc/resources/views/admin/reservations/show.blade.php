@extends('layouts.app')
@section('title', 'Réservation #{{ $reservation->id }} — Détails')
@section('content')

<style>
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #4CAF50;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    .back-link:hover { color: #2E7D32; transform: translateX(-4px); }

    .detail-card {
        background: white;
        border-radius: 10px;
        padding: 32px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        max-width: 860px;
        margin: 0 auto;
    }

    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 28px;
        padding-bottom: 20px;
        border-bottom: 2px solid transparent;
        border-image: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%) 1;
    }

    .detail-title { font-size: 22px; font-weight: 700; color: #111; margin: 0; }
    .detail-subtitle { font-size: 13px; color: #888; margin: 4px 0 0 0; }

    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .status-badge.pending   { background:#fff3e0; color:#f57c00; }
    .status-badge.approved  { background:#e8f5e9; color:#2e7d32; }
    .status-badge.cancelled { background:#ffebee; color:#c62828; }
    .status-badge.rejected  { background:#fce4ec; color:#ad1457; }

    .detail-section { margin-bottom: 26px; }

    .detail-section-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #4CAF50;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
    }

    .detail-item { display: flex; flex-direction: column; gap: 4px; }
    .detail-label { font-size: 11px; font-weight: 600; color: #999; text-transform: uppercase; }
    .detail-value { font-size: 14px; color: #222; font-weight: 500; }

    .license-plate {
        background: #fff8e1;
        border: 1px solid #ffe082;
        padding: 3px 8px;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 13px;
        display: inline-block;
    }

    .detail-actions {
        display: flex;
        gap: 12px;
        margin-top: 30px;
        padding-top: 22px;
        border-top: 1px solid #f0f0f0;
    }

    .btn {
        padding: 10px 22px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .btn-edit {
        background: linear-gradient(135deg, #FFA726 0%, #e65100 100%);
        color: white;
    }
    .btn-edit:hover { opacity: 0.88; transform: translateY(-1px); }

    .btn-secondary {
        background: white;
        color: #555;
        border: 1.5px solid #ddd;
    }
    .btn-secondary:hover { background: #f5f5f5; }

    .btn-danger {
        background: #ffebee;
        color: #c62828;
        border: 1.5px solid #ef9a9a;
    }
    .btn-danger:hover { background: #e53935; color: white; border-color: #e53935; }

    .btn-print {
        background: white;
        color: #2e7d32;
        border: 1.5px solid #2e7d32;
    }
    .btn-print:hover { background: #2e7d32; color: white; }

    @media (max-width: 640px) {
        .detail-grid { grid-template-columns: 1fr; }
        .detail-header { flex-direction: column; gap: 12px; }
    }
</style>

<div style="padding: 30px;">
    <a href="{{ route('admin.reservations.index') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Retour aux réservations
    </a>

    <div class="detail-card">
        <div class="detail-header">
            <div>
                <h1 class="detail-title">Réservation #{{ $reservation->id }}</h1>
                <p class="detail-subtitle">
                    Créée le {{ $reservation->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>
            <span class="status-badge {{ $reservation->status }}">
                @if($reservation->status === 'pending')
                    <i class="fas fa-hourglass-half"></i> En attente
                @elseif($reservation->status === 'approved')
                    <i class="fas fa-check-circle"></i> Approuvée
                @elseif($reservation->status === 'cancelled')
                    <i class="fas fa-times-circle"></i> Annulée
                @elseif($reservation->status === 'rejected')
                    <i class="fas fa-ban"></i> Rejetée
                @endif
            </span>
        </div>

        {{-- Employee --}}
        <div class="detail-section">
            <div class="detail-section-title">
                <i class="fas fa-user"></i> Employé
            </div>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Nom complet</div>
                    <div class="detail-value">{{ $reservation->user?->name ?? '—' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div class="detail-value">{{ $reservation->user?->email ?? '—' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Service</div>
                    <div class="detail-value">{{ $reservation->user?->service ?? '—' }}</div>
                </div>
            </div>
        </div>

        {{-- Vehicle --}}
        <div class="detail-section">
            <div class="detail-section-title">
                <i class="fas fa-car"></i> Véhicule
            </div>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Nom</div>
                    <div class="detail-value">{{ $reservation->car?->name ?? '—' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Immatriculation</div>
                    <div class="detail-value">
                        @if($reservation->car?->matricule)
                            <span class="license-plate">{{ $reservation->car?->matricule ?? 'N/A' }}</span>
                        @else
                            —
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Trip info --}}
        <div class="detail-section">
            <div class="detail-section-title">
                <i class="fas fa-calendar"></i> Période et Destination
            </div>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Date de départ</div>
                    <div class="detail-value">{{ $reservation->start_date->format('d/m/Y') }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Heure de départ</div>
                    <div class="detail-value">{{ $reservation->start_time ?? '—' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Date de retour</div>
                    <div class="detail-value">{{ $reservation->end_date->format('d/m/Y') }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Heure de retour</div>
                    <div class="detail-value">{{ $reservation->end_time ?? '—' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Destination</div>
                    <div class="detail-value">{{ $reservation->destination ?? '—' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Kilométrage prévu</div>
                    <div class="detail-value">
                        {{ $reservation->kilometers ? number_format($reservation->kilometers, 0, ',', ' ') . ' km' : '—' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Reason --}}
        @if($reservation->reason)
        <div class="detail-section">
            <div class="detail-section-title">
                <i class="fas fa-sticky-note"></i> Motif
            </div>
            <p style="font-size:14px; color:#333; margin:0; line-height:1.6;">
                {{ $reservation->reason }}
            </p>
        </div>
        @endif

        {{-- Actions --}}
        <div class="detail-actions">
            <a href="{{ route('admin.reservations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="btn btn-edit">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('demandes.print', ['id' => $reservation->id]) }}"
               target="_blank" rel="noopener" class="btn btn-print">
                <i class="fas fa-print"></i> Imprimer
            </a>
            <button class="btn btn-danger" id="deleteBtn"
                    data-id="{{ $reservation->id }}"
                    data-destination="{{ $reservation->destination }}">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>
    </div>
</div>

<script>
document.getElementById('deleteBtn')?.addEventListener('click', function () {
    const id          = this.dataset.id;
    const destination = this.dataset.destination;
    const csrf        = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const doDelete = async () => {
        const res  = await fetch(`/admin/reservations/${id}`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.success) {
            window.location.href = '{{ route("admin.reservations.index") }}';
        } else {
            alert(data.message || 'Erreur lors de la suppression.');
        }
    };

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'warning',
            title: 'Supprimer cette réservation ?',
            html: `<p style="color:#c62828;font-weight:600;">
                       <i class="fas fa-exclamation-triangle"></i>
                       Cette action est irréversible.
                   </p>`,
            confirmButtonText: '<i class="fas fa-trash"></i> Supprimer',
            cancelButtonText:  'Annuler',
            confirmButtonColor: '#c62828',
            cancelButtonColor:  '#757575',
            showCancelButton:   true,
            focusCancel:        true,
        }).then(r => r.isConfirmed && doDelete());
    } else {
        if (confirm('Supprimer cette réservation définitivement ?')) doDelete();
    }
});
</script>

@endsection
