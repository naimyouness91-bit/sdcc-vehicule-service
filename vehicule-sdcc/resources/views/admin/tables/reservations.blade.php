<!-- Reservations Table -->
<style>
    .btn-create-reservation {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
        color: white;
        padding: 10px 18px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(76, 175, 80, 0.3);
    }

    .btn-create-reservation:hover {
        background: linear-gradient(135deg, #2E7D32 0%, #E65100 100%);
        box-shadow: 0 4px 8px rgba(76, 175, 80, 0.4);
        transform: translateY(-2px);
        text-decoration: none;
        color: white;
    }

    .btn-create-reservation i { font-size: 14px; }

    /* Icon action buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
        align-items: center;
    }

    .icon-btn {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all 0.25s ease;
        text-decoration: none;
    }

    .icon-btn.view {
        background: #e8f5e9;
        color: #2e7d32;
    }
    .icon-btn.view:hover {
        background: #4CAF50;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(76,175,80,0.3);
    }

    .icon-btn.edit {
        background: #fff8e1;
        color: #e65100;
    }
    .icon-btn.edit:hover {
        background: #FFA726;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(255,167,38,0.3);
    }

    .icon-btn.delete {
        background: #fce4ec;
        color: #880e4f;
    }
    .icon-btn.delete:hover {
        background: #c2185b;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(194,24,91,0.3);
    }

    @media (max-width: 768px) {
        .btn-create-reservation { padding: 8px 12px; font-size: 12px; }
    }
</style>

<div class="data-table-container">
    <div class="table-toolbar">
        <div class="search-box">
            <input type="text" id="reservationSearch"
                   placeholder="Rechercher par employé, véhicule, destination..."
                   oninput="filterReservationsTable(this.value)">
        </div>
        <div class="table-filters">
            <select class="filter-select" id="statusFilter" onchange="filterReservationsTable()">
                <option value="">Tous les statuts</option>
                <option value="pending">En attente</option>
                <option value="approved">Approuvée</option>
                <option value="rejected">Rejetée</option>
                <option value="cancelled">Annulée</option>
            </select>
            <a href="{{ route('admin.reservations.create') }}" class="btn-create-reservation"
               title="Créer une nouvelle réservation">
                <i class="fas fa-plus"></i> Créer Réservation
            </a>
        </div>
    </div>

    @if($reservations->count() > 0)
        <div class="table-wrapper">
            <table id="reservationsTable">
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Véhicule</th>
                        <th>Destination</th>
                        <th>Départ</th>
                        <th>Retour</th>
                        <th>Kilométrage</th>
                        <th>Statut</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $reservation)
                    <tr id="reservation-row-{{ $reservation->id }}"
                        data-employee="{{ strtolower($reservation->user?->name ?? '') }}"
                        data-destination="{{ strtolower($reservation->destination ?? '') }}"
                        data-vehicle="{{ strtolower($reservation->car?->name ?? '') }}"
                        data-status="{{ $reservation->status }}">
                        <td>
                            <strong>{{ $reservation->user?->name ?? 'N/A' }}</strong>
                        </td>
                        <td>{{ $reservation->car?->name ?? '(Aucun)' }}</td>
                        <td>{{ $reservation->destination ?? 'N/A' }}</td>
                        <td>{{ $reservation->start_date ? \Carbon\Carbon::parse($reservation->start_date)->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $reservation->end_date ? \Carbon\Carbon::parse($reservation->end_date)->format('d/m/Y') : 'N/A' }}</td>
                        <td>
                            @if($reservation->kilometers)
                                <strong>{{ number_format($reservation->kilometers, 0, ',', ' ') }} km</strong>
                            @else
                                <span style="color: #999;">—</span>
                            @endif
                        </td>
                        <td>
                            @switch($reservation->status)
                                @case('pending')
                                    <span class="status-badge pending">En attente</span>
                                    @break
                                @case('approved')
                                    <span class="status-badge approved">Approuvée</span>
                                    @break
                                @case('rejected')
                                    <span class="status-badge rejected">Rejetée</span>
                                    @break
                                @case('cancelled')
                                    <span class="status-badge cancelled">Annulée</span>
                                    @break
                                @default
                                    <span class="status-badge">{{ ucfirst($reservation->status) }}</span>
                            @endswitch
                        </td>
                        <td>
                            <div class="action-buttons">
                                {{-- VIEW --}}
                                <a href="{{ route('admin.reservations.show', $reservation->id) }}"
                                   class="icon-btn view"
                                   title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- EDIT --}}
                                <a href="{{ route('admin.reservations.edit', $reservation->id) }}"
                                   class="icon-btn edit"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- DELETE --}}
                                <button
                                    class="icon-btn delete res-delete-btn"
                                    title="Supprimer définitivement"
                                    data-id="{{ $reservation->id }}"
                                    data-employee="{{ $reservation->user?->name ?? 'N/A' }}"
                                    data-destination="{{ $reservation->destination ?? 'N/A' }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px;">
                            <p style="color: #999;">Aucune réservation</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Aucune réservation dans le système</p>
        </div>
    @endif
</div>

<script>
const RESERVATIONS_CSRF = '{{ csrf_token() }}';

// ── Client-side search + status filter ────────────────────────────────────────
function filterReservationsTable(searchVal) {
    const query  = (searchVal ?? document.getElementById('reservationSearch')?.value ?? '').toLowerCase();
    const status = document.getElementById('statusFilter')?.value ?? '';
    const rows   = document.querySelectorAll('#reservationsTable tbody tr[id^="reservation-row-"]');

    rows.forEach(row => {
        const matchSearch = !query ||
            row.dataset.employee?.includes(query) ||
            row.dataset.destination?.includes(query) ||
            row.dataset.vehicle?.includes(query);

        const matchStatus = !status || row.dataset.status === status;

        row.style.display = (matchSearch && matchStatus) ? '' : 'none';
    });
}

// ── Delete buttons ─────────────────────────────────────────────────────────────
document.querySelectorAll('.res-delete-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const id          = this.dataset.id;
        const employee    = this.dataset.employee;
        const destination = this.dataset.destination;

        // Use SweetAlert2 if available, otherwise native confirm
        const confirmFn = (typeof Swal !== 'undefined')
            ? () => Swal.fire({
                icon: 'warning',
                title: 'Supprimer la réservation ?',
                html: `
                    <div style="text-align:left; margin:14px 0; font-size:14px;">
                        <p style="margin:6px 0;"><strong>Employé :</strong> ${employee}</p>
                        <p style="margin:6px 0;"><strong>Destination :</strong> ${destination}</p>
                        <p style="margin:14px 0 0 0; color:#c62828; font-size:13px; font-weight:600;">
                            <i class="fas fa-exclamation-triangle"></i>
                            Cette action est irréversible.
                        </p>
                    </div>`,
                confirmButtonText: '<i class="fas fa-trash"></i> Oui, supprimer',
                cancelButtonText:  'Annuler',
                confirmButtonColor: '#c62828',
                cancelButtonColor:  '#757575',
                showCancelButton:   true,
                focusCancel:        true,
            })
            : () => Promise.resolve({ isConfirmed: confirm(`Supprimer la réservation de ${employee} ?`) });

        confirmFn().then(async result => {
            if (!result.isConfirmed) return;

            try {
                const response = await fetch(`/admin/reservations/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type':  'application/json',
                        'Accept':        'application/json',
                        'X-CSRF-TOKEN':  RESERVATIONS_CSRF,
                    },
                });

                const data = await response.json().catch(() => ({}));

                if (response.ok && data.success) {
                    // Animate row out then remove it
                    const row = document.getElementById(`reservation-row-${id}`);
                    if (row) {
                        row.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
                        row.style.opacity    = '0';
                        row.style.transform  = 'translateX(20px)';
                        setTimeout(() => row.remove(), 370);
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Supprimée',
                            text:  data.message || `Réservation #${id} supprimée.`,
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                } else {
                    const msg = data.message || 'Erreur lors de la suppression.';
                    typeof Swal !== 'undefined'
                        ? Swal.fire({ icon: 'error', title: 'Erreur', text: msg })
                        : alert(msg);
                }
            } catch (err) {
                console.error('Delete error:', err);
                const msg = 'Erreur réseau. Veuillez réessayer.';
                typeof Swal !== 'undefined'
                    ? Swal.fire({ icon: 'error', title: 'Erreur réseau', text: msg })
                    : alert(msg);
            }
        });
    });
});
</script>
