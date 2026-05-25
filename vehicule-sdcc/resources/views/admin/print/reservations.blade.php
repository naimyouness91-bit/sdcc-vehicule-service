<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Réservations - SDCC</title>
    @include('partials.print-styles')
            margin-top: 30pt;
            padding-top: 20pt;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }

        .no-print {
            display: none !important;
        }

        @media print {
            .print-table {
                font-size: 9pt;
            }
            
            .print-table th,
            .print-table td {
                padding: 6pt 4pt;
            }
            
            .print-summary {
                flex-direction: column;
                gap: 10pt;
            }
        }

        .print-table {
            page-break-inside: auto;
        }

        .print-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .print-table thead {
            display: table-header-group;
        }
    </style>
</head>
<body>
    <!-- Print Header -->
    <div class="print-header">
        <h1>Liste des Réservations</h1>
        <div class="subtitle">Système de Gestion des Réservations de Véhicules</div>
        <div class="meta">
            Généré le: {{ now()->format('d/m/Y à H:i') }} | 
            Utilisateur: {{ Auth::user()->name }} | 
            Total: {{ count($reservations) }} réservation(s)
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="print-summary">
        <div class="summary-item">
            <div class="summary-label">Total Réservations</div>
            <div class="summary-value">{{ count($reservations) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Approuvées</div>
            <div class="summary-value">{{ collect($reservations)->where('status', 'approuved')->count() }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">En Attente</div>
            <div class="summary-value">{{ collect($reservations)->where('status', 'pending')->count() }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Date d'Export</div>
            <div class="summary-value" style="font-size: 12pt;">{{ now()->format('d/m/Y') }}</div>
        </div>
    </div>

    <!-- Reservations Table -->
    <table class="print-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="15%">Employé</th>
                <th width="15%">Véhicule</th>
                <th width="15%">Destination</th>
                <th width="12%">Date d'Usage</th>
                <th width="10%">Heure Départ</th>
                <th width="10%">Heure Retour</th>
                <th width="10%">Statut</th>
                <th width="8%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservations as $index => $reservation)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="employee-name">{{ $reservation->employee_name ?? 'N/A' }}</td>
                    <td class="vehicle-name">{{ $reservation->vehicle_name ?? 'N/A' }}</td>
                    <td>{{ $reservation->destination ?? 'Non spécifiée' }}</td>
                    <td>{{ $reservation->date_usage ? \Carbon\Carbon::parse($reservation->date_usage)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $reservation->start_time ?? 'N/A' }}</td>
                    <td>{{ $reservation->end_time ?? 'N/A' }}</td>
                    <td>
                        <span class="status-badge {{ $reservation->status ?? 'pending' }}">
                            {{ ucfirst($reservation->status ?? 'pending') }}
                        </span>
                    </td>
                    <td>
                        @if($reservation->status === 'approuved')
                            <span style="color: #4CAF50;">✓</span>
                        @elseif($reservation->status === 'pending')
                            <span style="color: #ff9800;">⚠</span>
                        @else
                            <span style="color: #f44336;">✗</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20pt; color: #666;">
                        Aucune réservation trouvée
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Print Footer -->
    <div class="print-footer">
        <p>
            <strong>SDCC - Système de Gestion des Réservations de Véhicules</strong><br>
            Document généré automatiquement - Confidentialité professionnelle
        </p>
    </div>

    <!-- Print Script -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
                setTimeout(function() {
                    window.close();
                }, 1000);
            }, 500);
        };

        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>
</html>
