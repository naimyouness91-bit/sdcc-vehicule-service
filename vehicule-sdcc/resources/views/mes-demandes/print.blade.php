@php
    // All values below come from real columns of `demandes`, `users`, `cars`.
    // Nothing is fabricated: missing values render as an em dash.
    $u = $demande->user;
    $c = $demande->car;

    $reference = 'DEM-' . str_pad((string) $demande->id, 5, '0', STR_PAD_LEFT);

    $roleLabels = [
        'super_admin' => 'Super Administrateur',
        'admin'       => 'Administrateur',
        'employee'    => 'Employé',
    ];
    $roleKey   = $u?->primaryRole();
    $roleLabel = $roleLabels[$roleKey] ?? ucfirst(str_replace('_', ' ', (string) $roleKey));

    $statusLabels = [
        'pending'   => 'En attente',
        'approved'  => 'Approuvée',
        'rejected'  => 'Rejetée',
        'cancelled' => 'Annulée',
    ];
    $statusLabel = $statusLabels[$demande->status] ?? ucfirst((string) $demande->status);

    $hm = static function ($value): ?string {
        if ($value === null || $value === '') {
            return null;
        }
        return substr((string) $value, 0, 5);
    };

    $dash = "\u{2014}";
    $d    = static fn($v): ?string => $v ? $v->format('d/m/Y') : null;
    $dt   = static fn($v): ?string => $v ? $v->format('d/m/Y à H:i') : null;

    // The product name is read from APP_NAME in .env through config/app.php, so
    // the sheet always follows the application's single source of truth.
    // The subtitle mirrors the navbar brand used by layouts/app.blade.php.
    $systemName = config('app.name');
    $serviceName = 'Véhicule de Service';
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $reference }} — Fiche de demande de véhicule</title>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/all.min.css') }}">
    <style>
        :root {
            --sdcc-primary: #2e7d32;
            --sdcc-accent:  #ffa726;
            --ink:          #000;
            --line:         #000;
            --soft-line:    #c9c9c9;
            --muted:        #555;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            background: #ececec;
            color: var(--ink);
            font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 12px;
            line-height: 1.45;
            -webkit-font-smoothing: antialiased;
        }

        /* ---------------- Screen toolbar (never printed) ---------------- */
        .print-toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 20px;
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
        }
        .print-toolbar .tb-hint {
            margin-left: auto;
            font-size: 12px;
            color: var(--muted);
        }
        .tb-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            background: var(--sdcc-primary);
            color: #fff;
        }
        .tb-btn:hover { background: #256428; }
        .tb-btn.ghost {
            background: #fff;
            color: #444;
            border: 1.5px solid #d5d5d5;
        }
        .tb-btn.ghost:hover { background: #f4f4f4; }

        /* ---------------- Sheet ---------------- */
        .sheet {
            width: 186mm;
            margin: 20px auto;
            padding: 0;
            background: #fff;
            box-shadow: 0 2px 14px rgba(0, 0, 0, .14);
        }

        /* ---------------- Header ---------------- */
        .sheet-header {
            display: flex;
            align-items: stretch;
            gap: 0;
            border: 1px solid var(--line);
            border-bottom: 2px solid var(--line);
        }
        .sheet-logo {
            flex: 0 0 auto;
            width: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
            border-right: 1px solid var(--soft-line);
        }
        .sheet-logo img { max-width: 100%; max-height: 44px; display: block; }
        .sheet-org {
            flex: 1 1 auto;
            padding: 8px 12px;
            border-right: 1px solid var(--soft-line);
        }
        .sheet-org-name {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.1px;
            text-transform: uppercase;
            color: var(--sdcc-primary);
        }
        .sheet-org-sub { font-size: 10px; color: var(--muted); }
        .sheet-title {
            margin-top: 4px;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: .7px;
            text-transform: uppercase;
        }
        .sheet-ref {
            flex: 0 0 auto;
            width: 132px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 8px 10px;
            text-align: center;
        }
        .sheet-ref-label {
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: .9px;
            color: var(--muted);
        }
        .sheet-ref-value {
            font-size: 17px;
            font-weight: 700;
            font-family: Consolas, "Courier New", monospace;
        }

        /* ---------------- Sections ---------------- */
        .section { margin-top: 12px; break-inside: avoid; page-break-inside: avoid; }

        .section-title {
            display: flex;
            align-items: center;
            gap: 7px;
            margin: 0;
            padding: 5px 9px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: #fff;
            background: #000;
        }
        .section-title .num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 15px;
            height: 15px;
            flex: 0 0 15px;
            border: 1px solid #fff;
            font-size: 9px;
            line-height: 1;
        }

        table.grid {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.grid th,
        table.grid td {
            border: 1px solid var(--line);
            padding: 5px 8px;
            vertical-align: top;
            word-wrap: break-word;
            font-size: 11.5px;
        }
        table.grid td.k {
            width: 32%;
            background: #f0f0f0;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
            color: #1a1a1a;
        }
        table.grid td.v.empty { color: #8a8a8a; font-style: italic; }

        .reason-box {
            border: 1px solid var(--line);
            padding: 9px 10px;
            font-size: 11.5px;
            white-space: pre-line;
            min-height: 34px;
        }

        .plate {
            display: inline-block;
            font-family: Consolas, "Courier New", monospace;
            font-weight: 700;
            letter-spacing: 1px;
            border: 1px solid var(--line);
            padding: 1px 8px;
        }

        .status-row {
            display: flex;
            align-items: stretch;
            border: 1px solid var(--line);
        }
        .status-row > div {
            flex: 1 1 0;
            padding: 8px 10px;
        }
        .status-row > div + div { border-left: 1px solid var(--line); }
        .status-row .lbl {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--muted);
        }
        .status-row .val { font-size: 14px; font-weight: 700; text-transform: uppercase; }
        .status-row .val.small { font-size: 11.5px; text-transform: none; font-weight: 600; }

        /* ---------------- Footer ---------------- */
        .sheet-footer {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 14px;
            padding-top: 6px;
            border-top: 1px solid var(--line);
            font-size: 9px;
            color: var(--muted);
        }

        /* =========================================================
           PRINT — only the sheet reaches the paper
           ========================================================= */
        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        @media print {
            html, body {
                background: #fff !important;
                color: #000 !important;
                font-size: 11px;
            }

            /* Hide every piece of application chrome */
            .no-print,
            .print-toolbar,
            .top-navbar,
            .navbar,
            .sidebar,
            .sidebar-overlay,
            .main-content,
            .content-wrapper,
            .admin-data-page,
            .breadcrumb,
            .back-btn,
            .back-link,
            .btn,
            .btn-print,
            form,
            input,
            select,
            textarea,
            .form-container,
            .form-section,
            .form-row,
            .form-group,
            .form-actions,
            .alert,
            .alert-success,
            .alert-danger,
            .alert-warning,
            .alert-info,
            .toast,
            .swal2-container,
            .dropdown-menu,
            .notifications-dropdown,
            .profile-dropdown,
            .modal,
            .pagination,
            table thead,
            nav,
            footer.app-footer {
                display: none !important;
            }

            .sheet {
                width: auto !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                background: #fff !important;
            }

            /* Keep label shading solid so it survives black & white printing */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            table.grid th,
            table.grid td { border-color: #000 !important; }

            table.grid td.k { background: #ededed !important; color: #000 !important; }

            .section-title { background: #000 !important; color: #fff !important; }

            .section,
            .status-row,
            .reason-box,
            tr { break-inside: avoid; page-break-inside: avoid; }

            a[href]:after { content: none !important; }
        }
    </style>
</head>
<body>

<div class="print-toolbar no-print">
    <a href="{{ route('mes-demandes.index') }}" class="tb-btn ghost">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
    <button type="button" class="tb-btn" onclick="window.print()">
        <i class="fas fa-print"></i> Imprimer
    </button>
    <span class="tb-hint">Fiche A4 — aperçu avant impression</span>
</div>

<div class="sheet">

    {{-- ============================== HEADER ============================== --}}
    <header class="sheet-header">
        <div class="sheet-logo">
            <img src="{{ asset('images/logo-sdcc-2.png') }}" alt="SDCC">
        </div>
        <div class="sheet-org">
            <div class="sheet-org-name">{{ $systemName }}</div>
            <div class="sheet-org-sub">{{ $serviceName }}</div>
            <div class="sheet-title">Fiche de demande de véhicule</div>
        </div>
        <div class="sheet-ref">
            <div class="sheet-ref-label">Référence</div>
            <div class="sheet-ref-value">{{ $reference }}</div>
        </div>
    </header>

    {{-- ===================== 1. INFORMATIONS DE LA DEMANDE ===================== --}}
    <section class="section">
        <h2 class="section-title"><span class="num">1</span> Informations de la demande</h2>
        <table class="grid">
            <colgroup><col style="width:32%"><col></colgroup>
            <tr>
                <td class="k">Référence</td>
                <td class="v">{{ $reference }}</td>
            </tr>
            <tr>
                <td class="k">Date de création</td>
                <td class="v {{ $demande->created_at ? '' : 'empty' }}">{{ $dt($demande->created_at) ?: $dash }}</td>
            </tr>
        </table>
    </section>

    {{-- ============================ 2. DEMANDEUR ============================ --}}
    <section class="section">
        <h2 class="section-title"><span class="num">2</span> Demandeur</h2>
        <table class="grid">
            <colgroup><col style="width:32%"><col></colgroup>
            <tr>
                <td class="k">Nom</td>
                <td class="v {{ $u?->name ? '' : 'empty' }}">{{ $u?->name ?: $dash }}</td>
            </tr>
            <tr>
                <td class="k">E-mail</td>
                <td class="v {{ $u?->email ? '' : 'empty' }}">{{ $u?->email ?: $dash }}</td>
            </tr>
            <tr>
                <td class="k">Fonction</td>
                <td class="v">{{ $roleLabel ?: $dash }}</td>
            </tr>
            <tr>
                <td class="k">Service</td>
                <td class="v {{ $u?->service ? '' : 'empty' }}">{{ $u?->service ?: $dash }}</td>
            </tr>
            <tr>
                <td class="k">Équipe</td>
                <td class="v {{ $u?->team ? '' : 'empty' }}">{{ $u?->team ?: $dash }}</td>
            </tr>
        </table>
    </section>

    {{-- ============================= 3. VÉHICULE ============================= --}}
    <section class="section">
        <h2 class="section-title"><span class="num">3</span> Véhicule</h2>
        <table class="grid">
            <colgroup><col style="width:32%"><col></colgroup>
            <tr>
                <td class="k">Véhicule</td>
                <td class="v {{ $c?->name ? '' : 'empty' }}">{{ $c?->name ?: $dash }}</td>
            </tr>
            <tr>
                <td class="k">Modèle</td>
                <td class="v {{ $c?->model ? '' : 'empty' }}">{{ $c?->model ?: $dash }}</td>
            </tr>
            <tr>
                <td class="k">Immatriculation</td>
                <td class="v {{ $c?->matricule ? '' : 'empty' }}">
                    @if($c?->matricule)
                        <span class="plate">{{ $c->matricule }}</span>
                    @else
                        <span class="empty">{{ $dash }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="k">Année</td>
                <td class="v {{ $c?->year ? '' : 'empty' }}">{{ $c?->year ?: $dash }}</td>
            </tr>
        </table>
    </section>

    {{-- ============================== 4. PÉRIODE ============================== --}}
    <section class="section">
        <h2 class="section-title"><span class="num">4</span> Période</h2>
        <table class="grid">
            <colgroup><col style="width:32%"><col></colgroup>
            <tr>
                <td class="k">Date de départ</td>
                <td class="v {{ $demande->start_date ? '' : 'empty' }}">{{ $d($demande->start_date) ?: $dash }}</td>
            </tr>
            <tr>
                <td class="k">Heure de départ</td>
                <td class="v {{ $hm($demande->start_time) ? '' : 'empty' }}">{{ $hm($demande->start_time) ?: $dash }}</td>
            </tr>
            <tr>
                <td class="k">Date de retour</td>
                <td class="v {{ $demande->end_date ? '' : 'empty' }}">{{ $d($demande->end_date) ?: $dash }}</td>
            </tr>
            <tr>
                <td class="k">Heure de retour</td>
                <td class="v {{ $hm($demande->end_time) ? '' : 'empty' }}">{{ $hm($demande->end_time) ?: $dash }}</td>
            </tr>
            <tr>
                <td class="k">Heure de retour prévue</td>
                <td class="v {{ $hm($demande->return_time) ? '' : 'empty' }}">{{ $hm($demande->return_time) ?: $dash }}</td>
            </tr>
        </table>
    </section>

    {{-- ============================ 5. DÉPLACEMENT ============================ --}}
    <section class="section">
        <h2 class="section-title"><span class="num">5</span> Déplacement</h2>
        <table class="grid">
            <colgroup><col style="width:32%"><col></colgroup>
            <tr>
                <td class="k">Destination</td>
                <td class="v {{ $demande->destination ? '' : 'empty' }}">{{ $demande->destination ?: $dash }}</td>
            </tr>
            <tr>
                <td class="k">Zone</td>
                <td class="v {{ $u?->planningZone?->name ? '' : 'empty' }}">{{ $u?->planningZone?->name ?: $dash }}</td>
            </tr>
            <tr>
                <td class="k">Kilométrage prévu</td>
                <td class="v {{ $demande->kilometers ? '' : 'empty' }}">
                    {{ $demande->kilometers ? number_format($demande->kilometers, 0, ',', ' ') . ' km' : $dash }}
                </td>
            </tr>
        </table>
    </section>

    {{-- ============================== 6. MOTIF ============================== --}}
    <section class="section">
        <h2 class="section-title"><span class="num">6</span> Motif</h2>
        <div class="reason-box {{ $demande->reason ? '' : 'empty' }}">{{ $demande->reason ?: $dash }}</div>
    </section>

    {{-- ============================== 7. STATUT ============================== --}}
    <section class="section">
        <h2 class="section-title"><span class="num">7</span> Statut</h2>
        <div class="status-row">
            <div>
                <div class="lbl">Statut de la demande</div>
                <div class="val">{{ $statusLabel }}</div>
            </div>
            <div>
                <div class="lbl">Dernière mise à jour</div>
                <div class="val small">{{ $dt($demande->updated_at) ?: $dash }}</div>
            </div>
        </div>
    </section>

    {{-- ============================== FOOTER ============================== --}}
    <div class="sheet-footer">
        <div>{{ $systemName }} &mdash; {{ $serviceName }}</div>
        <div>{{ $reference }} &mdash; Imprimée le {{ now()->format('d/m/Y à H:i') }}</div>
    </div>
</div>

<script>
    // Fire only once the document (including the logo image) is fully loaded.
    window.addEventListener('load', function () {
        window.print();
    });
</script>

</body>
</html>