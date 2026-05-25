<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SDCC - Export Planification</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111; font-size: 12px; }
        .header { padding: 12px 0; border-bottom: 2px solid #2e7d32; margin-bottom: 14px; }
        .header h1 { margin: 0; font-size: 18px; }
        .meta { color: #555; font-size: 11px; margin-top: 4px; }
        h2 { font-size: 13px; margin: 18px 0 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; vertical-align: top; }
        th { background: #f7f7f7; font-size: 11px; text-transform: uppercase; letter-spacing: 0.3px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-weight: 700; font-size: 11px; }
        .active { background: #e8f5e9; color: #2e7d32; }
        .in_progress { background: #fff3e0; color: #ef6c00; }
        .inactive { background: #ffebee; color: #c62828; }
        .muted { color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SDCC — Export Planification</h1>
        <div class="meta">Généré le: {{ $generatedAt->format('d/m/Y H:i') }}</div>
    </div>

    <h2>Fenêtres de planification</h2>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Période</th>
                <th>Actif</th>
            </tr>
        </thead>
        <tbody>
            @forelse($windows as $w)
                <tr>
                    <td>{{ $w->name ?? '—' }}</td>
                    <td>{{ optional($w->start_date)->format('d/m/Y') }} → {{ optional($w->end_date)->format('d/m/Y') }}</td>
                    <td>{{ $w->is_active ? 'Oui' : 'Non' }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="muted">Aucune fenêtre</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Zones</h2>
    <table>
        <thead>
            <tr>
                <th>Zone</th>
                <th>Description</th>
                <th>Statut</th>
                <th>Employés</th>
                <th>Véhicules</th>
            </tr>
        </thead>
        <tbody>
            @forelse($zones as $z)
                <tr>
                    <td><strong>{{ $z->name }}</strong></td>
                    <td>{{ $z->description ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $z->status }}">
                            {{ $z->status === 'active' ? 'Active' : ($z->status === 'in_progress' ? 'In Progress' : 'Inactive') }}
                        </span>
                    </td>
                    <td>
                        @if($z->users && $z->users->count())
                            {{ $z->users->map(fn($u) => $u->name)->implode(', ') }}
                        @else
                            <span class="muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($z->cars && $z->cars->count())
                            {{ $z->cars->map(fn($c) => $c->name . ' (' . $c->matricule . ')')->implode(', ') }}
                        @else
                            <span class="muted">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">Aucune zone</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
