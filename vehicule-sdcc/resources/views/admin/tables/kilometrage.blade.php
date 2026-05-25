<!-- Kilometrage Table -->
<style>
    .km-quick-select {
        display: grid;
        grid-template-columns: repeat(2, minmax(260px, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }

    .km-quick-card {
        border-radius: 16px;
        border: 1px solid rgba(46, 125, 50, 0.14);
        background: linear-gradient(135deg, rgba(46, 125, 50, 0.06) 0%, rgba(255, 167, 38, 0.08) 100%);
        padding: 14px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
    }

    .km-quick-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 16px 28px rgba(0, 0, 0, 0.10);
        border-color: rgba(255, 167, 38, 0.40);
    }

    .km-quick-card.active {
        border-color: rgba(255, 167, 38, 0.65);
        box-shadow: 0 18px 32px rgba(255, 167, 38, 0.18);
    }

    .km-quick-left {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .km-quick-icon {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 167, 38, 0.22);
        color: #7a3e00;
        flex: 0 0 auto;
    }

    .km-quick-title {
        font-weight: 900;
        color: #1f2937;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .km-quick-sub {
        font-size: 12px;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .km-explorer {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 14px;
        margin-bottom: 18px;
    }

    .km-vehicles {
        position: sticky;
        top: 90px;
        align-self: start;
    }

    .km-vehicle-list {
        padding: 14px;
        display: grid;
        gap: 10px;
    }

    .km-vehicle-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 12px;
        border-radius: 14px;
        border: 1px solid rgba(46, 125, 50, 0.12);
        background: rgba(46, 125, 50, 0.05);
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease, border-color 0.15s ease;
    }

    .km-vehicle-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 18px rgba(0, 0, 0, 0.08);
        border-color: rgba(255, 167, 38, 0.35);
        background: rgba(255, 167, 38, 0.08);
    }

    .km-vehicle-card.active {
        border-color: rgba(255, 167, 38, 0.55);
        box-shadow: 0 14px 22px rgba(255, 167, 38, 0.18);
        background: rgba(255, 167, 38, 0.12);
    }

    .km-vehicle-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .km-vehicle-icon {
        width: 40px;
        height: 40px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 167, 38, 0.22);
        color: #7a3e00;
        flex: 0 0 auto;
    }

    .km-vehicle-name {
        font-weight: 900;
        color: #1f2937;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 210px;
    }

    .km-vehicle-sub {
        font-size: 12px;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 210px;
    }

    .km-details {
        min-height: 180px;
    }

    .km-details-loading {
        padding: 60px 20px;
        text-align: center;
        color: #6b7280;
    }

    .km-details-loading i {
        font-size: 34px;
        color: rgba(46, 125, 50, 0.35);
        margin-bottom: 10px;
    }

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

    .km-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

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

    .km-field textarea { min-height: 90px; resize: vertical; }

    .km-modal-actions {
        display:flex;
        justify-content:flex-end;
        gap:10px;
        padding: 12px 16px;
        border-top: 1px solid #eef2f7;
        background: #fff;
    }

    @media (max-width: 1024px) {
        .km-quick-select { grid-template-columns: 1fr; }
        .km-explorer { grid-template-columns: 1fr; }
        .km-vehicles { position: static; }
        .km-vehicle-name, .km-vehicle-sub { max-width: 100%; }
    }
    @media (max-width: 520px) { .km-form-grid { grid-template-columns: 1fr; } }
</style>

@if(isset($highlightVehicles) && $highlightVehicles && count($highlightVehicles) > 0)
<div style="display:flex; align-items:center; justify-content:flex-end; gap:10px; margin-bottom: 10px; flex-wrap:wrap;">
    <div style="display:flex; align-items:center; gap:8px; color:#6b7280; font-weight:800; text-transform:uppercase; font-size:12px; letter-spacing:0.4px;">
        <i class="fas fa-filter" style="color:#FFA726;"></i>
        Affichage
    </div>
    <select class="filter-select" id="kmAllVehiclesSelect" style="min-width: 220px;" onchange="kmHandleAllVehiclesSelect(this.value)">
        <option value="all" selected>Tous les véhicules</option>
        <option value="pick">Choisir un véhicule…</option>
    </select>
