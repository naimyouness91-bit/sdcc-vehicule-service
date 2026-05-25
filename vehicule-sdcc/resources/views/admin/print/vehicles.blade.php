<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Véhicules - SDCC</title>
    @include('partials.print-styles')
</head>
<body>
    <!-- Print Header -->
    <div class="print-header">
        <h1>Liste des Véhicules</h1>
        <div class="subtitle">Système de Gestion des Réservations de Véhicules</div>
        <div class="meta">
            Généré le: {{ now()->format('d/m/Y à H:i') }} | 
            Utilisateur: {{ Auth::user()->name }} | 
            Total: {{ count($vehicles) }} véhicule(s)
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="print-summary">
        <div class="summary-item">
            <div class="summary-label">Total Véhicules</div>
            <div class="summary-value">{{ count($vehicles) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Disponibles</div>
            <div class="summary-value">{{ collect($vehicles)->where('status', 'disponible')->count() }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">En Maintenance</div>
            <div class="summary-value">{{ collect($vehicles)->where('status', 'maintenance')->count() }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Date d'Export</div>
            <div class="summary-value" style="font-size: 12pt;">{{ now()->format('d/m/Y') }}</div>
        </div>
    </div>

    <!-- Vehicles Table -->
    <table class="print-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="20%">Marque/Modèle</th>
                <th width="15%">Immatriculation</th>
                <th width="10%">Type</th>
                <th width="10%">Kilométrage</th>
                <th width="10%">Carburant</th>
                <th width="10%">Statut</th>
                <th width="15%">Date d'Ajout</th>
                <th width="5%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vehicles as $index => $vehicle)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="vehicle-name">{{ $vehicle->brand ?? 'N/A' }} {{ $vehicle->model ?? '' }}</td>
                    <td>{{ $vehicle->license_plate ?? 'N/A' }}</td>
                    <td>{{ $vehicle->type ?? 'Non spécifié' }}</td>
                    <td>{{ number_format($vehicle->kilometrage ?? 0, 0, ',', ' ') }} km</td>
                    <td>{{ $vehicle->fuel_type ?? 'Non spécifié' }}</td>
                    <td>
                        <span class="status-badge {{ $vehicle->status ?? 'disponible' }}">
                            {{ ucfirst($vehicle->status ?? 'disponible') }}
                        </span>
                    </td>
                    <td>{{ $vehicle->created_at ? \Carbon\Carbon::parse($vehicle->created_at)->format('d/m/Y') : 'N/A' }}</td>
                    <td>
                        @if($vehicle->status === 'disponible')
                            <span style="color: #4CAF50;">✓</span>
                        @else
                            <span style="color: #ff9800;">⚠</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20pt; color: #666;">
                        Aucun véhicule trouvé
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
