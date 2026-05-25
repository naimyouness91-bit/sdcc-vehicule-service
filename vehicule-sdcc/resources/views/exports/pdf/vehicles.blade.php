<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport des Véhicules - SDCC</title>
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

        .status-badge.disponible {
            background: #e8f5e8;
            color: #2e7d32;
        }

        .status-badge.maintenance {
            background: #fff3e0;
            color: #f57c00;
        }

        .status-badge.indisponible {
            background: #ffebee;
            color: #c62828;
        }

        .kilometrage {
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
        <h1>Rapport des Véhicules</h1>
        <div class="subtitle">Système de Gestion des Réservations de Véhicules</div>
        <div class="meta">
            Généré le: {{ $generatedAt->format('d/m/Y à H:i') }} | 
            Utilisateur: {{ $generatedBy }} | 
            Total: {{ $statistics['total'] }} véhicule(s)
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="summary">
        <div class="summary-item">
            <div class="summary-label">Total Véhicules</div>
            <div class="summary-value">{{ $statistics['total'] }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Disponibles</div>
            <div class="summary-value">{{ $statistics['available'] }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Indisponibles</div>
            <div class="summary-value">{{ $statistics['unavailable'] }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Km</div>
            <div class="summary-value" style="font-size: 14pt;">{{ number_format($statistics['total_km'], 0, ',', ' ') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Modèles</div>
            <div class="summary-value">{{ $statistics['models'] }}</div>
        </div>
    </div>

    <!-- Vehicles Table -->
    <div class="table-container">
        @if($vehicles->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="20%">Nom</th>
                        <th width="15%">Modèle</th>
                        <th width="12%">Immatriculation</th>
                        <th width="10%">Année</th>
                        <th width="12%">Kilométrage</th>
                        <th width="13%">Statut</th>
                        <th width="13%">Date d'Ajout</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehicles as $index => $vehicle)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="vehicle-name">{{ $vehicle->name ?? 'N/A' }}</td>
                            <td>{{ $vehicle->model ?? 'N/A' }}</td>
                            <td>{{ $vehicle->matricule ?? 'N/A' }}</td>
                            <td>{{ $vehicle->year ?? 'N/A' }}</td>
                            <td class="kilometrage">{{ number_format($vehicle->km ?? 0, 0, ',', ' ') }} km</td>
                            <td>
                                <span class="status-badge {{ $vehicle->status ?? 'available' }}">
                                    {{ ucfirst($vehicle->status ?? 'available') }}
                                </span>
                            </td>
                            <td>{{ $vehicle->created_at ? \Carbon\Carbon::parse($vehicle->created_at)->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                Aucun véhicule trouvé
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
