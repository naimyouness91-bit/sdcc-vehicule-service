<!-- Users (System) Table -->
<style>
    /* ── Icon action buttons ─────────────────────────────────── */
    .action-buttons { display: flex; gap: 8px; justify-content: center; align-items: center; }
    .icon-btn {
        width: 34px; height: 34px; border: none; border-radius: 8px; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 14px; transition: all 0.25s ease; background: none; flex-shrink: 0;
    }
    .icon-btn:disabled { opacity: 0.35; cursor: not-allowed; pointer-events: none; }
    .icon-btn.view   { background: #e8f5e9; color: #2e7d32; }
    .icon-btn.view:hover   { background: #4CAF50; color: white; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(76,175,80,0.3); }
    .icon-btn.edit   { background: #fff8e1; color: #e65100; }
    .icon-btn.edit:hover   { background: #FFA726; color: white; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(255,167,38,0.3); }
    .icon-btn.delete { background: #fce4ec; color: #880e4f; }
    .icon-btn.delete:hover { background: #c2185b; color: white; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(194,24,91,0.3); }

    /* ── Modal backdrop ──────────────────────────────────────── */
    .usr-modal-bg {
        position: fixed; inset: 0; background: rgba(17,24,39,0.55);
        display: none; align-items: center; justify-content: center;
        z-index: 10000; padding: 18px;
    }
    .usr-modal-bg.open { display: flex; }
    .usr-modal {
        width: 100%; max-width: 520px; background: #fff;
        border-radius: 16px; overflow: hidden;
        box-shadow: 0 20px 48px rgba(0,0,0,0.22);
        animation: usr-modal-in 0.25s ease;
    }
    @keyframes usr-modal-in {
        from { opacity: 0; transform: scale(0.95) translateY(-12px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }
    .usr-modal-header {
        padding: 16px 18px;
        background: linear-gradient(135deg, rgba(46,125,50,0.08) 0%, rgba(255,167,38,0.10) 100%);
        border-bottom: 1px solid #eef2f7;
        display: flex; justify-content: space-between; align-items: center; gap: 10px;
    }
    .usr-modal-header .title { font-size: 15px; font-weight: 700; color: #1f2937; display: flex; align-items: center; gap: 10px; }
    .usr-modal-header .title i { color: #2E7D32; }
    .usr-modal-close {
        width: 32px; height: 32px; border: none; background: rgba(0,0,0,0.06);
        border-radius: 8px; cursor: pointer; font-size: 16px; color: #555;
        display: flex; align-items: center; justify-content: center; transition: all 0.2s;
    }
    .usr-modal-close:hover { background: rgba(0,0,0,0.12); color: #111; }
    .usr-modal-body { padding: 20px 18px; }
    .usr-field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .usr-field-grid .full { grid-column: 1 / -1; }
    .usr-field label {
        display: block; font-size: 12px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.5px; color: #6b7280; margin-bottom: 6px;
    }
    .usr-field input, .usr-field select {
        width: 100%; border: 1.5px solid #e5e7eb; border-radius: 10px;
        padding: 10px 12px; font-size: 14px; outline: none; font-family: inherit;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .usr-field input:focus, .usr-field select:focus {
        border-color: #2E7D32; box-shadow: 0 0 0 3px rgba(46,125,50,0.12);
    }
    .usr-field input[readonly] { background: #f9fafb; color: #374151; cursor: default; }
    .usr-modal-error {
        display: none; margin-top: 12px;
        background: rgba(229,57,53,0.10); border: 1px solid rgba(229,57,53,0.25);
        color: #b71c1c; padding: 10px 12px; border-radius: 10px; font-size: 13px; font-weight: 600;
    }
    .usr-modal-footer {
        display: flex; justify-content: flex-end; gap: 10px;
        padding: 14px 18px; border-top: 1px solid #eef2f7; background: #fafafa;
    }
    .usr-btn {
        padding: 9px 18px; border: none; border-radius: 8px; font-size: 13px;
        font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
        transition: all 0.2s;
    }
    .usr-btn-cancel { background: #f3f4f6; color: #374151; }
    .usr-btn-cancel:hover { background: #e5e7eb; }
    .usr-btn-save { background: linear-gradient(135deg, #2E7D32, #FFA726); color: white; }
    .usr-btn-save:hover { opacity: 0.88; transform: translateY(-1px); }

    /* ── View modal fields ───────────────────────────────────── */
    .usr-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .usr-info-item label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #9ca3af; margin-bottom: 4px; display: block; }
    .usr-info-item .val { font-size: 14px; font-weight: 600; color: #1f2937; }

    @media (max-width: 520px) {
        .usr-field-grid, .usr-info-grid { grid-template-columns: 1fr; }
        .usr-field-grid .full { grid-column: 1; }
    }
</style>

<div class="data-table-container">
    <div class="table-toolbar">
        <div class="search-box">
            <input type="text" id="usersSearch" placeholder="Rechercher par nom, email..."
                   oninput="filterUsersTable(this.value)">
        </div>
        <div class="table-filters">
            <select class="filter-select" id="usersRoleFilter" onchange="filterUsersTable()">
                <option value="">Tous les rôles</option>
                <option value="admin">Admin</option>
                <option value="employee">Employé</option>
                <option value="super_admin">Super Admin</option>
            </select>
        </div>
    </div>

    @if($users->count() > 0)
        <div class="table-wrapper">
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle(s)</th>
                        <th>Service</th>
                        <th>Zone</th>
                        <th>Inscription</th>
                        <th>Dernière Connexion</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr id="user-row-{{ $user->id }}"
                        data-name="{{ e($user->name) }}"
                        data-email="{{ e($user->email) }}"
                        data-service="{{ e($user->service ?? '') }}"
                        data-zone="{{ e($user->planningZone?->name ?? '') }}"
                        data-role="{{ e(strtolower($user->roles->pluck('name')->join(','))) }}"
                        data-role-primary="{{ e($user->roles->first()?->name ?? 'employee') }}"
                        data-created="{{ $user->created_at->format('d/m/Y') }}"
                        data-last-login="{{ $user->last_login_at?->diffForHumans() ?? 'Jamais' }}">
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @forelse($user->roles as $role)
                                <span style="background:#e8f5e9;color:#2E7D32;padding:4px 8px;border-radius:4px;font-size:11px;margin-right:4px;text-transform:uppercase;font-weight:600;">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span style="color:#999;">-</span>
                            @endforelse
                        </td>
                        <td>{{ $user->service ?? 'N/A' }}</td>
                        <td>{{ $user->planningZone?->name ?? 'N/A' }}</td>
                        <td>
                            <span style="background:#e3f2fd;color:#1976d2;padding:4px 8px;border-radius:4px;font-size:11px;">
                                {{ $user->created_at->format('d/m/Y') }}
                            </span>
                        </td>
                        <td>{{ $user->last_login_at?->diffForHumans() ?? 'Jamais' }}</td>
                        <td>
                            <div class="action-buttons">
                                {{-- EDIT → opens inline modal, NO navigation --}}
                                <button type="button"
                                        class="icon-btn edit usr-edit-btn"
                                        title="Modifier cet utilisateur"
                                        data-id="{{ $user->id }}"
                                        data-row="user-row-{{ $user->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- VIEW → opens detail modal, NO navigation --}}
                                <button type="button"
                                        class="icon-btn view usr-view-btn"
                                        title="Voir le profil"
                                        data-id="{{ $user->id }}"
                                        data-row="user-row-{{ $user->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>

                                {{-- DELETE --}}
                                @if($user->id !== auth()->id())
                                <button type="button"
                                        class="icon-btn delete usr-delete-btn"
                                        title="Supprimer cet utilisateur"
                                        data-id="{{ $user->id }}"
                                        data-row="user-row-{{ $user->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @else
                                <button class="icon-btn delete" disabled title="Vous ne pouvez pas supprimer votre propre compte">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px;">
                            <p style="color:#999;">Aucun utilisateur système</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Aucun utilisateur dans le système</p>
        </div>
    @endif
</div>

{{-- ── EDIT MODAL ───────────────────────────────────────────────────────────── --}}
<div id="usrEditModal" class="usr-modal-bg" role="dialog" aria-modal="true">
    <div class="usr-modal">
        <div class="usr-modal-header">
            <div class="title"><i class="fas fa-user-edit"></i> Modifier l'utilisateur</div>
            <button type="button" class="usr-modal-close" onclick="closeUsrModal('usrEditModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="usr-modal-body">
            <div id="usrEditForm">
                <input type="hidden" id="editUserId">
                <div class="usr-field-grid">
                    <div class="usr-field full">
                        <label>Nom complet</label>
                        <input type="text" id="editUserName" required placeholder="Nom complet"
                               onkeydown="if(event.key==='Enter'){event.preventDefault();submitUsrEdit();}">
                    </div>
                    <div class="usr-field full">
                        <label>Email</label>
                        <input type="email" id="editUserEmail" required placeholder="email@example.com"
                               onkeydown="if(event.key==='Enter'){event.preventDefault();submitUsrEdit();}">
                    </div>
                    <div class="usr-field">
                        <label>Service</label>
                        @include('utilisateurs.partials.service-dropdown', ['selectId' => 'editUserService', 'selectName' => 'service', 'required' => true])
                    </div>
                    <div class="usr-field">
                        <label>Rôle</label>
                        <select id="editUserRole" required>
                            <option value="employee">Employé</option>
                            <option value="admin">Admin</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                </div>
                <div class="usr-modal-error" id="usrEditError"></div>
            </div>
        </div>
        <div class="usr-modal-footer">
            <button type="button" class="usr-btn usr-btn-cancel" onclick="closeUsrModal('usrEditModal')">
                <i class="fas fa-times"></i> Annuler
            </button>
            <button type="button" class="usr-btn usr-btn-save" onclick="submitUsrEdit()">
                <i class="fas fa-save"></i> Enregistrer
            </button>
        </div>
    </div>
</div>

{{-- ── VIEW MODAL ───────────────────────────────────────────────────────────── --}}
<div id="usrViewModal" class="usr-modal-bg" role="dialog" aria-modal="true">
    <div class="usr-modal">
        <div class="usr-modal-header">
            <div class="title"><i class="fas fa-user"></i> <span id="viewModalTitle">Profil utilisateur</span></div>
            <button type="button" class="usr-modal-close" onclick="closeUsrModal('usrViewModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="usr-modal-body">
            <div class="usr-info-grid">
                <div class="usr-info-item">
                    <label>Nom</label>
                    <div class="val" id="viewUserName">—</div>
                </div>
                <div class="usr-info-item">
                    <label>Email</label>
                    <div class="val" id="viewUserEmail">—</div>
                </div>
                <div class="usr-info-item">
                    <label>Service</label>
                    <div class="val" id="viewUserService">—</div>
                </div>
                <div class="usr-info-item">
                    <label>Zone</label>
                    <div class="val" id="viewUserZone">—</div>
                </div>
                <div class="usr-info-item">
                    <label>Rôle</label>
                    <div class="val" id="viewUserRole">—</div>
                </div>
                <div class="usr-info-item">
                    <label>Inscrit le</label>
                    <div class="val" id="viewUserCreated">—</div>
                </div>
                <div class="usr-info-item">
                    <label>Dernière connexion</label>
                    <div class="val" id="viewUserLogin">—</div>
                </div>
            </div>
        </div>
        <div class="usr-modal-footer">
            <button type="button" class="usr-btn usr-btn-cancel" onclick="closeUsrModal('usrViewModal')">
                <i class="fas fa-times"></i> Fermer
            </button>
        </div>
    </div>
</div>

<script>
const USERS_CSRF = '{{ csrf_token() }}';

// ── Client-side search + role filter ─────────────────────────────────────────
function filterUsersTable(searchVal) {
    const query = (searchVal ?? document.getElementById('usersSearch')?.value ?? '').toLowerCase();
    const role  = document.getElementById('usersRoleFilter')?.value?.toLowerCase() ?? '';
    document.querySelectorAll('#usersTable tbody tr[id^="user-row-"]').forEach(row => {
        const nameEmail = ((row.dataset.name ?? '') + ' ' + (row.dataset.email ?? '')).toLowerCase();
        const rowRole   = (row.dataset.role ?? '').toLowerCase();
        const matchS = !query || nameEmail.includes(query);
        const matchR = !role  || rowRole.includes(role);
        row.style.display = (matchS && matchR) ? '' : 'none';
    });
}

// ── Modal open / close helpers ────────────────────────────────────────────────
function openUsrModal(id)  { document.getElementById(id).classList.add('open'); }
function closeUsrModal(id) { document.getElementById(id).classList.remove('open'); }

// Close on backdrop click
['usrEditModal','usrViewModal'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('click', e => { if (e.target === el) closeUsrModal(id); });
});

// Close on Escape key
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeUsrModal('usrEditModal');
        closeUsrModal('usrViewModal');
    }
});

// ── EDIT button ───────────────────────────────────────────────────────────────
document.querySelectorAll('.usr-edit-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();          // stop any parent link navigation
        e.stopPropagation();
        const row = document.getElementById(this.dataset.row);
        if (!row) return;

        document.getElementById('editUserId').value    = this.dataset.id;
        document.getElementById('editUserName').value  = row.dataset.name  ?? '';
        document.getElementById('editUserEmail').value = row.dataset.email ?? '';
        document.getElementById('editUserService').value = row.dataset.service ?? '';
        document.getElementById('editUserRole').value  = row.dataset.rolePrimary ?? 'employee';
        document.getElementById('usrEditError').style.display = 'none';

        openUsrModal('usrEditModal');
    });
});

// ── VIEW button ───────────────────────────────────────────────────────────────
document.querySelectorAll('.usr-view-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const row = document.getElementById(this.dataset.row);
        if (!row) return;

        document.getElementById('viewModalTitle').textContent = row.dataset.name ?? '—';
        document.getElementById('viewUserName').textContent    = row.dataset.name    ?? '—';
        document.getElementById('viewUserEmail').textContent   = row.dataset.email   ?? '—';
        document.getElementById('viewUserService').textContent = row.dataset.service || 'N/A';
        document.getElementById('viewUserZone').textContent    = row.dataset.zone    || 'N/A';
        document.getElementById('viewUserRole').textContent    = row.dataset.rolePrimary ?? '—';
        document.getElementById('viewUserCreated').textContent = row.dataset.created ?? '—';
        document.getElementById('viewUserLogin').textContent   = row.dataset.lastLogin ?? 'Jamais';

        openUsrModal('usrViewModal');
    });
});

// ── Submit EDIT form ──────────────────────────────────────────────────────────
async function submitUsrEdit() {
    const errEl = document.getElementById('usrEditError');
    errEl.style.display = 'none';
    errEl.textContent   = '';

    const id      = document.getElementById('editUserId').value;
    const payload = {
        name:    document.getElementById('editUserName').value.trim(),
        email:   document.getElementById('editUserEmail').value.trim(),
        service: document.getElementById('editUserService').value.trim(),
        role:    document.getElementById('editUserRole').value,
    };

    if (!payload.name || !payload.email || !payload.role) {
        errEl.textContent   = 'Veuillez remplir tous les champs obligatoires.';
        errEl.style.display = 'block';
        return;
    }

    try {
        const res  = await fetch(`{{ url('/admin/data/users') }}/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': USERS_CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            const firstErr = data.errors ? Object.values(data.errors)[0]?.[0] : null;
            throw new Error(firstErr || data.message || 'Erreur lors de la mise à jour.');
        }

        // Update data attributes in the DOM row
        const row = document.querySelector(`tr[id="user-row-${id}"]`);
        if (row) {
            row.dataset.name        = payload.name;
            row.dataset.email       = payload.email;
            row.dataset.service     = payload.service;
            row.dataset.rolePrimary = payload.role;
            row.dataset.role        = payload.role;

            // Update visible cells
            const cells = row.querySelectorAll('td');
            if (cells[0]) cells[0].innerHTML = `<strong>${payload.name}</strong>`;
            if (cells[1]) cells[1].textContent = payload.email;
            if (cells[3]) cells[3].textContent = payload.service || 'N/A';
        }

        closeUsrModal('usrEditModal');

        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'success', title: 'Mis à jour', text: `Utilisateur "${payload.name}" modifié.`, timer: 2000, showConfirmButton: false });
        }
    } catch (err) {
        errEl.textContent   = err.message || 'Erreur inattendue.';
        errEl.style.display = 'block';
    }
}

// ── DELETE button ─────────────────────────────────────────────────────────────
document.querySelectorAll('.usr-delete-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const id   = this.dataset.id;
        const row  = document.getElementById(this.dataset.row);
        const name = row?.dataset.name ?? `#${id}`;
        const email= row?.dataset.email ?? '';

        const confirmFn = typeof Swal !== 'undefined'
            ? () => Swal.fire({
                icon: 'warning',
                title: 'Supprimer cet utilisateur ?',
                html: `<div style="text-align:left;margin:14px 0;font-size:14px;">
                           <p style="margin:6px 0;"><strong>Nom :</strong> ${name}</p>
                           <p style="margin:6px 0;"><strong>Email :</strong> ${email}</p>
                           <p style="margin:14px 0 0;color:#c62828;font-size:13px;font-weight:600;">
                               <i class="fas fa-exclamation-triangle"></i> Action irréversible.
                           </p>
                       </div>`,
                confirmButtonText: '<i class="fas fa-trash"></i> Supprimer',
                cancelButtonText:  'Annuler',
                confirmButtonColor: '#c62828',
                cancelButtonColor:  '#757575',
                showCancelButton: true,
                focusCancel: true,
            })
            : () => Promise.resolve({ isConfirmed: confirm(`Supprimer "${name}" ?`) });

        confirmFn().then(async result => {
            if (!result.isConfirmed) return;
            try {
                const res  = await fetch(`{{ url('/admin/data/users') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': USERS_CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await res.json().catch(() => ({}));

                if (!res.ok && !data.success) {
                    throw new Error(data.message || 'Erreur lors de la suppression.');
                }

                if (row) {
                    row.style.transition = 'opacity 0.35s, transform 0.35s';
                    row.style.opacity    = '0';
                    row.style.transform  = 'translateX(20px)';
                    setTimeout(() => row.remove(), 380);
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Supprimé', text: `"${name}" supprimé.`, timer: 2000, showConfirmButton: false });
                }
            } catch (err) {
                typeof Swal !== 'undefined'
                    ? Swal.fire({ icon: 'error', title: 'Erreur', text: err.message })
                    : alert(err.message);
            }
        });
    });
});
</script>
