<!-- ARCHIVED: Employees Table (original content from admin/tables/employees.blade.php) -->
<!- - Original file archived to preserve reference - ->
<!--
COPY of admin/tables/employees.blade.php archived here.
For rollback, restore this file to resources/views/admin/tables/employees.blade.php
-->

<!-- Employees Table -->
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
			<input type="text" placeholder="Rechercher par nom, email, service...">
		</div>
		<div class="table-filters">
			<select class="filter-select">
				<option value="">Tous les services</option>
				<option value="Commerciale">Commerciale</option>
				<option value="Technique">Technique</option>
				<option value="Moyens Généraux">Moyens Généraux</option>
				<option value="RH">RH</option>
				<option value="Finance">Finance</option>
			</select>
			<button class="action-btn" style="margin: 0;" onclick="openEmployeeModal()">
				<i class="fas fa-plus"></i> Ajouter
			</button>
		</div>
	</div>

	@if($employees->count() > 0)
		<div class="table-wrapper">
			<table>
				<thead>
					<tr>
						<th>Nom</th>
						<th>Email</th>
						<th>Service</th>
						<th>Rôle</th>
						<th>Zone de Planification</th>
						<th>Date d'Inscription</th>
						<th style="text-align: center;">Actions</th>
					</tr>
				</thead>
				<tbody>
					@forelse($employees as $employee)
					<tr id="employee-row-{{ $employee->id }}"
						data-id="{{ $employee->id }}"
						data-name="{{ e($employee->name) }}"
						data-email="{{ e($employee->email) }}"
						data-service="{{ e($employee->service ?? '') }}"
						data-role="{{ $employee->hasRole('admin') ? 'admin' : 'employee' }}">
						<td>
							<strong>{{ $employee->name }}</strong>
						</td>
						<td>{{ $employee->email }}</td>
						<td>
							<span style="background: #f0f0f0; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
								{{ $employee->service ?? 'N/A' }}
							</span>
						</td>
						<td>
							@if($employee->hasRole('admin'))
								<span class="status-badge approved">Admin</span>
							@elseif($employee->hasRole('employee'))
								<span class="status-badge disponible">Employé</span>
							@else
								<span class="status-badge">Utilisateur</span>
							@endif
						</td>
						<td>
							{{ $employee->planningZone?->name ?? 'Non assignée' }}
						</td>
						<td>{{ $employee->created_at->format('d/m/Y') }}</td>
						<td>
							<div class="action-buttons">
								<button class="icon-btn edit" title="Modifier" onclick="editEmployee({{ $employee->id }})">
									<i class="fas fa-edit"></i>
								</button>
								<button class="icon-btn view" title="Voir" onclick="viewEmployee({{ $employee->id }})">
									<i class="fas fa-eye"></i>
								</button>
								<button class="icon-btn delete" title="Supprimer" onclick="deleteEmployee({{ $employee->id }})">
									<i class="fas fa-trash"></i>
								</button>
							</div>
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="7" style="text-align: center; padding: 40px;">
							<p style="color: #999;">Aucun employé trouvé</p>
						</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	@else
		<div class="empty-state">
			<i class="fas fa-inbox"></i>
			<p>Aucun employé dans le système</p>
		</div>
	@endif
</div>

<script>
const EMPLOYEE_CSRF = '{{ csrf_token() }}';
let employeeModalMode = 'create';
let employeeEditingId = null;

function employeeRowData(id) {
	const row = document.getElementById(`employee-row-${id}`);
	if (!row) return null;
	return {
		id: row.getAttribute('data-id'),
		name: row.getAttribute('data-name') || '',
		email: row.getAttribute('data-email') || '',
		service: row.getAttribute('data-service') || '',
		role: row.getAttribute('data-role') || 'employee',
	};
}

function ensureEmployeeModal() {
	if (document.getElementById('employeeModal')) return;
	const modal = document.createElement('div');
	modal.id = 'employeeModal';
	modal.className = 'km-modal-backdrop';
	modal.innerHTML = `...`;
	document.body.appendChild(modal);
	modal.addEventListener('click', (e) => { if (e.target === modal) closeEmployeeModal(); });
}

function openEmployeeModal() { /* archived helper */ }
function editEmployee(id) { /* archived helper */ }
function viewEmployee(id) { editEmployee(id); }
function closeEmployeeModal() { document.getElementById('employeeModal')?.classList.remove('open'); }
async function submitEmployeeModal() { /* archived helper */ }
async function deleteEmployee(id) { /* archived helper */ }
</script>
