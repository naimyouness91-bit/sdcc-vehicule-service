<!-- Zones Table -->
<style>
    .action-buttons { display: flex; gap: 8px; justify-content: center; align-items: center; }
    .icon-btn {
        width: 36px; height: 36px; border: none; border-radius: 8px; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 14px; transition: all 0.25s ease; text-decoration: none;
    }
    .icon-btn.view  { background: #e8f5e9; color: #2e7d32; }
    .icon-btn.view:hover  { background: #4CAF50; color: white; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(76,175,80,0.3); }
    .icon-btn.edit  { background: #fff8e1; color: #e65100; }
    .icon-btn.edit:hover  { background: #FFA726; color: white; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(255,167,38,0.3); }
    .icon-btn.delete { background: #fce4ec; color: #880e4f; }
    .icon-btn.delete:hover { background: #c2185b; color: white; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(194,24,91,0.3); }
</style>

<div class="data-table-container">
    <div class="table-toolbar">
        <div class="search-box">
            <input type="text" id="zonesSearch" placeholder="Rechercher par nom, description..."
                   oninput="filterZonesTable(this.value)">
        </div>
        <div class="table-filters">
            <select class="filter-select" id="zonesStatusFilter" onchange="filterZonesTable()">
                <option value="">Tous les statuts</option>
                <option value="active">Actif</option>
                <option value="in_progress">En cours</option>
                <option value="inactive">Inactif</option>
            </select>
            <a href="{{ route('zones.create') }}" class="action-btn" style="margin: 0; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fas fa-plus"></i> Ajouter
            </a>
        </div>
    </div>

    @if($zones->count() > 0)
        <div class="table-wrapper">
            <table id="zonesTable">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Statut</th>
                        <th>Employés Assignés</th>
                        <th>Véhicules Assignés</th>
                        <th>Date de Création</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($zones as $zone)
                    <tr id="zone-row-{{ $zone->id }}" data-name="{{ strtolower($zone->name) }}" data-status="{{ $zone->status }}">
                        <td><strong>{{ $zone->name }}</strong></td>
                        <td>{{ Str::limit($zone->description ?? '-', 50) }}</td>
                        <td>
                            @if($zone->status === 'active')
                                <span class="status-badge disponible">Actif</span>
                            @elseif($zone->status === 'in_progress')
                                <span class="status-badge pending">En cours</span>
                            @else
                                <span class="status-badge maintenance">Inactif</span>
                            @endif
                        </td>
                        <td>
                            <span style="background:#e3f2fd;color:#1976d2;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                                {{ $zone->users_count }} employé(s)
                            </span>
                        </td>
                        <td>
                            <span style="background:#f3e5f5;color:#7b1fa2;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                                {{ $zone->cars_count }} véhicule(s)
                            </span>
                        </td>
                        <td>{{ $zone->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="action-buttons">
                                {{-- EDIT --}}
                                <a href="{{ route('zones.edit', $zone->id) }}"
                                   class="icon-btn edit" title="Modifier la zone">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- VIEW (same as edit since show is excluded) --}}
                                <a href="{{ route('zones.edit', $zone->id) }}"
                                   class="icon-btn view" title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- DELETE --}}
                                <button class="icon-btn delete zone-delete-btn" title="Supprimer"
                                        data-id="{{ $zone->id }}"
                                        data-name="{{ $zone->name }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;">
                            <p style="color:#999;">Aucune zone trouvée</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Aucune zone de planification</p>
        </div>
    @endif
</div>

<script>
const ZONES_CSRF = '{{ csrf_token() }}';

function filterZonesTable(searchVal) {
    const query  = (searchVal ?? document.getElementById('zonesSearch')?.value ?? '').toLowerCase();
    const status = document.getElementById('zonesStatusFilter')?.value ?? '';
    document.querySelectorAll('#zonesTable tbody tr[id^="zone-row-"]').forEach(row => {
        const matchSearch = !query || row.dataset.name?.includes(query);
        const matchStatus = !status || row.dataset.status === status;
        row.style.display = (matchSearch && matchStatus) ? '' : 'none';
    });
}

document.querySelectorAll('.zone-delete-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const id   = this.dataset.id;
        const name = this.dataset.name;

        const confirmFn = typeof Swal !== 'undefined'
            ? () => Swal.fire({
                icon: 'warning',
                title: 'Supprimer la zone ?',
                html: `<div style="text-align:left;margin:14px 0;font-size:14px;">
                           <p style="margin:6px 0;"><strong>Zone :</strong> ${name}</p>
                           <p style="margin:14px 0 0 0;color:#c62828;font-size:13px;font-weight:600;">
                               <i class="fas fa-exclamation-triangle"></i>
                               Les employés et véhicules seront désassignés. Action irréversible.
                           </p>
                       </div>`,
                confirmButtonText: '<i class="fas fa-trash"></i> Supprimer',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#c62828',
                cancelButtonColor: '#757575',
                showCancelButton: true,
                focusCancel: true,
            })
            : () => Promise.resolve({ isConfirmed: confirm(`Supprimer la zone "${name}" ?`) });

        confirmFn().then(async result => {
            if (!result.isConfirmed) return;
            try {
                const response = await fetch(`/zones/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': ZONES_CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (response.ok || response.redirected) {
                    // Animate row out
                    const row = document.getElementById(`zone-row-${id}`);
                    if (row) {
                        row.style.transition = 'opacity 0.35s, transform 0.35s';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(20px)';
                        setTimeout(() => row.remove(), 380);
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'success', title: 'Supprimée', text: `Zone "${name}" supprimée.`, timer: 2000, showConfirmButton: false });
                    }
                } else {
                    const data = await response.json().catch(() => ({}));
                    const msg = data.message || 'Erreur lors de la suppression.';
                    typeof Swal !== 'undefined' ? Swal.fire({ icon: 'error', title: 'Erreur', text: msg }) : alert(msg);
                }
            } catch (err) {
                const msg = 'Erreur réseau. Veuillez réessayer.';
                typeof Swal !== 'undefined' ? Swal.fire({ icon: 'error', title: 'Erreur réseau', text: msg }) : alert(msg);
            }
        });
    });
});
</script>
