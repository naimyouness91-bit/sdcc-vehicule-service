<!-- ARCHIVED: data-entry/tabs/employees.blade.php -->
<!-- Original file content archived below. Restore to resources/views/admin/data-entry/tabs/employees.blade.php if needed. -->

<!-- Employees Table -->
<div class="data-table-wrapper">
	<div class="table-controls">
		<input type="text" id="employeesSearch" class="search-input" placeholder="🔍 Rechercher employé...">
		<div class="table-info">
			<span id="employeesCount">Total: 0</span>
		</div>
	</div>

	<div class="table-responsive">
		<table class="data-table" id="employeesTable">
			<thead>
				<tr>
					<th>Nom</th>
					<th>Email</th>
					<th>Service</th>
					<th>Rôle</th>
					<th>Date Création</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody id="employeesBody">
				<tr class="empty-row">
					<td colspan="6">Chargement...</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="pagination" id="employeesPagination"></div>
</div>

<style>
	.data-table-wrapper {
		display: flex;
		flex-direction: column;
		gap: 15px;
	}

	.table-controls {
		display: flex;
		justify-content: space-between;
		align-items: center;
		gap: 15px;
	}

	.search-input {
		flex: 1;
		padding: 10px 15px;
		border: 1px solid #ddd;
		border-radius: 6px;
		font-size: 14px;
		transition: border-color 0.3s ease;
	}

	.search-input:focus {
		outline: none;
		border-color: #4CAF50;
		box-shadow: 0 0 5px rgba(76, 175, 80, 0.2);
	}

	.table-info {
		font-size: 13px;
		color: #666;
		white-space: nowrap;
	}

	.table-responsive {
		overflow-x: auto;
		border: 1px solid #ddd;
		border-radius: 6px;
	}

	.data-table {
		width: 100%;
		border-collapse: collapse;
		background: white;
	}

	.data-table thead {
		background: linear-gradient(90deg, #2c3e50, #34495e);
		color: white;
		position: sticky;
		top: 0;
		z-index: 10;
	}

	.data-table th {
		padding: 12px 15px;
		text-align: left;
		font-weight: 600;
		font-size: 13px;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}

	.data-table tbody tr {
		border-bottom: 1px solid #ecf0f1;
		transition: background 0.3s ease;
	}

	.data-table tbody tr:hover {
		background: #f8f9fa;
	}

	.data-table td {
		padding: 12px 15px;
		font-size: 13px;
	}

	.role-badge {
		display: inline-block;
		padding: 4px 10px;
		border-radius: 4px;
		font-size: 12px;
		font-weight: 600;
		background: #e3f2fd;
		color: #1976d2;
	}

	.role-badge.admin {
		background: #ffe0b2;
		color: #f57c00;
	}

	.action-buttons {
		display: flex;
		gap: 8px;
	}

	.btn-action {
		padding: 6px 10px;
		border: none;
		border-radius: 4px;
		cursor: pointer;
		font-size: 12px;
		font-weight: 600;
		transition: all 0.3s ease;
	}

	.btn-edit {
		background: #1976d2;
		color: white;
	}

	.btn-edit:hover {
		background: #1565c0;
	}

	.btn-delete {
		background: #d32f2f;
		color: white;
	}

	.btn-delete:hover {
		background: #c62828;
	}

	.empty-row td {
		text-align: center;
		color: #999;
		padding: 30px;
	}

	.pagination {
		display: flex;
		justify-content: center;
		gap: 5px;
		flex-wrap: wrap;
	}

	.pagination button {
		padding: 6px 10px;
		border: 1px solid #ddd;
		background: white;
		color: #2c3e50;
		border-radius: 4px;
		cursor: pointer;
		font-size: 12px;
		transition: all 0.3s ease;
	}

	.pagination button.active {
		background: #4CAF50;
		color: white;
		border-color: #4CAF50;
	}

	.pagination button:hover:not(.active) {
		background: #f5f5f5;
	}

	@media (max-width: 768px) {
		.table-controls {
			flex-direction: column;
			align-items: stretch;
		}

		.search-input {
			flex: 1;
		}

		.data-table {
			font-size: 12px;
		}

		.data-table th,
		.data-table td {
			padding: 8px 10px;
		}

		.action-buttons {
			flex-direction: column;
		}

		.btn-action {
			width: 100%;
		}
	}
</style>

<script>
	// Load employees data
	function loadEmployeesData() {
		fetch('{{ route("data-entry.employees.get") }}')
			.then(res => res.json())
			.then(data => {
				displayEmployeesTable(data);
				document.getElementById('employeesCount').textContent = `Total: ${data.length}`;
				document.getElementById('employees-badge').textContent = data.length;
			})
			.catch(err => console.error('Error loading employees:', err));
	}

	function displayEmployeesTable(employees) {
		const tbody = document.getElementById('employeesBody');
        
		if (!employees || employees.length === 0) {
			tbody.innerHTML = '<tr class="empty-row"><td colspan="6">Aucun employé trouvé</td></tr>';
			return;
		}

		tbody.innerHTML = employees.map(emp => `
			<tr>
				<td><strong>${emp.name || ''}</strong></td>
				<td>${emp.email || ''}</td>
				<td>${emp.service || ''}</td>
				<td>
					<span class="role-badge ${emp.role === 'admin' ? 'admin' : ''}">
						${emp.role === 'admin' ? 'Admin' : 'Employé'}
					</span>
				</td>
				<td>${new Date(emp.created_at).toLocaleDateString('fr-FR')}</td>
				<td>
					<div class="action-buttons">
						<button class="btn-action btn-edit" onclick="editEmployee(${emp.id})">Éditer</button>
						<button class="btn-action btn-delete" onclick="deleteEmployee(${emp.id})">Supprimer</button>
					</div>
				</td>
			</tr>
		`).join('');
	}

	function deleteEmployee(id) {
		if (!confirm('Êtes-vous sûr de vouloir supprimer cet employé?')) return;

		fetch(`{{ route("data-entry.employees.delete", ":id") }}`.replace(':id', id), {
			method: 'DELETE',
			headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content }
		})
			.then(res => res.json())
			.then(data => {
				if (data.success) {
					showToast('Employé supprimé avec succès', 'success');
					loadEmployeesData();
				} else {
					showToast(data.message || 'Erreur lors de la suppression', 'error');
				}
			})
			.catch(err => {
				console.error(err);
				showToast('Erreur lors de la suppression', 'error');
			});
	}

	function editEmployee(id) {
		showToast('Fonctionnalité bientôt disponible', 'info');
	}

	// Search
	document.getElementById('employeesSearch').addEventListener('keyup', function() {
		const search = this.value.toLowerCase();
		document.querySelectorAll('#employeesTable tbody tr').forEach(row => {
			const text = row.innerText.toLowerCase();
			row.style.display = text.includes(search) ? '' : 'none';
		});
	});

	// Load on tab switch
	if (document.querySelector('[data-tab="employees"]')) {
		loadEmployeesData();
	}
</script>
