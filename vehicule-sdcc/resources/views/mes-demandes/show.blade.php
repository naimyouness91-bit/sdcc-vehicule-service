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

    $statusBadge = [
        \App\Models\Demande::STATUS_PENDING => 'badge-pending',
        \App\Models\Demande::STATUS_APPROVED => 'badge-approved',
        \App\Models\Demande::STATUS_REJECTED => 'badge-rejected',
        \App\Models\Demande::STATUS_CANCELLED => 'badge-cancelled',
    ][$demande->status] ?? 'badge-pending';

    $statusIcon = [
        \App\Models\Demande::STATUS_PENDING => 'fas fa-clock',
        \App\Models\Demande::STATUS_APPROVED => 'fas fa-check',
        \App\Models\Demande::STATUS_REJECTED => 'fas fa-times',
        \App\Models\Demande::STATUS_CANCELLED => 'fas fa-ban',
    ][$demande->status] ?? 'fas fa-clock';

    $today = \Illuminate\Support\Carbon::today();
    $startDate = $demande->start_date;
    $endDate = $demande->end_date;
    $isApproved = $demande->status === \App\Models\Demande::STATUS_APPROVED;

    $validationState = 'current';
    $validationIcon = 'fas fa-clock';
    $validationMeta = 'En attente de validation';
    if ($demande->status === \App\Models\Demande::STATUS_APPROVED) {
        $validationState = 'done';
        $validationIcon = 'fas fa-check';
        $validationMeta = 'Demande approuvée';
    } elseif ($demande->status === \App\Models\Demande::STATUS_REJECTED) {
        $validationState = 'error';
        $validationIcon = 'fas fa-times';
        $validationMeta = 'Demande rejetée';
    } elseif ($demande->status === \App\Models\Demande::STATUS_CANCELLED) {
        $validationState = 'error';
        $validationIcon = 'fas fa-ban';
        $validationMeta = 'Demande annulée';
    }

    $usageState = 'blocked';
    $usageIcon = 'fas fa-route';
    $usageMeta = 'Non engagée';
    if ($isApproved && $startDate && $endDate) {
        if ($today->lt($startDate)) {
            $usageState = 'todo';
            $usageIcon = 'fas fa-play';
            $usageMeta = 'Planifiée le ' . $startDate->format('d/m/Y');
        } elseif ($today->between($startDate, $endDate)) {
            $usageState = 'current';
            $usageIcon = 'fas fa-road';
            $usageMeta = 'Période en cours';
        } else {
            $usageState = 'done';
            $usageIcon = 'fas fa-check';
            $usageMeta = 'Période terminée';
        }
    }

    $returnState = 'blocked';
    $returnIcon = 'fas fa-flag-checkered';
    $returnMeta = 'Non engagée';
    if ($isApproved && $endDate) {
        if ($today->gt($endDate)) {
            $returnState = 'done';
            $returnIcon = 'fas fa-check';
            $returnMeta = 'Restitution passée le ' . $endDate->format('d/m/Y');
        } else {
            $returnState = 'todo';
            $returnIcon = 'fas fa-flag-checkered';
            $returnMeta = 'Prévue le ' . $endDate->format('d/m/Y');
        }
    }
@endphp

