<!-- Vehicles Table -->
<style>
    /* Reusable modal styles (used for CRUD) */
    .km-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.55);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 18px;
    }
    .km-modal-backdrop.open { display: flex; }
    .km-modal {
        width: 100%;
        max-width: 560px;
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.22);
    }
    .km-modal-header {
        padding: 14px 16px;
        background: linear-gradient(135deg, rgba(46, 125, 50, 0.08) 0%, rgba(255, 167, 38, 0.10) 100%);
        border-bottom: 1px solid #eef2f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }
    .km-modal-header strong { font-size: 14px; color:#1f2937; }
    .km-modal-body { padding: 16px; }
    .km-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .km-field label {
        display: block;
        font-size: 12px;
        font-weight: 800;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }
    .km-field input, .km-field select, .km-field textarea {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 10px 12px;
        font-size: 13px;
        outline: none;
        transition: box-shadow 0.15s ease, border-color 0.15s ease;
    }
    .km-field input:focus, .km-field select:focus, .km-field textarea:focus {
        border-color: rgba(46, 125, 50, 0.6);
        box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.10);
    }
    .km-modal-actions {
        display:flex;
        justify-content:flex-end;
        gap:10px;
        padding: 12px 16px;
        border-top: 1px solid #eef2f7;
        background: #fff;
    }
    @media (max-width: 520px) { .km-form-grid { grid-template-columns: 1fr; } }

    /* ── Action buttons ── */
    .action-buttons { display: flex; gap: 8px; justify-content: center; align-items: center; }
    .icon-btn {
        width: 34px; height: 34px; border: none; border-radius: 8px; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 14px; transition: all 0.25s ease; text-decoration: none;
        flex-shrink: 0;
    }
    .icon-btn.view   { background: #e8f5e9; color: #2e7d32; }
    .icon-btn.view:hover   { background: #4CAF50; color: white; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(76,175,80,0.3); }
    .icon-btn.edit   { background: #fff8e1; color: #e65100; }
    .icon-btn.edit:hover   { background: #FFA726; color: white; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(255,167,38,0.3); }
    .icon-btn.delete { background: #fce4ec; color: #880e4f; }
    .icon-btn.delete:hover { background: #c2185b; color: white; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(194,24,91,0.3); }
</style>

<div class="data-table-container">
    <div class="table-toolbar">
        <div class="search-box">
            <input type="text" placeholder="Rechercher par nom, immatriculation, modèle...">
        </div>
        <div class="table-filters">
            <select class="filter-select">
                <option value="">Tous les statuts</option>
                <option value="disponible">Disponible</option>
                <option value="maintenance">Maintenance</option>
            </select>
            <button class="action-btn" style="margin: 0;" onclick="openVehicleModal()">
                <i class="fas fa-plus"></i> Ajouter
            </button>
        </div>
    </div>

    @if($vehicles->count() > 0)
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Immatriculation</th>
                        <th>Modèle</th>
                        <th>Année</th>
                        <th>Kilométrage</th>
                        <th>Statut</th>
                        <th>Type de Disponibilité</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $vehicle)
                    <tr id="vehicle-row-{{ $vehicle->id }}"
                        data-id="{{ $vehicle->id }}"
                        data-name="{{ e($vehicle->name) }}"
                        data-matricule="{{ e($vehicle->matricule ?? '') }}"
                        data-model="{{ e($vehicle->model ?? '') }}"
                        data-year="{{ $vehicle->year ?? '' }}"
                        data-km="{{ $vehicle->km ?? 0 }}"
                        data-status="{{ $vehicle->status ?? 'disponible' }}"
                        data-availability="{{ $vehicle->availability_type ?? 'both' }}">
                        <td>
                            <strong>{{ $vehicle->name }}</strong>
                        </td>
                        <td>
                            <span style="background: #fff3cd; padding: 4px 8px; border-radius: 4px; font-weight: 600; font-family: monospace;">
                                {{ $vehicle->matricule }}
                            </span>
                        </td>
                        <td>{{ $vehicle->model ?? 'N/A' }}</td>
                        <td>{{ $vehicle->year ?? 'N/A' }}</td>
                        <td>
                            <strong>{{ number_format($vehicle->km ?? 0, 0, ',', ' ') }} km</strong>
                        </td>
                        <td>
                            @if($vehicle->status === 'disponible')
                                <span class="status-badge disponible">Disponible</span>
                            @else
                                <span class="status-badge maintenance">Maintenance</span>
                            @endif
                        </td>
                        <td>
                            <span style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                                {{ ucfirst($vehicle->availability_type ?? 'standard') }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="icon-btn edit" title="Modifier" onclick="editVehicle({{ $vehicle->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="icon-btn view" title="Voir" onclick="viewVehicle({{ $vehicle->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="icon-btn delete" title="Supprimer" onclick="deleteVehicle({{ $vehicle->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px;">
                            <p style="color: #999;">Aucun véhicule trouvé</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Aucun véhicule dans le système</p>
        </div>
    @endif
</div>

<script>
const VEHICLE_CSRF = '{{ csrf_token() }}';
let vehicleModalMode = 'create';
let vehicleEditingId = null;

function vehicleRowData(id) {
    const row = document.getElementById(`vehicle-row-${id}`);
    if (!row) return null;
    return {
        id:           row.getAttribute('data-id'),
        name:         row.getAttribute('data-name')         || '',
        matricule:    row.getAttribute('data-matricule')    || '',
        model:        row.getAttribute('data-model')        || '',
        year:         row.getAttribute('data-year')         || '',
        km:           Number(row.getAttribute('data-km')    || 0),
        status:       row.getAttribute('data-status')       || 'disponible',
        availability: row.getAttribute('data-availability') || 'both',
    };
}

function ensureVehicleModal() {
    if (document.getElementById('vehicleModal')) return;
    const modal = document.createElement('div');
    modal.id = 'vehicleModal';
    modal.className = 'km-modal-backdrop';
    modal.innerHTML = `
        <div class="km-modal">
            <div class="km-modal-header">
                <div style="display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-car" style="color:#2E7D32;"></i>
                    <strong id="vehicleModalTitle">Ajouter un véhicule</strong>
                </div>
                <button class="icon-btn delete" type="button" title="Fermer" onclick="closeVehicleModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="km-modal-body">
                <div id="vehicleForm">
                    <div class="km-form-grid">
                        <div class="km-field" style="grid-column: 1 / -1;">
                            <label>Nom</label>
                            <input type="text" name="name" id="vehName" required>
                        </div>
                        <div class="km-field">
                            <label>Matricule</label>
                            <input type="text" name="matricule" id="vehMatricule" required>
                        </div>
                        <div class="km-field">
                            <label>Modèle</label>
                            <input type="text" name="model" id="vehModel" required>
                        </div>
                        <div class="km-field">
                            <label>Année</label>
                            <input type="number" name="year" id="vehYear" min="2000" max="${new Date().getFullYear()}" required>
                        </div>
                        <div class="km-field">
                            <label>Kilométrage (km)</label>
                            <input type="number" name="km" id="vehKm" min="0" step="1" required>
                        </div>
                        <div class="km-field" style="grid-column: 1 / -1;">
                            <label>Statut</label>
                            <select name="status" id="vehStatus" required>
                                <option value="disponible">Disponible</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="km-field" style="grid-column: 1 / -1;">
                            <label>Type de disponibilité</label>
                            <select name="availability_type" id="vehAvailability" required>
                                <option value="both">Semaine + Week-end</option>
                                <option value="weekend">Week-end uniquement</option>
                                <option value="weekday">Semaine uniquement</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="vehicleModalError" style="display:none; margin-top: 10px; background: rgba(255, 167, 38, 0.16); border: 1px solid rgba(255, 167, 38, 0.25); color:#7a3e00; padding: 10px 12px; border-radius: 12px; font-weight: 700;"></div>
            </div>
            <div class="km-modal-actions">
                <button class="action-btn secondary" type="button" onclick="closeVehicleModal()">
                    <i class="fas fa-ban"></i>
                    Annuler
                </button>
                <button class="action-btn" type="button" onclick="submitVehicleModal()">
                    <i class="fas fa-check"></i>
                    Enregistrer
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    modal.addEventListener('click', (e) => { if (e.target === modal) closeVehicleModal(); });
}

function openVehicleModal() {
    ensureVehicleModal();
    vehicleModalMode  = 'create';
    vehicleEditingId  = null;
    document.getElementById('vehicleModalTitle').textContent = 'Ajouter un véhicule';
    document.getElementById('vehicleModalError').style.display = 'none';

    // Clear all fields manually (vehicleForm is a <div>, not a <form>, so .reset() doesn't exist)
    document.getElementById('vehName').value         = '';
    document.getElementById('vehMatricule').value    = '';
    document.getElementById('vehModel').value        = '';
    document.getElementById('vehYear').value         = '';
    document.getElementById('vehKm').value           = '0';
    document.getElementById('vehStatus').value       = 'disponible';
    document.getElementById('vehAvailability').value = 'both';

    document.getElementById('vehicleModal').classList.add('open');
}

function editVehicle(id) {
    ensureVehicleModal();
    const data = vehicleRowData(id);
    vehicleModalMode = 'edit';
    vehicleEditingId = id;
    document.getElementById('vehicleModalTitle').textContent = 'Modifier véhicule';
    document.getElementById('vehicleModalError').style.display = 'none';

    document.getElementById('vehName').value         = data?.name         || '';
    document.getElementById('vehMatricule').value    = data?.matricule    || '';
    document.getElementById('vehModel').value        = data?.model        || '';
    document.getElementById('vehYear').value         = data?.year         || '';
    document.getElementById('vehKm').value           = data?.km           ?? 0;
    document.getElementById('vehStatus').value       = data?.status       || 'disponible';
    document.getElementById('vehAvailability').value = data?.availability || 'both';

    document.getElementById('vehicleModal').classList.add('open');
}

function viewVehicle(id) {
    editVehicle(id);
}

function closeVehicleModal() {
    document.getElementById('vehicleModal')?.classList.remove('open');
}

async function submitVehicleModal() {
    const err = document.getElementById('vehicleModalError');
    err.style.display = 'none';
    err.textContent = '';

    const payload = {
        name:              document.getElementById('vehName').value.trim(),
        matricule:         document.getElementById('vehMatricule').value.trim(),
        model:             document.getElementById('vehModel').value.trim(),
        year:              Number(document.getElementById('vehYear').value),
        km:                Number(document.getElementById('vehKm').value),
        status:            document.getElementById('vehStatus').value,
        availability_type: document.getElementById('vehAvailability').value,
    };

    const url = vehicleModalMode === 'create'
        ? `{{ route('data-entry.vehicles.store') }}`
        : `{{ url('/data-entry/vehicles') }}/${vehicleEditingId}`;

    const method = vehicleModalMode === 'create' ? 'POST' : 'PUT';

    try {
        const res = await fetch(url, {
            method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': VEHICLE_CSRF,
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json().catch(() => null);
        if (!res.ok || data?.success === false) {
            const msg = data?.message || 'Erreur lors de l’enregistrement.';
            const firstKey = data?.errors ? Object.keys(data.errors)[0] : null;
            throw new Error(firstKey ? data.errors[firstKey][0] : msg);
        }

        closeVehicleModal();
        window.location.reload();
    } catch (e) {
        err.style.display = 'block';
        err.textContent = e?.message || 'Erreur lors de l’enregistrement.';
    }
}

async function deleteVehicle(id) {
    const row  = document.getElementById(`vehicle-row-${id}`);
    const name = row?.getAttribute('data-name') || `#${id}`;
    const mat  = row?.getAttribute('data-matricule') || '';

    const confirmFn = typeof Swal !== 'undefined'
        ? () => Swal.fire({
            icon: 'warning',
            title: 'Supprimer ce véhicule ?',
            html: `<div style="text-align:left;margin:14px 0;font-size:14px;">
                       <p style="margin:6px 0;"><strong>Nom :</strong> ${name}</p>
                       <p style="margin:6px 0;"><strong>Matricule :</strong> ${mat}</p>
                       <p style="margin:14px 0 0;color:#c62828;font-size:13px;font-weight:600;">
                           <i class="fas fa-exclamation-triangle"></i> Action irréversible.
                       </p>
                   </div>`,
            confirmButtonText: '<i class="fas fa-trash"></i> Supprimer',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#c62828',
            cancelButtonColor: '#757575',
            showCancelButton: true,
            focusCancel: true,
        })
        : () => Promise.resolve({ isConfirmed: confirm(`Supprimer le véhicule "${name}" ?`) });

    const result = await confirmFn();
    if (!result.isConfirmed) return;

    try {
        const res = await fetch(`{{ url('/data-entry/vehicles') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': VEHICLE_CSRF,
            },
        });
        const data = await res.json().catch(() => null);
        if (!res.ok || data?.success === false) throw new Error(data?.message || 'Erreur lors de la suppression.');

        if (row) {
            row.style.transition = 'opacity 0.35s, transform 0.35s';
            row.style.opacity    = '0';
            row.style.transform  = 'translateX(20px)';
            setTimeout(() => row.remove(), 380);
        }
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'success', title: 'Supprimé', text: `Véhicule "${name}" supprimé.`, timer: 2000, showConfirmButton: false });
        }
    } catch (e) {
        typeof Swal !== 'undefined'
            ? Swal.fire({ icon: 'error', title: 'Erreur', text: e?.message || 'Erreur lors de la suppression.' })
            : alert(e?.message || 'Erreur lors de la suppression.');
    }
}
</script>
