<!-- Zones Management -->
<div class="data-section">
    <div class="section-header">
        <h3><i class="fas fa-map-marker-alt"></i> Zones</h3>
        <div class="section-actions">
            <input type="text" class="search-input" id="zonesSearch" placeholder="Rechercher par nom ou description...">
            <select id="zonesPerPage" class="search-input" style="max-width:120px;">
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
            </select>
            <button class="btn-add" onclick="openModal('addZoneModal')"><i class="fas fa-plus"></i> Ajouter</button>
        </div>
    </div>

    <div id="zonesContainer" style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Zone</th>
                    <th>Description</th>
                    <th>Employés</th>
                    <th>Véhicules</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="zonesBody">
                <tr>
                    <td colspan="5" class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Chargement des zones...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="pagination-controls" id="zonesPagination" style="display:none;"></div>
</div>

<script>
    if (document.querySelector('[data-tab="zones"]')) {
        // initialize
        showSkeleton('zonesBody', 5);
        document.getElementById('zonesSearch')?.addEventListener('input', debounce(() => { currentPages.zones = 1; loadZonesData(1); }, 250));
        document.getElementById('zonesPerPage')?.addEventListener('change', (e) => {
            const v = parseInt(e.target.value, 10) || 10;
            ITEMS_PER_PAGE = v;
            currentPages.zones = 1;
            loadZonesData(1);
        });

        loadZonesData(1);
    }

    async function loadZonesData(page = 1) {
        try {
            const perPage = ITEMS_PER_PAGE || 25;
            const params = new URLSearchParams();
            params.set('page', page);
            params.set('per_page', perPage);
            const searchQ = (document.getElementById('zonesSearch')?.value || '').trim();
            if (searchQ) params.set('search', searchQ);

            const res = await fetch('{{ route("data-entry.zones.get") }}' + '?' + params.toString());
            if (!res.ok) throw new Error('Network error');
            const json = await res.json();
            allData.zones = json;
            currentPages.zones = json.meta?.current_page || page;
            displayZonesTable(allData.zones);
        } catch (err) {
            console.error('Error loading zones', err);
            showEmptyState('zonesBody', 'Erreur lors du chargement');
        }
    }

    function displayZonesTable(data) {
        const tbody = document.getElementById('zonesBody');
        const searchQ = (document.getElementById('zonesSearch')?.value || '').toLowerCase().trim();
        const filteredAll = (data || []).filter(z => {
            const text = `${z.name || z.zone || ''} ${z.description || ''}`.toLowerCase();
            return !searchQ || text.includes(searchQ);
        });

        if (!filteredAll.length) {
            showEmptyState('zonesBody', 'Aucune zone trouvée');
            document.getElementById('zonesPagination').style.display = 'none';
            return;
        }

        // If data is server response wrapper, use it
        let items = [];
        let meta = null;
        if (Array.isArray(data)) {
            // legacy array
            items = data;
            meta = null;
        } else if (data && data.data) {
            items = data.data;
            meta = data.meta;
        } else {
            items = (data || []);
        }

        const totalPages = meta ? Math.max(1, meta.last_page) : Math.max(1, Math.ceil(items.length / ITEMS_PER_PAGE));

        tbody.innerHTML = items.map(z => `
            <tr>
                <td><strong>${escapeHtml(z.name || z.zone)}</strong></td>
                <td>${escapeHtml(z.description || '')}</td>
                <td>${escapeHtml(String((z.employees_count || z.employees || []).length || z.employees_count || 0))}</td>
                <td>${escapeHtml(String((z.vehicles_count || z.vehicles || []).length || z.vehicles_count || 0))}</td>
                <td>
                    <div class="table-actions">
                        <button class="btn-edit" onclick="editZone(${z.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn-delete" onclick="deleteZone(${z.id})"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        `).join('');
        updatePagination('zonesPagination', currentPages.zones, totalPages, 'zones');
    }
</script>
