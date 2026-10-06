@php
    $reference = 'DEM-' . str_pad((string) $demande->id, 5, '0', STR_PAD_LEFT);
    $statusLabels = [
        \App\Models\Demande::STATUS_PENDING => 'En attente',
        \App\Models\Demande::STATUS_APPROVED => 'Approuvée',
        \App\Models\Demande::STATUS_REJECTED => 'Rejetée',
        \App\Models\Demande::STATUS_CANCELLED => 'Annulée',
    ];
    $statusLabel = $statusLabels[$demande->status] ?? ucfirst((string) $demande->status);
    $time = static fn ($value) => $value ? substr((string) $value, 0, 5) : '—';
    $date = static fn ($value) => $value ? $value->format('d/m/Y') : '—';
@endphp

@extends('layouts.app')
@section('title', 'Fiche de la demande ' . $reference)
@section('content')
<style>
    .demande-sheet {
        max-width: 1000px;
        margin: 0 auto;
        padding: 24px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
    }

    .demande-sheet-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .demande-reference {
        margin: 0;
        color: #2e7d32;
        font-size: 14px;
        font-weight: 700;
    }

    .demande-heading {
        margin: 5px 0 0;
        color: #222;
        font-size: 24px;
    }

    .demande-status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        background: #f3f4f6;
        color: #374151;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
    }

    .demande-details {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .demande-detail {
        min-width: 0;
        padding: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 7px;
    }

    .demande-detail--wide {
        grid-column: 1 / -1;
    }

    .demande-detail-label {
        display: block;
        margin-bottom: 5px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
    }

    .demande-detail-value {
        color: #222;
        font-size: 14px;
        overflow-wrap: anywhere;
        white-space: pre-wrap;
    }

    .demande-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 22px;
    }

    .demande-print-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border: 0;
        border-radius: 5px;
        background: #2e7d32;
        color: #fff;
        font-weight: 600;
        text-decoration: none;
    }

    .demande-print-button:hover {
        background: #256428;
        color: #fff;
    }

    @media (max-width: 640px) {
        .demande-sheet {
            padding: 16px;
        }

        .demande-sheet-header {
            flex-direction: column;
            gap: 12px;
        }

        .demande-details {
            grid-template-columns: 1fr;
        }

        .demande-detail--wide {
            grid-column: auto;
        }
    }
</style>

<div class="main-content">
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Tableau de bord</a>
        <span> &gt; </span>
        <a href="{{ route('mes-demandes.index') }}">Mes demandes</a>
        <span> &gt; </span>
        <span>{{ $reference }}</span>
    </div>

    <a href="{{ route('mes-demandes.index') }}" class="back-btn">
        <i class="fas fa-chevron-left"></i> Retour à mes demandes
    </a>

    <section class="demande-sheet" aria-labelledby="demande-heading">
        <header class="demande-sheet-header">
            <div>
                <p class="demande-reference">{{ $reference }}</p>
                <h1 class="demande-heading" id="demande-heading">Fiche de la demande</h1>
                @if ($demande->created_at)
                    <p>Créée le {{ $demande->created_at->format('d/m/Y à H:i') }}</p>
                @endif
            </div>
            <span class="demande-status">{{ $statusLabel }}</span>
        </header>

        <div class="demande-details">
            <div class="demande-detail">
                <span class="demande-detail-label">Demandeur</span>
                <div class="demande-detail-value">{{ $demande->user?->name ?? '—' }}</div>
            </div>
            <div class="demande-detail">
                <span class="demande-detail-label">E-mail</span>
                <div class="demande-detail-value">{{ $demande->user?->email ?? '—' }}</div>
            </div>
            <div class="demande-detail">
                <span class="demande-detail-label">Service</span>
                <div class="demande-detail-value">{{ $demande->user?->service ?: '—' }}</div>
            </div>
            <div class="demande-detail">
                <span class="demande-detail-label">Véhicule</span>
                <div class="demande-detail-value">
                    {{ $demande->car?->name ?? '—' }}
                    @if ($demande->car?->model)
                        — {{ $demande->car->model }}
                    @endif
                    @if ($demande->car?->matricule)
                        ({{ $demande->car->matricule }})
                    @endif
                </div>
            </div>
            <div class="demande-detail">
                <span class="demande-detail-label">Date de départ</span>
                <div class="demande-detail-value">{{ $date($demande->start_date) }}</div>
            </div>
            <div class="demande-detail">
                <span class="demande-detail-label">Heure de départ</span>
                <div class="demande-detail-value">{{ $time($demande->start_time) }}</div>
            </div>
            <div class="demande-detail">
                <span class="demande-detail-label">Date de restitution</span>
                <div class="demande-detail-value">{{ $date($demande->end_date) }}</div>
            </div>
            <div class="demande-detail">
                <span class="demande-detail-label">Heure de restitution</span>
                <div class="demande-detail-value">{{ $time($demande->end_time) }}</div>
            </div>
            <div class="demande-detail">
                <span class="demande-detail-label">Heure de retour prévue</span>
                <div class="demande-detail-value">{{ $time($demande->return_time) }}</div>
            </div>
            <div class="demande-detail">
                <span class="demande-detail-label">Kilométrage prévu</span>
                <div class="demande-detail-value">
                    {{ $demande->kilometers !== null ? number_format($demande->kilometers, 0, ',', ' ') . ' km' : '—' }}
                </div>
            </div>
            <div class="demande-detail demande-detail--wide">
                <span class="demande-detail-label">Destination</span>
                <div class="demande-detail-value">{{ $demande->destination ?: '—' }}</div>
            </div>
            <div class="demande-detail demande-detail--wide">
                <span class="demande-detail-label">Motif</span>
                <div class="demande-detail-value">{{ $demande->reason ?: '—' }}</div>
            </div>
        </div>

        <div class="demande-actions">
            <a href="{{ route('demandes.print', ['id' => $demande->id]) }}"
               target="_blank" rel="noopener"
               class="demande-print-button">
                <i class="fas fa-print"></i> Imprimer
            </a>
        </div>
    </section>
</div>
@endsection
