<!-- Kilometrage History Table -->
<div class="data-table-wrapper">
    <div class="table-controls">
        <input type="text" id="kilometrageSearch" class="search-input" placeholder="🔍 Rechercher...(véhicule, employé)">
        <div class="table-info">
            <span id="kilometrageCount">Total: 0</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table" id="kilometrageTable">
            <thead>
                <tr>
                    <th>Véhicule</th>
                    <th>Employé</th>
                    <th>Service</th>
                    <th>Kilométrage</th>
                    <th>Date</th>
                    <th>Raison</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="kilometrageBody">
                <tr class="empty-row">
                    <td colspan="7">Chargement...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="pagination" id="kilometragePagination"></div>
</div>

<style>
    .km-increase {
        color: #d32f2f;
        font-weight: 600;
    }

    .km-decrease {
        color: #2e7d32;
        font-weight: 600;
    }
</style>

<script>
    function loadKilometrageData() {
        fetch('{{ route("data-entry.kilometrage.get") }}')
            .then(res => res.json())
            .then(data => {
                displayKilometrageTable(data);
                document.getElementById('kilometrageCount').textContent = `Total: ${data.length}`;
                document.getElementById('kilometrage-badge').textContent = data.length;
            })
            .catch(err => console.error('Error loading kilometrage:', err));
    }

    function displayKilometrageTable(records) {
        const tbody = document.getElementById('kilometrageBody');
        
        if (!records || records.length === 0) {
            tbody.innerHTML = '<tr class="empty-row"><td colspan="7">Aucune mise à jour trouvée</td></tr>';
            return;
        }

        tbody.innerHTML = records.map(rec => {
            const changeClass = rec.change_sign === '+' ? 'km-increase' : 'km-decrease';
            return `
                <tr>
                    <td><strong>${rec.vehicle_name || 'N/A'}</strong></td>
                    <td>${rec.employee_name || '-'}</td>
                    <td>${rec.service || '-'}</td>
                    <td><span class="${changeClass}">${rec.change_sign}${rec.change_value} km</span></td>
                    <td>${new Date(rec.date).toLocaleDateString('fr-FR')}</td>
                    <td>${rec.reason || '-'}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-action btn-delete" onclick="deleteKilometrage(${rec.id})">Supprimer</button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function deleteKilometrage(id) {
        if (!confirm('Êtes-vous sûr?')) return;
        
        fetch(`/data-entry/kilometrage/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Supprimé avec succès', 'success');
                    loadKilometrageData();
                } else {
                    showToast('Erreur lors de la suppression', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Erreur', 'error');
            });
    }

    document.getElementById('kilometrageSearch').addEventListener('keyup', function() {
        const search = this.value.toLowerCase();
        document.querySelectorAll('#kilometrageTable tbody tr').forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(search) ? '' : 'none';
        });
    });

    if (document.querySelector('[data-tab="kilometrage"]')) {
        loadKilometrageData();
    }
</script>