</div>

<div class="km-quick-select" id="kmQuickSelect">
    @foreach($highlightVehicles as $v)
        <div class="km-quick-card"
             data-quick-car-id="{{ $v['id'] }}"
             onclick="kmSelectVehicle({{ $v['id'] }})">
            <div class="km-quick-left">
                <div class="km-quick-icon"><i class="fas fa-car"></i></div>
                <div style="min-width:0;">
                    <div class="km-quick-title">{{ $v['name'] }}</div>
                    <div class="km-quick-sub">{{ $v['plate'] ? ('Immat: ' . $v['plate']) : 'Immat: —' }}</div>
                </div>
            </div>
            <div style="text-align:right;">
                <div style="font-weight:900; color:#1b5e20;">{{ number_format((float)($v['total_km'] ?? 0), 1, ',', ' ') }} km</div>
                <div style="font-size:12px; color:#6b7280;"><i class="fas fa-history" style="color:#FFA726;"></i> {{ (int)($v['trips'] ?? 0) }} trajets</div>
            </div>
        </div>
    @endforeach
</div>
@endif

<div class="km-explorer">
    <div class="data-table-container km-vehicles">
        <div class="table-toolbar">
            <div style="display:flex; align-items:center; gap:10px;">
                <i class="fas fa-car" style="color:#2E7D32;"></i>
                <strong>Véhicules</strong>
            </div>
            <div style="position:relative;">
                <button class="action-btn" type="button" onclick="kmToggleAddMenu()">
                    <i class="fas fa-plus"></i>
                    Ajouter
                    <i class="fas fa-chevron-down" style="margin-left:4px; font-size: 12px;"></i>
                </button>
                <div id="kmAddMenu" style="position:absolute; right:0; top: 44px; min-width: 220px; background:#fff; border:1px solid #eef2f7; border-radius: 14px; box-shadow: 0 16px 28px rgba(0,0,0,0.12); padding: 8px; display:none; z-index: 50;">
                    <button type="button" class="action-btn" style="width:100%; justify-content:flex-start; border-radius: 12px; background: rgba(46,125,50,0.10); color:#1b5e20;" onclick="kmOpenAddModal(); kmHideAddMenu();">
                        <i class="fas fa-road"></i>
                        Ajouter kilométrage
                    </button>
                    <button type="button" class="action-btn secondary" style="width:100%; justify-content:flex-start; border-radius: 12px;" onclick="kmOpenAddVehicleModal(); kmHideAddMenu();">
                        <i class="fas fa-car-side"></i>
                        Ajouter véhicule
                    </button>
                </div>
            </div>
        </div>
        <div class="km-vehicle-list" id="kmVehicleList">
            @forelse(($cars ?? []) as $car)
                <div class="km-vehicle-card" data-car-id="{{ $car->id }}" onclick="kmSelectVehicle({{ $car->id }})">
                    <div class="km-vehicle-meta">
                        <div class="km-vehicle-icon"><i class="fas fa-car"></i></div>
                        <div style="min-width:0;">
                            <div class="km-vehicle-name">{{ $car->name ?? ('Véhicule #' . $car->id) }}</div>
                            <div class="km-vehicle-sub">{{ $car->matricule ? ('Immat: ' . $car->matricule) : 'Immat: —' }}</div>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right" style="color: rgba(46,125,50,0.75);"></i>
                </div>
            @empty
                <div class="empty-state" style="padding: 30px 16px;">
                    <i class="fas fa-inbox"></i>
                    <p>Aucun véhicule</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="km-details" id="kmDetails">
        <div class="data-table-container">
            <div class="km-details-loading">
                <i class="fas fa-hand-pointer"></i>
                <p style="margin:0; font-weight:800; color:#1f2937;">Sélectionnez un véhicule</p>
                <p style="margin:10px 0 0 0;">Son historique kilométrage s’affichera ici.</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add entry -->
