<!-- Planning Windows Table -->
<style>
    /* ── Action buttons ── */
    .action-buttons { display: flex; gap: 8px; justify-content: center; align-items: center; }
    .icon-btn {
        width: 34px; height: 34px; border: none; border-radius: 8px; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 14px; transition: all 0.25s ease; background: none; flex-shrink: 0;
    }
    .icon-btn.view   { background: #e8f5e9; color: #2e7d32; }
    .icon-btn.view:hover   { background: #4CAF50; color: white; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(76,175,80,0.3); }
    .icon-btn.edit   { background: #fff8e1; color: #e65100; }
    .icon-btn.edit:hover   { background: #FFA726; color: white; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(255,167,38,0.3); }
    .icon-btn.delete { background: #fce4ec; color: #880e4f; }
    .icon-btn.delete:hover { background: #c2185b; color: white; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(194,24,91,0.3); }

    /* ── Window modal ── */
    .win-modal-bg {
        position: fixed; inset: 0; background: rgba(17,24,39,0.55);
        display: none; align-items: center; justify-content: center;
        z-index: 10000; padding: 18px;
    }
    .win-modal-bg.open { display: flex; }
    .win-modal {
        width: 100%; max-width: 480px; background: #fff;
        border-radius: 16px; overflow: hidden;
        box-shadow: 0 20px 48px rgba(0,0,0,0.22);
        animation: win-in 0.25s ease;
    }
    @keyframes win-in {
        from { opacity: 0; transform: scale(0.95) translateY(-12px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }
    .win-modal-header {
        padding: 16px 18px;
        background: linear-gradient(135deg, rgba(46,125,50,0.08) 0%, rgba(255,167,38,0.10) 100%);
        border-bottom: 1px solid #eef2f7;
        display: flex; justify-content: space-between; align-items: center;
    }
    .win-modal-header .title { font-size: 15px; font-weight: 700; color: #1f2937; display: flex; align-items: center; gap: 10px; }
    .win-modal-header .title i { color: #2E7D32; }
    .win-close {
        width: 32px; height: 32px; border: none; background: rgba(0,0,0,0.06);
        border-radius: 8px; cursor: pointer; font-size: 16px; color: #555;
        display: flex; align-items: center; justify-content: center; transition: all 0.2s;
    }
    .win-close:hover { background: rgba(0,0,0,0.12); }
    .win-modal-body { padding: 20px 18px; }
    .win-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .win-grid .full { grid-column: 1 / -1; }
    .win-field label {
        display: block; font-size: 12px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.5px; color: #6b7280; margin-bottom: 6px;
    }
    .win-field input, .win-field select {
        width: 100%; border: 1.5px solid #e5e7eb; border-radius: 10px;
        padding: 10px 12px; font-size: 14px; outline: none; font-family: inherit;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .win-field input[readonly] { background: #f9fafb; color: #374151; cursor: default; }
    .win-field input:focus, .win-field select:focus {
        border-color: #2E7D32; box-shadow: 0 0 0 3px rgba(46,125,50,0.12);
    }
    .win-error {
        display: none; margin-top: 12px;
        background: rgba(229,57,53,0.10); border: 1px solid rgba(229,57,53,0.25);
        color: #b71c1c; padding: 10px 12px; border-radius: 10px;
        font-size: 13px; font-weight: 600;
    }
    .win-modal-footer {
        display: flex; justify-content: flex-end; gap: 10px;
        padding: 14px 18px; border-top: 1px solid #eef2f7; background: #fafafa;
    }
    .win-btn {
        padding: 9px 18px; border: none; border-radius: 8px; font-size: 13px;
        font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;
    }
    .win-btn-cancel { background: #f3f4f6; color: #374151; }
    .win-btn-cancel:hover { background: #e5e7eb; }
    .win-btn-save { background: linear-gradient(135deg, #2E7D32, #FFA726); color: white; }
    .win-btn-save:hover { opacity: 0.88; transform: translateY(-1px); }

    /* ── View modal info grid ── */
    .win-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .win-info-item label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #9ca3af; margin-bottom: 4px; display: block; }
    .win-info-item .val  { font-size: 14px; font-weight: 600; color: #1f2937; }

    @media (max-width: 480px) {
        .win-grid, .win-info-grid { grid-template-columns: 1fr; }
        .win-grid .full { grid-column: 1; }
    }
</style>

<div class="data-table-container">
    <div class="table-toolbar">
        <div class="search-box">
            <input type="text" id="windowSearch" placeholder="Rechercher par nom..."
                   oninput="filterWindowsTable(this.value)">
        </div>
        <div class="table-filters">
            <select class="filter-select" id="windowStatusFilter" onchange="filterWindowsTable()">
                <option value="">Tous les statuts</option>
                <option value="actif">Actif</option>
                <option value="inactif">Inactif</option>
            </select>
            <button class="action-btn" style="margin: 0;" onclick="openWindowModal()">
                <i class="fas fa-plus"></i> Ajouter
            </button>
        </div>
    </div>

    @if($windows->count() > 0)
        <div class="table-wrapper">
            <table id="windowsTable">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Date de Début</th>
                        <th>Date de Fin</th>
                        <th>Durée</th>
                        <th>Statut</th>
                        <th>Date de Création</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($windows as $window)
                    @php
                        $start = \Carbon\Carbon::parse($window->start_date);
                        $end   = \Carbon\Carbon::parse($window->end_date);
                        $days  = $start->diffInDays($end);
                    @endphp
                    <tr id="window-row-{{ $window->id }}"
                        data-name="{{ e(strtolower($window->name)) }}"
                        data-name-raw="{{ e($window->name) }}"
                        data-start="{{ $start->format('Y-m-d') }}"
                        data-end="{{ $end->format('Y-m-d') }}"
                        data-start-display="{{ $start->format('d/m/Y') }}"
                        data-end-display="{{ $end->format('d/m/Y') }}"
                        data-days="{{ $days }}"
                        data-active="{{ $window->is_active ? '1' : '0' }}"
                        data-active-label="{{ $window->is_active ? 'actif' : 'inactif' }}"
                        data-created="{{ $window->created_at->format('d/m/Y') }}">
                        <td><strong>{{ $window->name }}</strong></td>
                        <td>{{ $start->format('d/m/Y') }}</td>
                        <td>{{ $end->format('d/m/Y') }}</td>
                        <td>
                            <span style="background:#f0f0f0;padding:4px 8px;border-radius:4px;font-size:12px;">
                                {{ $days }} jour(s)
                            </span>
                        </td>
                        <td>
                            @if($window->is_active)
                                <span class="status-badge disponible">Actif</span>
                            @else
                                <span class="status-badge maintenance">Inactif</span>
                            @endif
                        </td>
                        <td>{{ $window->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" class="icon-btn edit" title="Modifier"
                                        onclick="editWindow({{ $window->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="icon-btn view" title="Voir les détails"
                                        onclick="viewWindow({{ $window->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="icon-btn delete" title="Supprimer"
                                        onclick="deleteWindow({{ $window->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;">
                            <p style="color:#999;">Aucune fenêtre de planification</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Aucune fenêtre de planification</p>
        </div>
    @endif
</div>

{{-- ── EDIT / CREATE MODAL ──────────────────────────────────────────────────── --}}
<div id="winEditModal" class="win-modal-bg" role="dialog" aria-modal="true">
    <div class="win-modal">
        <div class="win-modal-header">
            <div class="title"><i class="fas fa-window-maximize"></i> <span id="winModalTitle">Ajouter une fenêtre</span></div>
            <button type="button" class="win-close" onclick="closeWinModal('winEditModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="win-modal-body">
            <div id="winEditForm">
                <input type="hidden" id="winEditId">
                <div class="win-grid">
                    <div class="win-field full">
                        <label>Nom de la fenêtre</label>
                        <input type="text" id="winName" required placeholder="Ex: Fenêtre Janvier 2025"
                               onkeydown="if(event.key==='Enter'){event.preventDefault();submitWinModal();}">
                    </div>
                    <div class="win-field">
                        <label>Date de début</label>
                        <input type="date" id="winStart" required>
                    </div>
                    <div class="win-field">
                        <label>Date de fin</label>
                        <input type="date" id="winEnd" required>
                    </div>
                    <div class="win-field full">
                        <label>Statut</label>
                        <select id="winActive">
                            <option value="1">Actif</option>
                            <option value="0">Inactif</option>
                        </select>
                    </div>
                </div>
                <div class="win-error" id="winEditError"></div>
            </div>
        </div>
        <div class="win-modal-footer">
            <button type="button" class="win-btn win-btn-cancel" onclick="closeWinModal('winEditModal')">
                <i class="fas fa-times"></i> Annuler
            </button>
            <button type="button" class="win-btn win-btn-save" onclick="submitWinModal()">
                <i class="fas fa-save"></i> Enregistrer
            </button>
        </div>
    </div>
</div>

{{-- ── VIEW MODAL ───────────────────────────────────────────────────────────── --}}
<div id="winViewModal" class="win-modal-bg" role="dialog" aria-modal="true">
    <div class="win-modal">
        <div class="win-modal-header">
            <div class="title"><i class="fas fa-calendar-alt"></i> <span id="winViewTitle">Détails</span></div>
            <button type="button" class="win-close" onclick="closeWinModal('winViewModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="win-modal-body">
            <div class="win-info-grid">
                <div class="win-info-item" style="grid-column:1/-1;">
                    <label>Nom</label>
                    <div class="val" id="viewWinName">—</div>
                </div>
                <div class="win-info-item">
                    <label>Date de début</label>
                    <div class="val" id="viewWinStart">—</div>
                </div>
                <div class="win-info-item">
                    <label>Date de fin</label>
                    <div class="val" id="viewWinEnd">—</div>
                </div>
                <div class="win-info-item">
                    <label>Durée</label>
                    <div class="val" id="viewWinDays">—</div>
                </div>
                <div class="win-info-item">
                    <label>Statut</label>
                    <div class="val" id="viewWinActive">—</div>
                </div>
                <div class="win-info-item">
                    <label>Créé le</label>
                    <div class="val" id="viewWinCreated">—</div>
                </div>
            </div>
        </div>
        <div class="win-modal-footer">
            <button type="button" class="win-btn win-btn-cancel" onclick="closeWinModal('winViewModal')">
                <i class="fas fa-times"></i> Fermer
            </button>
        </div>
    </div>
</div>

<script>
const WINDOWS_CSRF = '{{ csrf_token() }}';
let winMode = 'create'; // 'create' | 'edit'

// ── Filter ────────────────────────────────────────────────────────────────────
function filterWindowsTable(searchVal) {
    const q      = (searchVal ?? document.getElementById('windowSearch')?.value ?? '').toLowerCase();
    const status = document.getElementById('windowStatusFilter')?.value ?? '';
    document.querySelectorAll('#windowsTable tbody tr[id^="window-row-"]').forEach(row => {
        const matchQ = !q      || (row.dataset.name ?? '').includes(q);
        const matchS = !status || (row.dataset.activeLabel ?? '') === status;
        row.style.display = (matchQ && matchS) ? '' : 'none';
    });
}

// ── Modal helpers ─────────────────────────────────────────────────────────────
function openWinModal(id)  { document.getElementById(id).classList.add('open'); }
function closeWinModal(id) { document.getElementById(id).classList.remove('open'); }

['winEditModal','winViewModal'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('click', e => { if (e.target === el) closeWinModal(id); });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeWinModal('winEditModal'); closeWinModal('winViewModal'); }
});

// ── Open CREATE modal ─────────────────────────────────────────────────────────
function openWindowModal() {
    winMode = 'create';
    document.getElementById('winModalTitle').textContent = 'Ajouter une fenêtre';
    document.getElementById('winEditId').value  = '';
    document.getElementById('winName').value    = '';
    document.getElementById('winStart').value   = '';
    document.getElementById('winEnd').value     = '';
    document.getElementById('winActive').value  = '1';
    document.getElementById('winEditError').style.display = 'none';
    openWinModal('winEditModal');
}

// ── Open EDIT modal ───────────────────────────────────────────────────────────
function editWindow(id) {
    const row = document.getElementById(`window-row-${id}`);
    if (!row) return;
    winMode = 'edit';
    document.getElementById('winModalTitle').textContent = 'Modifier la fenêtre';
    document.getElementById('winEditId').value  = id;
    document.getElementById('winName').value    = row.dataset.nameRaw  ?? '';
    document.getElementById('winStart').value   = row.dataset.start    ?? '';
    document.getElementById('winEnd').value     = row.dataset.end      ?? '';
    document.getElementById('winActive').value  = row.dataset.active   ?? '1';
    document.getElementById('winEditError').style.display = 'none';
    openWinModal('winEditModal');
}

// ── Open VIEW modal ───────────────────────────────────────────────────────────
function viewWindow(id) {
    const row = document.getElementById(`window-row-${id}`);
    if (!row) return;
    document.getElementById('winViewTitle').textContent  = row.dataset.nameRaw ?? '—';
    document.getElementById('viewWinName').textContent   = row.dataset.nameRaw ?? '—';
    document.getElementById('viewWinStart').textContent  = row.dataset.startDisplay ?? '—';
    document.getElementById('viewWinEnd').textContent    = row.dataset.endDisplay   ?? '—';
    document.getElementById('viewWinDays').textContent   = (row.dataset.days ?? '?') + ' jour(s)';
    document.getElementById('viewWinActive').textContent = row.dataset.active === '1' ? '✅ Actif' : '❌ Inactif';
    document.getElementById('viewWinCreated').textContent= row.dataset.created ?? '—';
    openWinModal('winViewModal');
}

// ── Submit CREATE / EDIT ──────────────────────────────────────────────────────
async function submitWinModal() {
    const errEl = document.getElementById('winEditError');
    errEl.style.display = 'none';
    errEl.textContent   = '';

    const id      = document.getElementById('winEditId').value;
    const payload = {
        name:       document.getElementById('winName').value.trim(),
        start_date: document.getElementById('winStart').value,
        end_date:   document.getElementById('winEnd').value,
        is_active:  document.getElementById('winActive').value === '1',
    };

    if (!payload.name || !payload.start_date || !payload.end_date) {
        errEl.textContent   = 'Veuillez remplir tous les champs obligatoires.';
        errEl.style.display = 'block';
        return;
    }
    if (payload.start_date > payload.end_date) {
        errEl.textContent   = 'La date de début doit être antérieure à la date de fin.';
        errEl.style.display = 'block';
        return;
    }

    const url    = winMode === 'create'
        ? '{{ url("/planification/windows") }}'
        : `{{ url("/planification/windows") }}/${id}`;
    const method = winMode === 'create' ? 'POST' : 'PUT';

    try {
        const res  = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': WINDOWS_CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json().catch(() => ({}));

        if (!res.ok || data?.success === false) {
            const firstErr = data.errors ? Object.values(data.errors)[0]?.[0] : null;
            throw new Error(firstErr || data.message || 'Erreur lors de l\'enregistrement.');
        }

        closeWinModal('winEditModal');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: winMode === 'create' ? 'Créée' : 'Mise à jour',
                text: data.message || `Fenêtre "${payload.name}" enregistrée.`,
                timer: 2000, showConfirmButton: false,
            }).then(() => window.location.reload());
        } else {
            window.location.reload();
        }
    } catch (err) {
        errEl.textContent   = err.message || 'Erreur inattendue.';
        errEl.style.display = 'block';
    }
}

// ── DELETE ────────────────────────────────────────────────────────────────────
function deleteWindow(id) {
    const row  = document.getElementById(`window-row-${id}`);
    const name = row?.dataset.nameRaw || `#${id}`;

    const confirmFn = typeof Swal !== 'undefined'
        ? () => Swal.fire({
            icon: 'warning',
            title: 'Supprimer cette fenêtre ?',
            html: `<div style="text-align:left;margin:14px 0;font-size:14px;">
                       <p style="margin:6px 0;"><strong>Nom :</strong> ${name}</p>
                       <p style="margin:14px 0 0;color:#c62828;font-size:13px;font-weight:600;">
                           <i class="fas fa-exclamation-triangle"></i> Action irréversible.
                       </p>
                   </div>`,
            confirmButtonText: '<i class="fas fa-trash"></i> Supprimer',
            cancelButtonText:  'Annuler',
            confirmButtonColor: '#c62828',
            cancelButtonColor:  '#757575',
            showCancelButton:  true,
            focusCancel:       true,
        })
        : () => Promise.resolve({ isConfirmed: confirm(`Supprimer "${name}" ?`) });

    confirmFn().then(async result => {
        if (!result.isConfirmed) return;
        try {
            const res  = await fetch(`{{ url('/planification/windows') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': WINDOWS_CSRF,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(data.message || 'Erreur lors de la suppression.');

            if (row) {
                row.style.transition = 'opacity 0.35s, transform 0.35s';
                row.style.opacity    = '0';
                row.style.transform  = 'translateX(20px)';
                setTimeout(() => row.remove(), 380);
            }
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'success', title: 'Supprimée', text: `"${name}" supprimée.`, timer: 2000, showConfirmButton: false });
            }
        } catch (e) {
            typeof Swal !== 'undefined'
                ? Swal.fire({ icon: 'error', title: 'Erreur', text: e.message })
                : alert(e.message);
        }
    });
}
</script>
