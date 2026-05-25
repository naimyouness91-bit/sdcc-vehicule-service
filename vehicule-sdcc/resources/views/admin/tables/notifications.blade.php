<!-- Notifications Table -->
<div class="data-table-container">
    <div class="table-toolbar">
        <div class="search-box">
            <input type="text" placeholder="Rechercher dans les notifications...">
        </div>
        <div class="table-filters">
            <select class="filter-select">
                <option value="">Tous les statuts</option>
                <option value="read">Lues</option>
                <option value="unread">Non lues</option>
            </select>
            <button class="action-btn secondary" style="margin: 0;" onclick="markAllAsRead()">
                <i class="fas fa-check-double"></i> Marquer tout comme lu
            </button>
        </div>
    </div>

    @if($notifications->count() > 0)
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;"></th>
                        <th>Message</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                    <tr id="notification-row-{{ $notification->id }}" style="{{ $notification->read_at ? '' : 'background: rgba(76, 175, 80, 0.05);' }}">
                        <td>
                            @if(!$notification->read_at)
                                <span style="width: 12px; height: 12px; background: #4CAF50; border-radius: 50%; display: inline-block;"></span>
                            @else
                                <span style="width: 12px; height: 12px; background: #ddd; border-radius: 50%; display: inline-block;"></span>
                            @endif
                        </td>
                        <td>
                            {{ $notification->data['message'] ?? json_encode($notification->data) }}
                        </td>
                        <td>
                            @php
                                // Prefer a human-friendly label stored inside the notification payload
                                $typeLabel = $notification->data['type_label'] ?? null;
                                $badgeColor = $notification->data['badge_color'] ?? null;
                                $icon = $notification->data['icon'] ?? null;

                                // Fallback mapping for older notifications that lack type_label
                                $base = $notification->type ? class_basename($notification->type) : null;
                                $fallbackLabels = [
                                    'RequestSubmittedNotification' => 'Nouvelle demande',
                                    'VehicleChangedNotification' => 'Modification véhicule',
                                    'RequestStatusUpdatedNotification' => 'Statut demande',
                                    'VehicleReservationNotification' => 'Réservation',
                                ];

                                if (!$typeLabel) {
                                    if ($base && isset($fallbackLabels[$base])) {
                                        $typeLabel = $fallbackLabels[$base];
                                    } else {
                                        // Last resort: class_basename or generic
                                        $typeLabel = $base ?: ($notification->data['type'] ?? 'Autre');
                                    }
                                }

                                // Default badge color choices when not provided
                                $defaultColors = [
                                    'Nouvelle demande' => '#4CAF50',
                                    'Modification véhicule' => '#FFA726',
                                    'Statut demande' => '#FFA726',
                                    'Réservation' => '#2E7D32',
                                ];
                                $badgeBg = $badgeColor ?? ($defaultColors[$typeLabel] ?? '#e3f2fd');
                                $textColor = '#fff';
                                // use dark text for light backgrounds
                                if (in_array($badgeBg, ['#e3f2fd', '#fff3e0'])) {
                                    $textColor = '#1976d2';
                                }
                            @endphp
                            <span style="display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border-radius:12px;font-size:12px;background:{{ $badgeBg }};color:{{ $textColor }};">
                                @if($icon)
                                    <i class="fas {{ $icon }}" style="width:16px;text-align:center;"></i>
                                @endif
                                <span style="font-weight:600;">{{ $typeLabel }}</span>
                            </span>
                        </td>
                        <td>{{ $notification->created_at->diffForHumans() }}</td>
                        <td>
                            <div class="action-buttons">
                                @if(!$notification->read_at)
                                <button class="icon-btn view" title="Marquer comme lu" onclick="markNotificationAsRead('{{ $notification->id }}')">
                                    <i class="fas fa-envelope-open"></i>
                                </button>
                                @endif
                                <button class="icon-btn delete" title="Supprimer" onclick="deleteNotification('{{ $notification->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px;">
                            <p style="color: #999;">Aucune notification</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
        <div style="padding: 20px; text-align: center; border-top: 1px solid #f0f0f0;">
            {{ $notifications->links() }}
        </div>
        @endif
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Aucune notification</p>
        </div>
    @endif
</div>

<script>
const NOTIFS_CSRF = '{{ csrf_token() }}';

function markNotificationAsRead(id) {
    fetch(`{{ url('/notifications') }}/${id}/read`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': NOTIFS_CSRF
        }
    })
    .then((r) => {
        if (!r.ok) throw new Error('Erreur');
        window.location.reload();
    })
    .catch(() => alert('Erreur lors de la mise à jour.'));
}

function markAllAsRead() {
    if (confirm('Marquer toutes les notifications comme lues?')) {
        fetch(`{{ route('notifications.read-all') }}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': NOTIFS_CSRF
            }
        })
        .then((r) => {
            if (!r.ok) throw new Error('Erreur');
            window.location.reload();
        })
        .catch(() => alert('Erreur lors de la mise à jour.'));
    }
}

function deleteNotification(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette notification?')) {
        fetch(`{{ url('/notifications') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': NOTIFS_CSRF
            }
        })
        .then((r) => {
            if (!r.ok) throw new Error('Erreur');
            document.getElementById(`notification-row-${id}`)?.remove();
        })
        .catch(() => alert('Erreur lors de la suppression.'));
    }
}
</script>