@extends('layouts.app')
@section('title', 'Fiche de la demande ' . $reference)
@section('content')
<style>
    .fiche {
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
        background: var(--bg-surface, #fff);
        border: 1px solid var(--border-soft, #e5e7eb);
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(31, 41, 55, .06);
        overflow: hidden;
    }

    /* ==================== HEADER ==================== */
    .fiche-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 24px;
        padding: 26px 28px;
        border-bottom: 1px solid var(--border-soft, #e5e7eb);
        background: linear-gradient(180deg, #ffffff 0%, #fafcfb 100%);
    }

    .fiche-ref {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 5px 11px;
        border: 1px solid #c8e6c9;
        border-radius: 6px;
        background: #eef7ee;
        color: var(--brand-primary, #2e7d32);
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: .4px;
    }

    .fiche-title {
        margin: 12px 0 0;
        color: var(--text-strong, #1f2937);
        font-size: 25px;
        font-weight: 700;
        line-height: 1.25;
    }

    .fiche-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 18px;
        margin-top: 10px;
        color: var(--text-muted, #6b7280);
        font-size: 13.5px;
    }

    .fiche-meta span {
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .fiche-meta i {
        color: var(--brand-primary, #2e7d32);
    }

    .fiche-header-side {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 14px;
        flex-shrink: 0;
    }

    .fiche-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 17px;
        border-radius: 7px;
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition: background-color .18s ease, border-color .18s ease, color .18s ease, box-shadow .18s ease;
    }

    .btn-primary {
        border: 1px solid var(--brand-primary, #2e7d32);
        background: var(--brand-primary, #2e7d32);
        color: #fff;
        box-shadow: 0 2px 6px rgba(46, 125, 50, .22);
    }

    .btn-primary:hover {
        background: #256428;
        border-color: #256428;
        color: #fff;
    }

    .btn-secondary {
        border: 1px solid var(--border-soft, #e5e7eb);
        background: #fff;
        color: var(--text-strong, #1f2937);
    }

    .btn-secondary:hover {
        border-color: var(--brand-primary, #2e7d32);
        color: var(--brand-primary, #2e7d32);
        background: #f6faf6;
    }

    /* ==================== STATUS BADGE ==================== */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 14px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-approved {
        background: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }

    .badge-pending {
        background: #fff3e0;
        color: #e65100;
        border: 1px solid #ffe0b2;
    }

    .badge-rejected {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
    }

    .badge-cancelled {
        background: #f3e5f5;
        color: #6a1b9a;
        border: 1px solid #e1bee7;
    }

    /* ==================== BODY / SECTIONS ==================== */
    .fiche-body {
        padding: 26px 28px 8px;
    }

    .fiche-section + .fiche-section {
        margin-top: 30px;
    }

    .section-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
    }

    .section-head i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: #eef7ee;
        color: var(--brand-primary, #2e7d32);
        font-size: 13px;
    }

    .section-head h2 {
        margin: 0;
        color: var(--text-strong, #1f2937);
        font-size: 15.5px;
        font-weight: 700;
        letter-spacing: .2px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .trip-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .info-card {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        min-width: 0;
        padding: 15px 16px;
        border: 1px solid var(--border-soft, #e5e7eb);
        border-radius: 10px;
        background: #fff;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .info-card:hover {
        border-color: #cfe4d1;
        box-shadow: 0 3px 10px rgba(31, 41, 55, .05);
    }

    .info-card-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 34px;
        height: 34px;
        border-radius: 9px;
        background: #eef7ee;
        color: var(--brand-primary, #2e7d32);
        font-size: 14px;
    }

    .info-card-body {
        min-width: 0;
    }

    .info-card-label {
        display: block;
        margin-bottom: 4px;
        color: var(--text-muted, #6b7280);
        font-size: 11.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .info-card-value {
        color: var(--text-strong, #1f2937);
        font-size: 14.5px;
        font-weight: 600;
        line-height: 1.45;
        overflow-wrap: anywhere;
        white-space: pre-wrap;
    }

    .info-card--wide {
        grid-column: span 2;
    }

    /* ==================== TIMELINE ==================== */
    .timeline {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0;
        padding: 20px 18px;
        border: 1px solid var(--border-soft, #e5e7eb);
        border-radius: 10px;
        background: #fafcfb;
    }

    .timeline-step {
        position: relative;
        padding: 0 14px;
        min-width: 0;
    }

    .timeline-step::before {
        content: "";
        position: absolute;
        top: 17px;
        left: 0;
        right: 50%;
        height: 2px;
        background: #e2e6e4;
    }

    .timeline-step::after {
        content: "";
        position: absolute;
        top: 17px;
        left: 50%;
        right: 0;
        height: 2px;
        background: #e2e6e4;
    }

    .timeline-step:first-child::before,
    .timeline-step:first-child::after,
    .timeline-step:last-child::after {
        display: none;
    }

    .timeline-step.is-done::before,
    .timeline-step.is-done::after,
    .timeline-step.is-done + .timeline-step::before {
        background: var(--brand-primary, #2e7d32);
    }

    .timeline-dot {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        margin: 0 auto;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #d6dbd9;
        color: #9aa3a0;
        font-size: 13px;
    }

    .timeline-step.is-done .timeline-dot {
        background: var(--brand-primary, #2e7d32);
        border-color: var(--brand-primary, #2e7d32);
        color: #fff;
    }

    .timeline-step.is-current .timeline-dot {
        background: var(--brand-accent, #ffa726);
        border-color: var(--brand-accent, #ffa726);
        color: #fff;
        box-shadow: 0 0 0 4px rgba(255, 167, 38, .22);
    }

    .timeline-step.is-error .timeline-dot {
        background: var(--brand-danger, #e53935);
        border-color: var(--brand-danger, #e53935);
        color: #fff;
    }

    .timeline-step.is-blocked .timeline-dot {
        background: #f1f3f2;
        border-color: #e2e6e4;
        color: #9aa3a0;
    }

    .timeline-label {
        display: block;
        margin-top: 12px;
        text-align: center;
        color: var(--text-strong, #1f2937);
        font-size: 13.5px;
        font-weight: 700;
    }

    .timeline-meta {
        display: block;
        margin-top: 4px;
        text-align: center;
        color: var(--text-muted, #6b7280);
        font-size: 12px;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    /* ==================== FOOTER ACTIONS ==================== */
    .fiche-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 26px;
        padding: 18px 28px;
        border-top: 1px solid var(--border-soft, #e5e7eb);
        background: #fafcfb;
    }

    .fiche-footer-note {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted, #6b7280);
        font-size: 12.5px;
    }

    .fiche-footer-note i {
        color: var(--brand-primary, #2e7d32);
    }

    .fiche-footer-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-left: auto;
    }

    /* ==================== RESPONSIVE ==================== */
    @media (max-width: 1024px) {
        .info-grid,
        .trip-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .fiche-header {
            flex-direction: column;
            align-items: stretch;
            padding: 20px;
        }

        .fiche-header-side {
            align-items: flex-start;
        }

        .fiche-actions {
            justify-content: flex-start;
        }

        .fiche-body {
            padding: 20px 20px 4px;
        }

        .fiche-title {
            font-size: 21px;
        }

        .timeline {
            grid-template-columns: 1fr;
            gap: 0;
            padding: 16px;
        }

        .timeline-step {
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr);
            column-gap: 14px;
            align-items: start;
            padding: 0 0 20px;
        }

        .timeline-step:last-child {
            padding-bottom: 0;
        }

        .timeline-step::before {
            top: 0;
            bottom: 50%;
            left: 16px;
            right: auto;
            width: 2px;
            height: auto;
        }

        .timeline-step::after {
            top: 50%;
            bottom: -2px;
            left: 16px;
            right: auto;
            width: 2px;
            height: auto;
        }

        .timeline-step:first-child::before {
            display: none;
        }

        .timeline-step.is-done::after {
            background: var(--brand-primary, #2e7d32);
        }

        .timeline-dot {
            margin: 0;
        }

        .timeline-label,
        .timeline-meta {
            margin-top: 0;
            text-align: left;
        }

        .timeline-meta {
            margin-top: 3px;
        }

        .fiche-footer {
            padding: 16px 20px;
        }
    }

    @media (max-width: 560px) {
        .info-grid,
        .trip-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .info-card--wide {
            grid-column: auto;
        }

        .fiche-actions,
        .fiche-footer-actions {
            width: 100%;
        }

        .fiche-actions .btn,
        .fiche-footer-actions .btn {
            flex: 1 1 100%;
        }

        .fiche-footer-note {
            display: none;
        }

        .fiche-footer-actions {
            margin-left: 0;
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

    <section class="fiche" aria-labelledby="demande-heading">
        <header class="fiche-header">
            <div>
                <span class="fiche-ref"><i class="fas fa-file-alt"></i> {{ $reference }}</span>
                <h1 class="fiche-title" id="demande-heading">Fiche de la demande</h1>
                <div class="fiche-meta">
                    @if ($demande->created_at)
                        <span><i class="fas fa-calendar-plus"></i> Créée le {{ $demande->created_at->format('d/m/Y à H:i') }}</span>
                    @endif
                    @if ($demande->destination)
                        <span><i class="fas fa-map-marker-alt"></i> {{ $demande->destination }}</span>
                    @endif
                </div>
            </div>

            <div class="fiche-header-side">
                <span class="status-badge {{ $statusBadge }}"><i class="{{ $statusIcon }}"></i> {{ $statusLabel }}</span>
                <div class="fiche-actions">
                    <a href="{{ route('mes-demandes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à mes demandes
                    </a>
                    <a href="{{ route('demandes.print', ['id' => $demande->id]) }}"
                       target="_blank" rel="noopener"
                       class="btn btn-primary">
                        <i class="fas fa-print"></i> Imprimer
                    </a>
                </div>
            </div>
        </header>

        <div class="fiche-body">
            <section class="fiche-section" aria-labelledby="section-general">
                <div class="section-head">
                    <i class="fas fa-user"></i>
                    <h2 id="section-general">Informations générales</h2>
                </div>

                <div class="info-grid">
                    <div class="info-card">
                        <span class="info-card-icon"><i class="fas fa-id-badge"></i></span>
                        <div class="info-card-body">
                            <span class="info-card-label">Demandeur</span>
                            <div class="info-card-value">{{ $demande->user?->name ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="info-card">
                        <span class="info-card-icon"><i class="fas fa-envelope"></i></span>
                        <div class="info-card-body">
                            <span class="info-card-label">E-mail</span>
                            <div class="info-card-value">{{ $demande->user?->email ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="info-card">
                        <span class="info-card-icon"><i class="fas fa-building"></i></span>
                        <div class="info-card-body">
                            <span class="info-card-label">Service</span>
                            <div class="info-card-value">{{ $demande->user?->service ?: '—' }}</div>
                        </div>
                    </div>

                    <div class="info-card">
                        <span class="info-card-icon"><i class="fas fa-car"></i></span>
                        <div class="info-card-body">
                            <span class="info-card-label">Véhicule</span>
                            <div class="info-card-value">
                                {{ $demande->car?->name ?? '—' }}
                                @if ($demande->car?->model)
                                    — {{ $demande->car->model }}
                                @endif
                                @if ($demande->car?->matricule)
                                    ({{ $demande->car->matricule }})
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="fiche-section" aria-labelledby="section-deplacement">
                <div class="section-head">
                    <i class="fas fa-route"></i>
                    <h2 id="section-deplacement">Détails du déplacement</h2>
                </div>

                <div class="trip-grid">
                    <div class="info-card">
                        <span class="info-card-icon"><i class="fas fa-calendar-day"></i></span>
                        <div class="info-card-body">
                            <span class="info-card-label">Date de départ</span>
                            <div class="info-card-value">{{ $date($demande->start_date) }}</div>
                        </div>
                    </div>

                    <div class="info-card">
                        <span class="info-card-icon"><i class="fas fa-clock"></i></span>
                        <div class="info-card-body">
                            <span class="info-card-label">Heure de départ</span>
                            <div class="info-card-value">{{ $time($demande->start_time) }}</div>
                        </div>
                    </div>

                    <div class="info-card">
                        <span class="info-card-icon"><i class="fas fa-calendar-check"></i></span>
                        <div class="info-card-body">
                            <span class="info-card-label">Date de restitution</span>
                            <div class="info-card-value">{{ $date($demande->end_date) }}</div>
                        </div>
                    </div>

                    <div class="info-card">
                        <span class="info-card-icon"><i class="fas fa-hourglass-half"></i></span>
                        <div class="info-card-body">
                            <span class="info-card-label">Heure de restitution</span>
                            <div class="info-card-value">{{ $time($demande->end_time) }}</div>
                        </div>
                    </div>

                    <div class="info-card">
                        <span class="info-card-icon"><i class="fas fa-flag-checkered"></i></span>
                        <div class="info-card-body">
                            <span class="info-card-label">Heure de retour prévue</span>
                            <div class="info-card-value">{{ $time($demande->return_time) }}</div>
                        </div>
                    </div>

                    <div class="info-card">
                        <span class="info-card-icon"><i class="fas fa-tachometer-alt"></i></span>
                        <div class="info-card-body">
                            <span class="info-card-label">Kilométrage prévu</span>
                            <div class="info-card-value">
                                {{ $demande->kilometers !== null ? number_format($demande->kilometers, 0, ',', ' ') . ' km' : '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="info-card info-card--wide">
                        <span class="info-card-icon"><i class="fas fa-map-marker-alt"></i></span>
                        <div class="info-card-body">
                            <span class="info-card-label">Destination</span>
                            <div class="info-card-value">{{ $demande->destination ?: '—' }}</div>
                        </div>
                    </div>

                    <div class="info-card info-card--wide">
                        <span class="info-card-icon"><i class="fas fa-comment-dots"></i></span>
                        <div class="info-card-body">
                            <span class="info-card-label">Motif</span>
                            <div class="info-card-value">{{ $demande->reason ?: '—' }}</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="fiche-section" aria-labelledby="section-historique">
                <div class="section-head">
                    <i class="fas fa-history"></i>
                    <h2 id="section-historique">Historique de la demande</h2>
                </div>

                <div class="timeline">
                    <div class="timeline-step is-done">
                        <span class="timeline-dot"><i class="fas fa-check"></i></span>
                        <span class="timeline-label">Création</span>
                        <span class="timeline-meta">
                            @if ($demande->created_at)
                                {{ $demande->created_at->format('d/m/Y') }}
                            @else
                                —
                            @endif
                        </span>
                    </div>

                    <div class="timeline-step is-{{ $validationState }}">
                        <span class="timeline-dot"><i class="{{ $validationIcon }}"></i></span>
                        <span class="timeline-label">Validation</span>
                        <span class="timeline-meta">{{ $validationMeta }}</span>
                    </div>

                    <div class="timeline-step is-{{ $usageState }}">
                        <span class="timeline-dot"><i class="{{ $usageIcon }}"></i></span>
                        <span class="timeline-label">Utilisation</span>
                        <span class="timeline-meta">{{ $usageMeta }}</span>
                    </div>

                    <div class="timeline-step is-{{ $returnState }}">
                        <span class="timeline-dot"><i class="{{ $returnIcon }}"></i></span>
                        <span class="timeline-label">Retour</span>
                        <span class="timeline-meta">{{ $returnMeta }}</span>
                    </div>
                </div>
            </section>
        </div>

        <footer class="fiche-footer">
            <span class="fiche-footer-note">
                <i class="fas fa-shield-alt"></i> Demande {{ $statusLabel }} — réf. {{ $reference }}
            </span>
            <div class="fiche-footer-actions">
                <a href="{{ route('mes-demandes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à mes demandes
                </a>
                <a href="{{ route('demandes.print', ['id' => $demande->id]) }}"
                   target="_blank" rel="noopener"
                   class="btn btn-primary">
                    <i class="fas fa-print"></i> Imprimer
                </a>
            </div>
        </footer>
    </section>
</div>
@endsection
