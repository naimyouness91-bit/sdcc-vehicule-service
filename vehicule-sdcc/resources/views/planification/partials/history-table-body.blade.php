@forelse($reservations as $index => $reservation)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $reservation->created_at->format('d/m/Y H:i') }}</td>
        <td><strong>{{ $reservation->user?->name ?? 'N/A' }}</strong></td>
        <td>{{ $reservation->user?->service ?? 'N/A' }}</td>
        <td>{{ $reservation->car?->name ?? 'N/A' }}</td>
        <td>
            <span style="font-family: monospace; font-weight: 600; color: var(--primary);">
                {{ $reservation->car?->matricule ?? 'N/A' }}
            </span>
        </td>
        <td>{{ $reservation->destination ?? '-' }}</td>
        <td>{{ $reservation->start_date->format('d/m/Y') }}</td>
        <td>{{ $reservation->end_date->format('d/m/Y') }}</td>
        <td>
            <span title="{{ $reservation->reason ?? '-' }}" style="display: block; max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                {{ $reservation->reason ?? '-' }}
            </span>
        </td>
        <td>
            <span class="status-badge status-{{ $reservation->status }}">
                @if($reservation->status === 'pending')
                    <i class="fas fa-clock"></i> En Attente
                @elseif($reservation->status === 'approved')
                    <i class="fas fa-check-circle"></i> Approuvée
                @elseif($reservation->status === 'cancelled')
                    <i class="fas fa-times-circle"></i> Annulée
                @else
                    <i class="fas fa-ban"></i> Rejetée
                @endif
            </span>
        </td>
    </tr>
@empty
    <tr class="history-empty-row">
        <td colspan="11" style="text-align: center; padding: 40px; color: var(--text-light);">
            <i class="fas fa-inbox" style="font-size: 32px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
            Aucune réservation ne correspond à ce filtre.
        </td>
    </tr>
@endforelse
