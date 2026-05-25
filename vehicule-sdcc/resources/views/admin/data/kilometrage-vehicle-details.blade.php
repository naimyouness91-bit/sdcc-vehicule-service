<div class="data-table-container" style="margin-bottom: 18px;">
    <div class="table-toolbar" style="border-bottom: none;">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; width:100%; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; background: rgba(46, 125, 50, 0.14); color:#1b5e20;">
                    <i class="fas fa-car"></i>
                </div>
                <div>
                    <div style="font-weight: 900; color:#1f2937; line-height:1.15;">
                        {{ $car->name ?? ('Véhicule #' . $car->id) }}
                    </div>
                    <div style="font-size:12px; color:#6b7280;">
                        {{ $car->matricule ? ('Immat: ' . $car->matricule) : 'Immat: —' }}
                        @if($car->model)
                            · {{ $car->model }}
                        @endif
                        @if($car->year)
                            · {{ $car->year }}
                        @endif
                    </div>
                </div>
            </div>

            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <div style="background: rgba(255, 167, 38, 0.16); border: 1px solid rgba(255, 167, 38, 0.22); padding: 10px 12px; border-radius: 12px; min-width: 170px;">
                    <div style="font-size: 12px; color:#6b7280; font-weight:800; text-transform:uppercase;">Total</div>
                    <div style="font-weight: 900; color:#7a3e00;">{{ number_format((float)($stats['total_km'] ?? 0), 1, ',', ' ') }} km</div>
                </div>
                <div style="background: rgba(46, 125, 50, 0.10); border: 1px solid rgba(46, 125, 50, 0.16); padding: 10px 12px; border-radius: 12px; min-width: 170px;">
                    <div style="font-size: 12px; color:#6b7280; font-weight:800; text-transform:uppercase;">Dernier</div>
                    <div style="font-weight: 900; color:#1b5e20;">
                        {{ number_format((float)($stats['last_km'] ?? 0), 1, ',', ' ') }} km
                        <span style="font-size:12px; color:#6b7280; font-weight:700;">
                            · {{ ($stats['last_date'] ?? null) ? \Illuminate\Support\Carbon::parse($stats['last_date'])->format('d/m/Y') : '—' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="data-table-container">
    <div class="table-toolbar">
        <div style="display:flex; align-items:center; gap:10px;">
            <i class="fas fa-history" style="color:#2E7D32;"></i>
            <strong>Historique kilométrage</strong>
            <span class="status-badge" style="margin-left:8px;">{{ (int)($stats['records'] ?? 0) }} ligne(s)</span>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Kilométrage</th>
                    <th>Source</th>
                    <th>Employé</th>
                    <th>Destination</th>
                    <th>Note / Raison</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $row)
                    <tr>
                        <td>{{ $row['date'] ? \Illuminate\Support\Carbon::parse($row['date'])->format('d/m/Y') : '—' }}</td>
                        <td><strong style="color:#7a3e00;">{{ number_format((float)($row['kilometers'] ?? 0), 1, ',', ' ') }} km</strong></td>
                        <td>
                            @if(($row['type'] ?? '') === 'entry')
                                <span class="status-badge disponible"><i class="fas fa-pen"></i> Manuel</span>
                            @else
                                <span class="status-badge pending"><i class="fas fa-file-alt"></i> Demande</span>
                            @endif
                        </td>
                        <td>{{ $row['employee'] ?? '—' }}</td>
                        <td>{{ $row['destination'] ?? '—' }}</td>
                        <td>{{ $row['note'] ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 40px; color:#6b7280;">Aucun historique pour ce véhicule.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

