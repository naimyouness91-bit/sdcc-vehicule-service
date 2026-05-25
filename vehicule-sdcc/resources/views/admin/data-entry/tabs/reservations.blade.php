<!-- Reservations Table -->
<div class="data-section">
    <div class="section-header">
        <h3><i class="fas fa-calendar-check"></i> Réservations</h3>
        <div class="section-actions">
            <input type="text" id="reservationsSearch" class="search-input" placeholder="Rechercher par employé ou véhicule...">
            <select id="reservationsFilter" class="search-input" style="max-width:160px;">
                <option value="all">Tous statuts</option>
                <option value="pending">En attente</option>
                <option value="approved">Approuvée</option>
                <option value="cancelled">Annulée</option>
            </select>
            <button class="btn-add" onclick="openModal('addReservationModal')"><i class="fas fa-plus"></i> Nouvelle</button>
        </div>
    </div>

    <div id="reservationsContainer" style="overflow-x: auto;">
        <table class="data-table" id="reservationsTable">
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Véhicule</th>
                    <th>Date Début</th>
                    <th>Date Fin</th>
                    <th>Kilométrage</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="reservationsBody">
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Chargement des réservations...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="pagination-controls" id="reservationsPagination" style="display:none;"></div>
</div>

<script>
    if (document.querySelector('[data-tab="reservations"]')) {
        showSkeleton('reservationsBody', 7);
        document.getElementById('reservationsSearch')?.addEventListener('input', debounce(() => { currentPages.reservations = 1; loadReservationsData(1); }, 250));
        document.getElementById('reservationsFilter')?.addEventListener('change', () => { currentPages.reservations = 1; loadReservationsData(1); });
        loadReservationsData(1);
    }

    async function loadReservationsData(page = 1) {
        try {
            const perPage = ITEMS_PER_PAGE || 25;
            const params = new URLSearchParams();
            params.set('page', page);
            params.set('per_page', perPage);
            const searchQ = (document.getElementById('reservationsSearch')?.value || '').trim();
            const status = (document.getElementById('reservationsFilter')?.value || 'all');
            if (searchQ) params.set('search', searchQ);
            if (status && status !== 'all') params.set('status', status);

            const res = await fetch('{{ route("data-entry.reservations.get") }}' + '?' + params.toString());
            if (!res.ok) throw new Error('Network error');
            const json = await res.json();
            allData.reservations = json;
            currentPages.reservations = json.meta?.current_page || page;
            displayReservationsTable(allData.reservations);
        } catch (err) {
            console.error('Error loading reservations', err);
            showEmptyState('reservationsBody', 'Erreur lors du chargement');
        }
    }

    function displayReservationsTable(response) {
        const tbody = document.getElementById('reservationsBody');
        if (!response || !response.data) {
            showEmptyState('reservationsBody', 'Aucune réservation trouvée');
            document.getElementById('reservationsPagination').style.display = 'none';
            return;
        }

        const items = response.data;
        if (!items.length) {
            showEmptyState('reservationsBody', 'Aucune réservation trouvée');
            document.getElementById('reservationsPagination').style.display = 'none';
            return;
        }

        tbody.innerHTML = items.map(r => `
            <tr>
                <td>${escapeHtml(r.employee || r.employee_name)}</td>
                <td>${escapeHtml(r.vehicle || r.vehicle_name)}</td>
                <td>${escapeHtml(r.start_date || '')}</td>
                <td>${escapeHtml(r.end_date || '')}</td>
                <td>${escapeHtml(String(r.kilometers || r.km || ''))}</td>
                <td><span class="badge ${escapeHtml((r.status || '').toLowerCase())}">${escapeHtml(r.status)}</span></td>
                <td>
                    <div class="table-actions">
                        <button class="btn-edit" onclick="editReservation(${r.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn-delete" onclick="deleteReservation(${r.id})"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        `).join('');

        const meta = response.meta || { total: items.length, per_page: items.length, current_page: 1, last_page: 1 };
        updatePagination('reservationsPagination', meta.current_page, Math.max(1, meta.last_page), 'reservations');
    }
</script>
