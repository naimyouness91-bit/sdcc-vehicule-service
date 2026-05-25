<!-- Vehicles Table -->
<div class="data-table-wrapper">
    <div class="table-controls">
        <input type="text" id="vehiclesSearch" class="search-input" placeholder="🔍 Rechercher véhicule...">
        <div class="table-info">
            <span id="vehiclesCount">Total: 0</span>
            <span id="vehiclesAvailable" style="margin-left: 20px;">Disponibles: 0</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table" id="vehiclesTable">
            <thead>
                <tr>
                    <th>Modèle</th>
                    <th>Immatriculation</th>
                    <th>Type</th>
                    <th>Année</th>
                    <th>Kilométrage</th>
                    <th>Statut</th>
                    <th>Date Création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="vehiclesBody">
                <tr class="empty-row">
                    <td colspan="8">Chargement...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="pagination" id="vehiclesPagination"></div>
</div>

<style>
    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        background: #c8e6c9;
        color: #2e7d32;
    }

    .status-badge.maintenance {
        background: #ffe0b2;
        color: #f57c00;
    }

    .km-value {
        font-weight: 600;
        color: #d32f2f;
    }
</style>

<script>
    function loadVehiclesData() {
        fetch('{{ route("data-entry.vehicles.get") }}')
            .then(res => res.json())
            .then(data => {
                displayVehiclesTable(data);
                document.getElementById('vehiclesCount').textContent = `Total: ${data.length}`;
                const available = data.filter(v => v.status === 'disponible').length;
                document.getElementById('vehiclesAvailable').textContent = `Disponibles: ${available}`;
                document.getElementById('vehicles-badge').textContent = data.length;
            })
            .catch(err => console.error('Error loading vehicles:', err));
    }

    function displayVehiclesTable(vehicles) {
        const tbody = document.getElementById('vehiclesBody');
        
        if (!vehicles || vehicles.length === 0) {
            tbody.innerHTML = '<tr class="empty-row"><td colspan="8">Aucun véhicule trouvé</td></tr>';
            return;
        }

        tbody.innerHTML = vehicles.map(veh => `
            <tr>
                <td><strong>${veh.name || ''}</strong></td>
                <td><span style="background: #ffd54f; padding: 4px 8px; border-radius: 4px; font-weight: 600;">${veh.matricule || ''}</span></td>
                <td>${veh.model || ''}</td>
                <td>${veh.year || ''}</td>
                <td><span class="km-value">${veh.km ? veh.km.toLocaleString('fr-FR') : 0} km</span></td>
                <td>
                    <span class="status-badge ${veh.status === 'maintenance' ? 'maintenance' : ''}">
                        ${veh.status === 'disponible' ? '✓ Disponible' : '⚠ Maintenance'}
                    </span>
                </td>
                <td>${new Date(veh.created_at).toLocaleDateString('fr-FR')}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-action btn-edit" onclick="editVehicle(${veh.id})">Éditer</button>
                        <button class="btn-action btn-delete" onclick="deleteVehicle(${veh.id})">Supprimer</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function deleteVehicle(id) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce véhicule?')) return;

        fetch(`{{ route("data-entry.vehicles.delete", ":id") }}`.replace(':id', id), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Véhicule supprimé avec succès', 'success');
                    loadVehiclesData();
                } else {
                    showToast(data.message || 'Erreur lors de la suppression', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Erreur lors de la suppression', 'error');
            });
    }

    function editVehicle(id) {
        showToast('Fonctionnalité bientôt disponible', 'info');
    }

    document.getElementById('vehiclesSearch').addEventListener('keyup', function() {
        const search = this.value.toLowerCase();
        document.querySelectorAll('#vehiclesTable tbody tr').forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(search) ? '' : 'none';
        });
    });

    if (document.querySelector('[data-tab="vehicles"]')) {
        loadVehiclesData();
    }
</script>
