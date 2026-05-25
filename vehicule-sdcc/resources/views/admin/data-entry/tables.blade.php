<!-- ======================= DATA DISPLAY TABLES ======================= -->
<style>
    .data-section {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }

    .section-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #333;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-header h3 i {
        color: #4CAF50;
        font-size: 20px;
    }

    .section-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-add {
        background: linear-gradient(135deg, #4CAF50, #66BB6A);
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }

    .search-filter {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    .search-input {
        flex: 1;
        min-width: 200px;
        padding: 10px 12px;
        border: 1.5px solid #e0e0e0;
        border-radius: 6px;
        font-size: 13px;
    }

    .search-input:focus {
        outline: none;
        border-color: #4CAF50;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }

    /* Badges */
    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        text-transform: capitalize;
    }

    .badge.disponible { background: #d1fae5; color: #047857; }
    .badge.maintenance { background: #fee2e2; color: #991b1b; }

    /* Skeleton */
    .skeleton { background: linear-gradient(90deg, #f0f0f0 25%, #f7f7f7 37%, #f0f0f0 63%); background-size: 400% 100%; animation: shimmer 1.2s linear infinite; }
    @keyframes shimmer { 0% { background-position: 100% 0 } 100% { background-position: -100% 0 } }

    /* Cleaner section header on small screens */
    .section-header .section-actions { gap: 8px; }

    .table-actions {
        display: flex;
        gap: 6px;
    }

    .btn-edit,
    .btn-delete {
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-edit {
        color: #1976d2;
        background: rgba(25, 118, 210, 0.1);
    }

    .btn-edit:hover {
        background: rgba(25, 118, 210, 0.2);
    }

    .btn-delete {
        color: #d32f2f;
        background: rgba(211, 47, 47, 0.1);
    }

    .btn-delete:hover {
        background: rgba(211, 47, 47, 0.2);
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #999;
    }

    .empty-state i {
        font-size: 48px;
        color: #e0e0e0;
        margin-bottom: 15px;
    }

    .empty-state p {
        margin: 0;
        font-size: 14px;
    }

    /* Pagination */
    .pagination-controls {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
    }

    .pagination-btn {
        padding: 6px 10px;
        border: 1px solid #e0e0e0;
        background: white;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.3s;
    }

    .pagination-btn:hover:not(:disabled) {
        background: #f0f0f0;
        border-color: #4CAF50;
    }

    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination-info {
        font-size: 12px;
        color: #999;
    }

    @media (max-width: 768px) {
        .data-table {
            font-size: 12px;
        }

        .data-table th,
        .data-table td {
            padding: 8px;
        }

        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .section-actions {
            width: 100%;
        }

        .btn-add {
            flex: 1;
        }

        .search-input {
            min-width: 100%;
        }
    }
</style>

<!-- ==================== EMPLOYEES TABLE ==================== -->
<div class="data-section">
    <div class="section-header">
        <h3><i class="fas fa-users"></i> Gestion des Employés</h3>
        <div class="section-actions">
            <input type="text" class="search-input" id="employeeSearch" placeholder="Rechercher par nom ou email...">
            <button class="btn-add" onclick="openModal('addEmployeeModal')">
                <i class="fas fa-user-plus"></i> Ajouter Employé
            </button>
        </div>
    </div>

    <div id="employeesContainer" style="overflow-x: auto;">
        <table class="data-table" id="employeesTable">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Service</th>
                    <th>Rôle</th>
                    <th>Ajouté le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="employeesTableBody">
                <tr>
                    <td colspan="6" class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Chargement des employés...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="pagination-controls" id="employeesPagination" style="display: none;"></div>
</div>

<!-- ==================== VEHICLES TABLE ==================== -->
<div class="data-section">
    <div class="section-header">
        <h3><i class="fas fa-car"></i> Gestion des Véhicules</h3>
        <div class="section-actions">
            <input type="text" class="search-input" id="vehicleSearch" placeholder="Rechercher par modèle ou matricule...">
            <select id="vehicleStatusFilter" class="search-input" style="max-width:170px;">
                <option value="all">Tous statuts</option>
                <option value="disponible">Disponible</option>
                <option value="maintenance">Maintenance</option>
            </select>
            <button class="btn-add" onclick="openModal('addVehicleModal')">
                <i class="fas fa-car-plus"></i> Ajouter Véhicule
            </button>
        </div>
    </div>

    <div id="vehiclesContainer" style="overflow-x: auto;">
        <table class="data-table" id="vehiclesTable">
            <thead>
                <tr>
                    <th>Modèle</th>
                    <th>Matricule</th>
                    <th>Type</th>
                    <th>Année</th>
                    <th>Kilométrage</th>
                    <th>Statut</th>
                    <th>Ajouté le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="vehiclesTableBody">
                <tr>
                    <td colspan="8" class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Chargement des véhicules...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="pagination-controls" id="vehiclesPagination" style="display: none;"></div>
</div>

<!-- ==================== KILOMETRAGE HISTORY TABLE ==================== -->
<div class="data-section">
    <div class="section-header">
        <h3><i class="fas fa-tachometer-alt"></i> Historique du Kilométrage</h3>
        <div class="section-actions">
            <input type="text" class="search-input" id="kilometrageSearch" placeholder="Rechercher par véhicule ou employé...">
            <select id="kilometrageServiceFilter" class="search-input" style="max-width:220px;">
                <option value="all">Tous services</option>
            </select>
            <button class="btn-add" onclick="openModal('addKilometrageModal')">
                <i class="fas fa-tachometer-alt"></i> Ajouter Kilométrage
            </button>
        </div>
    </div>

    <div id="kilometrageContainer" style="overflow-x: auto;">
        <table class="data-table" id="kilometrageTable">
            <thead>
                <tr>
                    <th>Véhicule</th>
                    <th>Employé</th>
                    <th>Service</th>
                    <th>Changement (km)</th>
                    <th>Raison</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="kilometrageTableBody">
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Chargement de l'historique...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="pagination-controls" id="kilometragePagination" style="display: none;"></div>
</div>

<!-- ==================== DATA TABLE SCRIPTS ==================== -->
<script>
    // Pagination configuration
    let ITEMS_PER_PAGE = 10;
    let currentPages = { employees: 1, vehicles: 1, kilometrage: 1, zones: 1, reservations: 1, requests: 1 };
    let allData = { employees: [], vehicles: [], kilometrage: [], zones: [], reservations: [], requests: [] };

    // Simple debounce helper
    function debounce(fn, wait = 300) {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn(...args), wait);
        };
    }

    // Helper: show skeleton rows while loading
    function showSkeleton(tbodyId, cols = 6) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        const rows = Array.from({ length: 3 }).map(() => `\n            <tr>\n                ${'<td class="skeleton" colspan="'+cols+'" style="height:36px"></td>'}\n            </tr>`).join('');
        tbody.innerHTML = rows;
    }

    // Load all data on page load
    document.addEventListener('DOMContentLoaded', function() {
        // show skeletons
        showSkeleton('vehiclesTableBody', 8);
        showSkeleton('kilometrageTableBody', 7);

        loadVehiclesData();
        loadKilometrageData();

        // Setup search listeners (debounced)
        document.getElementById('vehicleSearch')?.addEventListener('input', debounce(filterVehicles, 250));
        document.getElementById('vehicleStatusFilter')?.addEventListener('change', () => { currentPages.vehicles = 1; displayVehiclesTable(allData.vehicles); });

        document.getElementById('kilometrageSearch')?.addEventListener('input', debounce(filterKilometrage, 250));
        document.getElementById('kilometrageServiceFilter')?.addEventListener('change', () => { currentPages.kilometrage = 1; displayKilometrageTable(allData.kilometrage); });
    });

    // ==================== VEHICLES DATA ==================== 
    async function loadVehiclesData() {
        try {
            const response = await fetch('{{ route("data-entry.vehicles.get") }}');
            allData.vehicles = await response.json();
            currentPages.vehicles = 1;
            displayVehiclesTable(allData.vehicles);
        } catch (error) {
            console.error('Error loading vehicles:', error);
            showEmptyState('vehiclesTableBody', 'Erreur lors du chargement');
        }
    }

    function displayVehiclesTable(data) {
        const tbody = document.getElementById('vehiclesTableBody');

        // build filtered dataset BEFORE pagination
        const statusFilter = document.getElementById('vehicleStatusFilter')?.value || 'all';
        const searchQ = (document.getElementById('vehicleSearch')?.value || '').toLowerCase().trim();

        const filteredAll = data.filter(car => {
            const matchStatus = statusFilter === 'all' || car.status === statusFilter;
            const text = `${car.name} ${car.matricule} ${car.model}`.toLowerCase();
            const matchSearch = !searchQ || text.includes(searchQ);
            return matchStatus && matchSearch;
        });

        if (filteredAll.length === 0) {
            showEmptyState('vehiclesTableBody', 'Aucun véhicule trouvé');
            document.getElementById('vehiclesPagination').style.display = 'none';
            return;
        }

        const totalPages = Math.max(1, Math.ceil(filteredAll.length / ITEMS_PER_PAGE));
        const { items } = paginate(filteredAll, currentPages.vehicles, ITEMS_PER_PAGE);

        tbody.innerHTML = items.map(car => `
            <tr>
                <td><strong>${escapeHtml(car.name)}</strong></td>
                <td><span style="background: #fff3e0; padding: 2px 8px; border-radius: 4px; font-family: monospace; font-weight: 600;">${escapeHtml(car.matricule)}</span></td>
                <td>${escapeHtml(car.model)}</td>
                <td>${escapeHtml(car.year)}</td>
                <td><strong>${escapeHtml(String(car.km))} km</strong></td>
                <td><span class="badge ${car.status === 'disponible' ? 'disponible' : 'maintenance'}">${escapeHtml(car.status)}</span></td>
                <td>${escapeHtml(car.created_at)}</td>
                <td>
                    <div class="table-actions">
                        <button class="btn-edit" onclick="editVehicle(${car.id})">
                            <i class="fas fa-edit"></i> Éditer
                        </button>
                        <button class="btn-delete" onclick="deleteVehicle(${car.id})">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        updatePagination('vehiclesPagination', currentPages.vehicles, totalPages, 'vehicles');
    }

    function filterVehicles() {
        const search = document.getElementById('vehicleSearch').value.toLowerCase();
        const filtered = allData.vehicles.filter(car => 
            car.name.toLowerCase().includes(search) || 
            car.matricule.toLowerCase().includes(search)
        );
        currentPages.vehicles = 1;
        displayVehiclesTable(filtered);
    }

    // ==================== KILOMETRAGE DATA ==================== 
    async function loadKilometrageData() {
        try {
            const response = await fetch('{{ route("data-entry.kilometrage.get") }}');
            allData.kilometrage = await response.json();
            currentPages.kilometrage = 1;
            // populate service filter options
            const services = Array.from(new Set(allData.kilometrage.map(r => r.service).filter(Boolean)));
            const select = document.getElementById('kilometrageServiceFilter');
            if (select) {
                // keep 'all' option then add services
                services.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s;
                    opt.textContent = s;
                    select.appendChild(opt);
                });
            }
            displayKilometrageTable(allData.kilometrage);
        } catch (error) {
            console.error('Error loading kilometrage:', error);
            showEmptyState('kilometrageTableBody', 'Erreur lors du chargement');
        }
    }

    function displayKilometrageTable(data) {
        const tbody = document.getElementById('kilometrageTableBody');

        const serviceFilter = document.getElementById('kilometrageServiceFilter')?.value || 'all';
        const searchQ = (document.getElementById('kilometrageSearch')?.value || '').toLowerCase().trim();

        const filteredAll = data.filter(record => {
            const matchService = serviceFilter === 'all' || (record.service || '').toLowerCase() === serviceFilter.toLowerCase();
            const text = `${record.vehicle || ''} ${record.employee || ''}`.toLowerCase();
            const matchSearch = !searchQ || text.includes(searchQ);
            return matchService && matchSearch;
        });

        if (filteredAll.length === 0) {
            showEmptyState('kilometrageTableBody', 'Aucun historique disponible');
            document.getElementById('kilometragePagination').style.display = 'none';
            return;
        }

        const totalPages = Math.max(1, Math.ceil(filteredAll.length / ITEMS_PER_PAGE));
        const { items } = paginate(filteredAll, currentPages.kilometrage, ITEMS_PER_PAGE);

        tbody.innerHTML = items.map((record) => `
            <tr>
                <td><strong>${escapeHtml(record.vehicle)}</strong></td>
                <td>${escapeHtml(record.employee)}</td>
                <td>${escapeHtml(record.service || '')}</td>
                <td><span style="color: ${record.km_change > 0 ? '#d32f2f' : '#2e7d32'}; font-weight: 600;">+${escapeHtml(String(record.km_change))} km</span></td>
                <td>${escapeHtml(record.reason || '')}</td>
                <td>${escapeHtml(record.date)}</td>
                <td>
                    <button class="btn-delete" onclick="deleteKilometrage(${record.id})">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </td>
            </tr>
        `).join('');

        updatePagination('kilometragePagination', currentPages.kilometrage, totalPages, 'kilometrage');
    }

    function filterKilometrage() {
        const search = document.getElementById('kilometrageSearch').value.toLowerCase();
        const filtered = allData.kilometrage.filter(record => 
            record.vehicle.toLowerCase().includes(search) || 
            record.employee.toLowerCase().includes(search)
        );
        currentPages.kilometrage = 1;
        displayKilometrageTable(filtered);
    }

    // ==================== UTILITY FUNCTIONS ==================== 
    function escapeHtml(unsafe) {
        if (unsafe === null || unsafe === undefined) return '';
        return String(unsafe)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
    function paginate(data, page, itemsPerPage) {
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        return {
            items: data.slice(start, end),
            hasMore: end < data.length,
            total: data.length
        };
    }

    function updatePagination(containerId, currentPage, totalPages, dataType) {
        const container = document.getElementById(containerId);
        if (totalPages <= 1) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'flex';
        let html = '';

        // Previous button
        html += `<button class="pagination-btn" onclick="goToPage('${dataType}', ${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
            <i class="fas fa-chevron-left"></i>
        </button>`;

        // Page numbers
        for (let i = 1; i <= Math.min(totalPages, 5); i++) {
            const style = i === currentPage ? 'background: #4CAF50; color: white; border-color: #4CAF50;' : '';
            html += `<button class="pagination-btn" onclick="goToPage('${dataType}', ${i})" style="${style}">${i}</button>`;
        }

        if (totalPages > 5) {
            html += `<span class="pagination-info">... sur ${totalPages}</span>`;
        }

        // Next button
        html += `<button class="pagination-btn" onclick="goToPage('${dataType}', ${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
            <i class="fas fa-chevron-right"></i>
        </button>`;

        html += `<span class="pagination-info">Page ${currentPage} / ${totalPages}</span>`;
        container.innerHTML = html;
    }

    function goToPage(dataType, page) {
        currentPages[dataType] = page;
        if (dataType === 'employees') displayEmployeesTable && displayEmployeesTable(allData.employees);
        else if (dataType === 'vehicles') displayVehiclesTable && displayVehiclesTable(allData.vehicles);
        else if (dataType === 'kilometrage') displayKilometrageTable && displayKilometrageTable(allData.kilometrage);
        else if (dataType === 'zones') loadZonesData && loadZonesData(page);
        else if (dataType === 'reservations') loadReservationsData && loadReservationsData(page);
        else if (dataType === 'requests') loadRequestsData && loadRequestsData(page);
    }

    function showEmptyState(elementId, message) {
        document.getElementById(elementId).innerHTML = `
            <tr>
                <td colspan="100%" class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>${message}</p>
                </td>
            </tr>
        `;
    }

    // ==================== DELETE FUNCTIONS ==================== 
    // Employees deletion removed (archived)

    async function deleteVehicle(id) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce véhicule?')) return;

        try {
            const response = await fetch(`{{ route('data-entry.vehicles.delete', '') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                }
            });

            const data = await response.json();

            if (response.ok) {
                showToast('success', 'Véhicule supprimé avec succès');
                loadVehiclesData();
            } else {
                showToast('error', data.message || 'Erreur lors de la suppression');
            }
        } catch (error) {
            showToast('error', 'Erreur réseau');
        }
    }

    async function deleteKilometrage(id) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet enregistrement?')) return;

        try {
            const response = await fetch(`{{ route('data-entry.kilometrage.get') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                }
            });

            if (response.ok) {
                showToast('success', 'Enregistrement supprimé');
                loadKilometrageData();
            } else {
                showToast('error', 'Erreur lors de la suppression');
            }
        } catch (error) {
            showToast('error', 'Erreur réseau');
        }
    }

    // Edit functions (placeholder - can be extended)
    // editEmployee removed (archived)

    function editVehicle(id) {
        showToast('info', 'Fonctionnalité d\'édition en développement');
    }
</script>
