{{-- Requests Table - All Demandes (admin view) --}}
@php
    $conflictCount = $requests->where('has_conflict', true)->where('status', 'pending')->count();
@endphp

<style>
    .conflict-alert-banner {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        background: linear-gradient(135deg, #fff3e0 0%, #ffebee 100%);
        border: 1px solid #ffcdd2;
        border-left: 5px solid #e53935;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 18px;
        color: #b71c1c;
        font-size: 14px;
    }
    .conflict-alert-banner i { font-size: 20px; margin-top: 2px; }
    .conflict-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ef9a9a;
        border-radius: 20px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 700;
        margin-top: 6px;
        white-space: nowrap;
    }
    tr.has-conflict-row { background: rgba(229, 57, 53, 0.04); }
    tr.has-conflict-row:hover { background: rgba(229, 57, 53, 0.08); }
</style>

@if($conflictCount > 0)
    <div class="conflict-alert-banner" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <div>
            <strong>{{ $conflictCount }} conflit(s) de réservation détecté(s)</strong>
            <div style="margin-top:4px; font-size:13px; color:#6d1b1b;">
                Des demandes chevauchent des réservations existantes. Vérifiez et traitez-les manuellement (approuver ou refuser).
            </div>
        </div>
    </div>
@endif

<div class="data-table-container">
    <div class="table-toolbar">
        <div class="search-box">
            <input type="text" id="reqSearch" placeholder="Rechercher par employé, véhicule, destination...">
        </div>
        <div class="table-filters">
            <select class="filter-select" id="reqStatusFilter">
                <option value="">Tous les statuts</option>
                <option value="pending">En attente</option>
                <option value="approved">Approuvée</option>
                <option value="rejected">Rejetée</option>
                <option value="cancelled">Annulée</option>
            </select>
        </div>
    </div>

    @if($requests->count() > 0)
        <div class="table-wrapper">
            <table id="requestsTable">
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Destination</th>
                        <th>Véhicule Demandé</th>
                        <th>Date de Départ</th>
                        <th>Date de Retour</th>
                        <th>Raison</th>
                        <th>Statut</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                    <tr id="request-row-{{ $request->id }}" data-status="{{ $request->status }}" class="{{ $request->has_conflict ? 'has-conflict-row' : '' }}">
                        <td><strong>{{ $request->user?->name ?? 'N/A' }}</strong></td>
                        <td>{{ $request->destination ?? 'N/A' }}</td>
                        <td>{{ $request->car?->name ?? '(Aucun spécifié)' }}</td>
                        <td>{{ $request->start_date ? \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $request->end_date   ? \Carbon\Carbon::parse($request->end_date)->format('d/m/Y')   : 'N/A' }}</td>
                        <td>{{ $request->reason ?? '-' }}</td>
                        <td>
                            @php
                                $badgeClass = match($request->status) {
                                    'approved'  => 'approved',
                                    'rejected'  => 'rejected',
                                    'cancelled' => 'cancelled',
                                    default     => 'pending',
                                };
                                $badgeLabel = match($request->status) {
                                    'approved'  => 'Approuvée',
                                    'rejected'  => 'Rejetée',
                                    'cancelled' => 'Annulée',
                                    default     => 'En attente',
                                };
                            @endphp
                            <span class="status-badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                            @if($request->has_conflict)
                                <span class="conflict-badge" title="Conflit de dates avec une autre réservation">
                                    <i class="fas fa-exclamation-triangle"></i> Conflit de réservation détecté
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                @if($request->status === 'pending')
                                    <button class="icon-btn" style="background:rgba(76,175,80,.15);color:#2E7D32;"
                                            title="Approuver"
                                            onclick="approveRequest({{ $request->id }})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="icon-btn delete" title="Rejeter"
                                            onclick="rejectRequest({{ $request->id }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @else
                                    <span style="color:#aaa;font-size:12px;padding:0 8px;">—</span>
                                @endif
                                <button class="icon-btn view" title="Voir détails"
                                        onclick="viewRequest({{ $request->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Aucune demande trouvée</p>
        </div>
    @endif
</div>

<script>
const REQUESTS_CSRF = '{{ csrf_token() }}';

/* ── Status filter ─────────────────────────────────────────── */
function applyRequestFilters() {
    const q      = (document.getElementById('reqSearch').value || '').toLowerCase();
    const status = document.getElementById('reqStatusFilter').value;
    document.querySelectorAll('#requestsTable tbody tr').forEach(row => {
        const text   = (row.textContent || '').toLowerCase();
        const rStat  = row.dataset.status || '';
        const matchQ = !q      || text.includes(q);
        const matchS = !status || rStat === status;
        row.style.display = (matchQ && matchS) ? '' : 'none';
    });
}

document.getElementById('reqSearch')?.addEventListener('keyup',  applyRequestFilters);
document.getElementById('reqStatusFilter')?.addEventListener('change', applyRequestFilters);

/* ── Approve ───────────────────────────────────────────────── */
function approveRequest(id) {
    Swal.fire({
        title: 'Approuver cette demande ?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Oui, approuver',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#2E7D32',
    }).then(result => {
        if (!result.isConfirmed) return;
        fetch(`{{ url('/admin/reservations') }}/${id}/approve`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': REQUESTS_CSRF
            }
        })
        .then(async r => {
            const data = await r.json().catch(() => null);
            if (!r.ok || data?.success === false) throw new Error(data?.message || 'Erreur');
            const row = document.getElementById(`request-row-${id}`);
            if (row) {
                row.querySelector('.status-badge').className = 'status-badge approved';
                row.querySelector('.status-badge').textContent = 'Approuvée';
                row.dataset.status = 'approved';
                // Remove action buttons
                const btns = row.querySelector('.action-buttons');
                if (btns) btns.innerHTML = '<span style="color:#aaa;font-size:12px;padding:0 8px;">—</span>';
            }
            Swal.fire({ toast:true, position:'top-end', icon:'success', title:'Demande approuvée', showConfirmButton:false, timer:2500 });
        })
        .catch(e => Swal.fire({ icon:'error', title:'Erreur', text: e?.message || "Erreur lors de l'approbation." }));
    });
}

/* ── Reject ────────────────────────────────────────────────── */
function rejectRequest(id) {
    Swal.fire({
        title: 'Rejeter cette demande ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Oui, rejeter',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#c62828',
    }).then(result => {
        if (!result.isConfirmed) return;
        fetch(`{{ url('/admin/reservations') }}/${id}/cancel`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': REQUESTS_CSRF
            }
        })
        .then(async r => {
            const data = await r.json().catch(() => null);
            if (!r.ok || data?.success === false) throw new Error(data?.message || 'Erreur');
            const row = document.getElementById(`request-row-${id}`);
            if (row) {
                row.querySelector('.status-badge').className = 'status-badge rejected';
                row.querySelector('.status-badge').textContent = 'Rejetée';
                row.dataset.status = 'rejected';
                const btns = row.querySelector('.action-buttons');
                if (btns) btns.innerHTML = '<span style="color:#aaa;font-size:12px;padding:0 8px;">—</span>';
            }
            Swal.fire({ toast:true, position:'top-end', icon:'success', title:'Demande rejetée', showConfirmButton:false, timer:2500 });
        })
        .catch(e => Swal.fire({ icon:'error', title:'Erreur', text: e?.message || 'Erreur lors du rejet.' }));
    });
}

/* ── View ──────────────────────────────────────────────────── */
function viewRequest(id) {
    window.location.href = `{{ route('admin.reservations.index') }}?focus=${id}`;
}
</script>