<div class="km-modal-backdrop" id="kmAddModal" role="dialog" aria-modal="true">
    <div class="km-modal">
        <div class="km-modal-header">
            <div style="display:flex; align-items:center; gap:10px;">
                <i class="fas fa-road" style="color:#2E7D32;"></i>
                <strong>Ajouter un kilométrage</strong>
            </div>
            <button class="icon-btn delete" type="button" title="Fermer" onclick="kmCloseAddModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="km-modal-body">
            <form id="kmAddForm">
                @csrf
                <div class="km-form-grid">
                    <div class="km-field" style="grid-column: 1 / -1;">
                        <label>Véhicule</label>
                        <select name="car_id" id="kmCarSelect" required>
                            <option value="">Choisir un véhicule...</option>
                            @foreach(($cars ?? []) as $car)
                                <option value="{{ $car->id }}">{{ $car->name ?? ('Véhicule #' . $car->id) }}{{ $car->matricule ? (' — ' . $car->matricule) : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="km-field">
                        <label>Date</label>
                        <input type="date" name="recorded_at" id="kmRecordedAt" required>
                    </div>
                    <div class="km-field">
                        <label>Kilométrage (km)</label>
                        <input type="number" step="0.1" min="0" name="kilometers" id="kmKilometers" placeholder="ex: 120.5" required>
                    </div>
                    <div class="km-field" style="grid-column: 1 / -1;">
                        <label>Note</label>
                        <textarea name="note" id="kmNote" placeholder="Optionnel..."></textarea>
                    </div>
                </div>
            </form>
            <div id="kmAddError" style="display:none; margin-top: 10px; background: rgba(255, 167, 38, 0.16); border: 1px solid rgba(255, 167, 38, 0.25); color:#7a3e00; padding: 10px 12px; border-radius: 12px; font-weight: 700;"></div>
        </div>
        <div class="km-modal-actions">
            <button class="action-btn secondary" type="button" onclick="kmCloseAddModal()">
                <i class="fas fa-ban"></i>
                Annuler
            </button>
            <button class="action-btn" type="button" onclick="kmSubmitAdd()">
                <i class="fas fa-check"></i>
                Enregistrer
            </button>
        </div>
    </div>
</div>

<!-- Modal: Add vehicle -->
<div class="km-modal-backdrop" id="kmAddVehicleModal" role="dialog" aria-modal="true">
    <div class="km-modal">
        <div class="km-modal-header">
            <div style="display:flex; align-items:center; gap:10px;">
                <i class="fas fa-car-side" style="color:#2E7D32;"></i>
                <strong>Ajouter un véhicule</strong>
            </div>
            <button class="icon-btn delete" type="button" title="Fermer" onclick="kmCloseAddVehicleModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="km-modal-body">
            <form id="kmAddVehicleForm">
                @csrf
                <div class="km-form-grid">
                    <div class="km-field" style="grid-column: 1 / -1;">
                        <label>Nom</label>
                        <input type="text" name="name" id="kmVName" placeholder="ex: Renault Clio 5" required>
                    </div>
                    <div class="km-field">
                        <label>Matricule</label>
                        <input type="text" name="matricule" id="kmVMatricule" placeholder="ex: 123-ABC-45" required>
                    </div>
                    <div class="km-field">
                        <label>Modèle</label>
                        <input type="text" name="model" id="kmVModel" placeholder="ex: Diesel / Essence" required>
                    </div>
                    <div class="km-field">
                        <label>Année</label>
                        <input type="number" name="year" id="kmVYear" min="2000" max="{{ date('Y') }}" placeholder="{{ date('Y') }}" required>
                    </div>
                    <div class="km-field">
                        <label>Kilométrage (km)</label>
                        <input type="number" name="km" id="kmVKm" min="0" step="1" placeholder="0" required>
                    </div>
                    <div class="km-field" style="grid-column: 1 / -1;">
                        <label>Statut</label>
                        <select name="status" id="kmVStatus" required>
                            <option value="disponible">Disponible</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>
            </form>
            <div id="kmAddVehicleError" style="display:none; margin-top: 10px; background: rgba(255, 167, 38, 0.16); border: 1px solid rgba(255, 167, 38, 0.25); color:#7a3e00; padding: 10px 12px; border-radius: 12px; font-weight: 700;"></div>
        </div>
        <div class="km-modal-actions">
            <button class="action-btn secondary" type="button" onclick="kmCloseAddVehicleModal()">
                <i class="fas fa-ban"></i>
                Annuler
            </button>
            <button class="action-btn" type="button" onclick="kmSubmitAddVehicle()">
                <i class="fas fa-check"></i>
                Enregistrer
            </button>
        </div>
    </div>
</div>

<div class="data-table-container">
    <div class="table-toolbar">
        <div class="search-box">
            <input type="text" placeholder="Rechercher par employé, véhicule, destination...">
        </div>
        <div class="table-filters">
            <select class="filter-select">
                <option value="">Tous les véhicules</option>
                @foreach($demandes->pluck('car')->unique() as $car)
                    @if($car)
                    <option value="{{ $car->name }}">{{ $car->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>

    @if($demandes->count() > 0)
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Véhicule</th>
                        <th>Destination</th>
                        <th>Date de Départ</th>
                        <th>Date de Retour</th>
                        <th>Kilométrage</th>
                        <th>Raison</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($demandes as $demande)
                    <tr>
                        <td>
                            <strong>{{ $demande->user?->name ?? 'N/A' }}</strong>
                        </td>
                        <td>{{ $demande->car?->name ?? 'N/A' }}</td>
                        <td>{{ $demande->destination ?? 'N/A' }}</td>
                        <td>{{ $demande->start_date ? \Carbon\Carbon::parse($demande->start_date)->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $demande->end_date ? \Carbon\Carbon::parse($demande->end_date)->format('d/m/Y') : 'N/A' }}</td>
                        <td>
                            <strong style="color: #f57c00;">{{ number_format($demande->kilometers ?? 0, 1, ',', ' ') }} km</strong>
                        </td>
                        <td>{{ $demande->reason ?? '-' }}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="icon-btn view" title="Voir" onclick="viewKilometrage({{ $demande->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px;">
                            <p style="color: #999;">Aucun enregistrement de kilométrage</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Aucun enregistrement de kilométrage</p>
        </div>
    @endif
</div>

<script>
let kmSelectedCarId = null;
let kmVehiclesCache = [];
let kmVehiclesPollHandle = null;

function kmSetActiveCard(carId) {
    document.querySelectorAll('.km-vehicle-card').forEach((c) => c.classList.remove('active'));
    const active = document.querySelector(`.km-vehicle-card[data-car-id="${carId}"]`);
    if (active) active.classList.add('active');

    document.querySelectorAll('.km-quick-card').forEach((c) => c.classList.remove('active'));
    const quick = document.querySelector(`.km-quick-card[data-quick-car-id="${carId}"]`);
    if (quick) quick.classList.add('active');
}

function kmHandleAllVehiclesSelect(value) {
    const quick = document.getElementById('kmQuickSelect');
    if (!quick) return;

    if (value === 'all') {
        quick.style.display = '';
        quick.scrollIntoView({ behavior: 'smooth', block: 'start' });
        return;
    }

    quick.style.display = 'none';
    const list = document.getElementById('kmVehicleList');
    if (list) list.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function viewKilometrage(demandeId) {
    window.location.href = `{{ route('admin.reservations.index') }}?focus=${demandeId}`;
}

function kmToggleAddMenu() {
    const el = document.getElementById('kmAddMenu');
    if (!el) return;
    el.style.display = (el.style.display === 'none' || !el.style.display) ? 'block' : 'none';
}

function kmHideAddMenu() {
    const el = document.getElementById('kmAddMenu');
    if (!el) return;
    el.style.display = 'none';
}

function kmSelectVehicle(carId) {
    kmSelectedCarId = carId;
    kmSetActiveCard(carId);

    const details = document.getElementById('kmDetails');
    details.innerHTML = `
        <div class="data-table-container">
            <div class="km-details-loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p style="margin:0; font-weight:800; color:#1f2937;">Chargement…</p>
                <p style="margin:10px 0 0 0;">Historique kilométrage du véhicule</p>
            </div>
        </div>
    `;

    fetch(`{{ url('/admin/data/kilometrage/vehicle') }}/${carId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then((r) => r.text())
        .then((html) => { details.innerHTML = html; })
        .catch(() => {
            details.innerHTML = `
                <div class="data-table-container">
                    <div class="km-details-loading">
                        <i class="fas fa-exclamation-circle"></i>
                        <p style="margin:0; font-weight:800; color:#1f2937;">Erreur de chargement</p>
                        <p style="margin:10px 0 0 0;">Veuillez réessayer.</p>
                    </div>
                </div>
            `;
        });
}

function kmOpenAddModal() {
    const modal = document.getElementById('kmAddModal');
    const err = document.getElementById('kmAddError');
    err.style.display = 'none';
    err.textContent = '';

    // Default date to today
    const dateInput = document.getElementById('kmRecordedAt');
    if (dateInput && !dateInput.value) {
        const d = new Date();
        const pad = (n) => String(n).padStart(2, '0');
        dateInput.value = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
    }

    // If a car is selected, preselect it
    const sel = document.getElementById('kmCarSelect');
    if (sel && kmSelectedCarId) sel.value = String(kmSelectedCarId);

    modal.classList.add('open');
}

function kmCloseAddModal() {
    document.getElementById('kmAddModal').classList.remove('open');
}

function kmOpenAddVehicleModal() {
    const modal = document.getElementById('kmAddVehicleModal');
    const err = document.getElementById('kmAddVehicleError');
    err.style.display = 'none';
    err.textContent = '';
    modal.classList.add('open');
}

function kmCloseAddVehicleModal() {
    document.getElementById('kmAddVehicleModal').classList.remove('open');
}

function kmSubmitAdd() {
    const form = document.getElementById('kmAddForm');
    const err = document.getElementById('kmAddError');
    err.style.display = 'none';
    err.textContent = '';

    const formData = new FormData(form);

    fetch(`{{ route('admin.data.kilometrage.entries.store') }}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
        .then(async (r) => {
            if (!r.ok) {
                const data = await r.json().catch(() => null);
                throw data || { message: 'Erreur lors de l’enregistrement.' };
            }
            return r.json();
        })
        .then(() => {
            kmCloseAddModal();
            const carId = formData.get('car_id');
            if (carId) kmSelectVehicle(carId);
        })
        .catch((e) => {
            err.style.display = 'block';
            err.textContent = e?.message || 'Erreur lors de l’enregistrement.';
        });
}

function kmSubmitAddVehicle() {
    const form = document.getElementById('kmAddVehicleForm');
    const err = document.getElementById('kmAddVehicleError');
    err.style.display = 'none';
    err.textContent = '';

    const formData = new FormData(form);

    fetch(`{{ route('data-entry.vehicles.store') }}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(Object.fromEntries(formData.entries()))
    })
        .then(async (r) => {
            const data = await r.json().catch(() => null);
            if (!r.ok || !data?.success) {
                const msg = data?.message || 'Erreur lors de l’enregistrement.';
                throw { message: msg, errors: data?.errors };
            }
            return data;
        })
        .then((data) => {
            kmCloseAddVehicleModal();
            // Force refresh and select the newly created vehicle
            kmRefreshVehicles(true).then(() => {
                const id = data?.data?.id;
                if (id) kmSelectVehicle(id);
            });
        })
        .catch((e) => {
            err.style.display = 'block';
            if (e?.errors) {
                const firstKey = Object.keys(e.errors)[0];
                err.textContent = (e.errors[firstKey] && e.errors[firstKey][0]) ? e.errors[firstKey][0] : (e.message || 'Erreur lors de l’enregistrement.');
                return;
            }
            err.textContent = e?.message || 'Erreur lors de l’enregistrement.';
        });
}

function kmVehicleLabel(v) {
    const name = v?.name || `Véhicule #${v?.id}`;
    const mat = v?.matricule ? `Immat: ${v.matricule}` : 'Immat: —';
    return { name, mat };
}

function kmRenderVehicles(vehicles) {
    const list = document.getElementById('kmVehicleList');
    if (!list) return;

    if (!vehicles.length) {
        list.innerHTML = `
            <div class="empty-state" style="padding: 30px 16px;">
                <i class="fas fa-inbox"></i>
                <p>Aucun véhicule</p>
            </div>
        `;
    } else {
        list.innerHTML = vehicles.map((v) => {
            const l = kmVehicleLabel(v);
            return `
                <div class="km-vehicle-card ${String(v.id) === String(kmSelectedCarId) ? 'active' : ''}" data-car-id="${v.id}" onclick="kmSelectVehicle(${v.id})">
                    <div class="km-vehicle-meta">
                        <div class="km-vehicle-icon"><i class="fas fa-car"></i></div>
                        <div style="min-width:0;">
                            <div class="km-vehicle-name">${l.name}</div>
                            <div class="km-vehicle-sub">${l.mat}</div>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right" style="color: rgba(46,125,50,0.75);"></i>
                </div>
            `;
        }).join('');
    }

    // Also refresh the select in Add KM modal
    const sel = document.getElementById('kmCarSelect');
    if (sel) {
        const current = sel.value;
        sel.innerHTML = `<option value="">Choisir un véhicule...</option>` + vehicles.map((v) => {
            const l = kmVehicleLabel(v);
            return `<option value="${v.id}">${l.name}${v.matricule ? ' — ' + v.matricule : ''}</option>`;
        }).join('');
        if (current) sel.value = current;
    }
}

async function kmRefreshVehicles(force = false) {
    const res = await fetch(`{{ route('data-entry.vehicles.get') }}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    });
    const vehicles = await res.json();
    // DataEntryController returns an array; normalize to array
    const list = Array.isArray(vehicles) ? vehicles : [];

    // sort by name for UI
    list.sort((a, b) => String(a.name || '').localeCompare(String(b.name || ''), 'fr', { sensitivity: 'base' }));

    // update only if changed
    const prev = kmVehiclesCache.map(v => v.id).join(',');
    const next = list.map(v => v.id).join(',');
    if (force || prev !== next) {
        kmVehiclesCache = list;
        kmRenderVehicles(list);
    }

    return list;
}

// Close modal when clicking backdrop
document.addEventListener('click', function (e) {
    const modal = document.getElementById('kmAddModal');
    if (!modal || !modal.classList.contains('open')) return;
    if (e.target === modal) kmCloseAddModal();
});

// Close vehicle modal when clicking backdrop
document.addEventListener('click', function (e) {
    const modal = document.getElementById('kmAddVehicleModal');
    if (!modal || !modal.classList.contains('open')) return;
    if (e.target === modal) kmCloseAddVehicleModal();
});

// Close add menu when clicking outside
document.addEventListener('click', function (e) {
    const menu = document.getElementById('kmAddMenu');
    if (!menu) return;
    if (menu.style.display !== 'block') return;
    if (e.target.closest('#kmAddMenu')) return;
    if (e.target.closest('.action-btn') && e.target.closest('.action-btn').getAttribute('onclick')?.includes('kmToggleAddMenu')) return;
    const toggleBtn = e.target.closest('button');
    if (toggleBtn && toggleBtn.getAttribute('onclick')?.includes('kmToggleAddMenu')) return;
    kmHideAddMenu();
});

// Auto-sync vehicles from DB (polling)
document.addEventListener('DOMContentLoaded', function () {
    // Ensure "Tous les véhicules" shows the 2 selectable options.
    kmHandleAllVehiclesSelect('all');
    kmRefreshVehicles(true);
    if (kmVehiclesPollHandle) clearInterval(kmVehiclesPollHandle);
    kmVehiclesPollHandle = setInterval(() => kmRefreshVehicles(false), 5000);
});
</script>
