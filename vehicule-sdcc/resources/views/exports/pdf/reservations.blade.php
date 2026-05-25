<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport des Réservations - SDCC</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
            @top-center {
                content: "Système de Gestion des Réservations de Véhicules - SDCC";
                font-size: 10pt;
                color: #666;
                border-bottom: 1px solid #ddd;
                padding-bottom: 5pt;
            }
            @bottom-center {
                content: "Page " counter(page) " sur " counter(pages);
                font-size: 9pt;
                color: #666;
                border-top: 1px solid #ddd;
                padding-top: 5pt;
            }
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 30pt;
            padding-bottom: 20pt;
            border-bottom: 3px solid #4CAF50;
        }

        .header h1 {
            font-size: 20pt;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 8pt;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header .subtitle {
            font-size: 12pt;
            color: #666;
            margin-bottom: 5pt;
        }

        .header .meta {
            font-size: 10pt;
            color: #999;
        }

        .summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25pt;
            padding: 15pt;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4pt;
        }

        .summary-item {
            text-align: center;
            flex: 1;
        }

        .summary-label {
            font-size: 9pt;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3pt;
        }

        .summary-value {
            font-size: 18pt;
            font-weight: 700;
            color: #4CAF50;
        }

        .table-container {
            margin-bottom: 20pt;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .data-table thead {
            background: linear-gradient(135deg, #1a1a2e 0%, #2c3e50 100%);
            color: white;
        }

        .data-table th {
            padding: 12pt 8pt;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #1a1a2e;
        }

        .data-table td {
            padding: 10pt 8pt;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .data-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .employee-name {
            font-weight: 600;
            color: #1a1a2e;
        }

        .vehicle-name {
            font-weight: 600;
            color: #1a1a2e;
        }

        .status-badge {
            display: inline-block;
            padding: 3pt 8pt;
            border-radius: 4pt;
            font-size: 8pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .status-badge.approved {
            background: #e8f5e8;
            color: #2e7d32;
        }

        .status-badge.pending {
            background: #fff3e0;
            color: #f57c00;
        }

        .status-badge.rejected {
            background: #ffebee;
            color: #c62828;
        }

        .status-badge.cancelled {
            background: #ffebee;
            color: #c62828;
        }

        .date-field {
            font-weight: 600;
            color: #666;
        }

        .footer {
            margin-top: 30pt;
            padding-top: 20pt;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }

        .no-data {
            text-align: center;
            padding: 30pt;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Rapport des Réservations</h1>
        <div class="subtitle">Système de Gestion des Réservations de Véhicules</div>
        <div class="meta">
            Généré le: {{ $generatedAt->format('d/m/Y à H:i') }} | 
            Utilisateur: {{ $generatedBy }} | 
            Total: {{ $statistics['total'] }} réservation(s)
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="summary">
        <div class="summary-item">
            <div class="summary-label">Total Réservations</div>
            <div class="summary-value">{{ $statistics['total'] }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Approuvées</div>
            <div class="summary-value">{{ $statistics['approved'] }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">En Attente</div>
            <div class="summary-value">{{ $statistics['pending'] }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Rejetées</div>
            <div class="summary-value">{{ $statistics['rejected'] }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Ce Mois</div>
            <div class="summary-value">{{ $statistics['this_month'] }}</div>
        </div>
    </div>

    <!-- Reservations Table -->
    <div class="table-container">
        @if($reservations->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="15%">Employé</th>
                        <th width="18%">Véhicule</th>
                        <th width="15%">Destination</th>
                        <th width="10%">Date Début</th>
                        <th width="10%">Date Fin</th>
                        <th width="8%">Kilomètres</th>
                        <th width="12%">Statut</th>
                        <th width="7%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservations as $index => $reservation)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="employee-name">{{ $reservation['employee_name'] }}</td>
                            <td class="vehicle-name">{{ $reservation['vehicle_name'] }}</td>
                            <td>{{ $reservation['destination'] ?? 'Non spécifiée' }}</td>
                            <td class="date-field">{{ $reservation['start_date'] ? \Carbon\Carbon::parse($reservation['start_date'])->format('d/m/Y') : 'N/A' }}</td>
                            <td class="date-field">{{ $reservation['end_date'] ? \Carbon\Carbon::parse($reservation['end_date'])->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ number_format($reservation['kilometers'] ?? 0, 0, ',', ' ') }}</td>
                            <td>
                                <span class="status-badge {{ $reservation['status'] ?? 'pending' }}">
                                    {{ ucfirst($reservation['status'] ?? 'pending') }}
                                </span>
                            </td>
                            <td>
                                @if($reservation['status'] === 'approved')
                                    <span style="color: #4CAF50;">✓</span>
                                @elseif($reservation['status'] === 'pending')
                                    <span style="color: #ff9800;">⚠</span>
                                @else
                                    <span style="color: #f44336;">✗</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                Aucune réservation trouvée
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>
            <strong>SDCC - Système de Gestion des Réservations de Véhicules</strong><br>
            Rapport généré automatiquement - Document confidentiel
        </p>
    </div>
</body>
</html>
