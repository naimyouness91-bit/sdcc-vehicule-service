<!-- Requests/Demandes Table -->
<div class="data-section">
    <div class="section-header">
        <h3><i class="fas fa-envelope-open-text"></i> Demandes</h3>
        <div class="section-actions">
            <input type="text" id="requestsSearch" class="search-input" placeholder="Rechercher par employé, véhicule ou destination...">
            <select id="requestsFilter" class="search-input" style="max-width:160px;">
                <option value="all">Tous statuts</option>
                <option value="pending">En attente</option>
                <option value="approved">Approuvé</option>
                <option value="rejected">Rejeté</option>
                <option value="cancelled">Annulé</option>
            </select>
            <button class="btn-add" onclick="openModal('addRequestModal')"><i class="fas fa-plus"></i> Nouvelle</button>
        </div>
    </div>

    <div id="requestsContainer" style="overflow-x: auto;">
        <table class="data-table" id="requestsTable">
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Véhicule</th>
                    <th>Destination</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="requestsBody">
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Chargement des demandes...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="pagination-controls" id="requestsPagination" style="display:none;"></div>
</div>

<style>
    .status-pending { background: #fff3e0; color: #f57c00; }
    .status-approved { background: #c8e6c9; color: #2e7d32; }
    .status-rejected, .status-cancelled { background: #ffcdd2; color: #c62828; }
</style>

<script>
    if (document.querySelector('[data-tab="requests"]')) {
        showSkeleton('requestsBody', 7);
        document.getElementById('requestsSearch')?.addEventListener('input', debounce(() => { currentPages.requests = 1; loadRequestsData(1); }, 250));
        document.getElementById('requestsFilter')?.addEventListener('change', () => { currentPages.requests = 1; loadRequestsData(1); });
        loadRequestsData(1);
    }

    async function loadRequestsData(page = 1) {
        try {
            const perPage = ITEMS_PER_PAGE || 25;
            const params = new URLSearchParams();
            params.set('page', page);
            params.set('per_page', perPage);
            const searchQ = (document.getElementById('requestsSearch')?.value || '').trim();
            const status = (document.getElementById('requestsFilter')?.value || 'all');
            if (searchQ) params.set('search', searchQ);
            if (status && status !== 'all') params.set('status', status);

            const res = await fetch('{{ route("data-entry.requests.get") }}' + '?' + params.toString());
            if (!res.ok) throw new Error('Network error');
            const json = await res.json();
            // store for potential client-side use
            allData.requests = json;
            currentPages.requests = json.meta?.current_page || page;
            displayRequestsTable(allData.requests);
        } catch (err) {
            console.error('Error loading requests', err);
            showEmptyState('requestsBody', 'Erreur lors du chargement');
        }
    }

    function displayRequestsTable(response) {
        const tbody = document.getElementById('requestsBody');
        if (!response || !response.data) {
            showEmptyState('requestsBody', 'Aucune demande trouvée');
            document.getElementById('requestsPagination').style.display = 'none';
            return;
        }

        const items = response.data;
        if (!items.length) {
            showEmptyState('requestsBody', 'Aucune demande trouvée');
            document.getElementById('requestsPagination').style.display = 'none';
            return;
        }

        tbody.innerHTML = items.map(r => `
            <tr>
                <td>${escapeHtml(r.employee || r.employee_name)}</td>
                <td>${escapeHtml(r.vehicle || r.vehicle_name || '')}</td>
                <td>${escapeHtml(r.destination || '')}</td>
                <td>${escapeHtml(r.start_date || '')}</td>
                <td>${escapeHtml(r.end_date || '')}</td>
                <td>
                    <span class="badge ${escapeHtml((r.status || '').toLowerCase())}">${escapeHtml(r.status)}</span>
                    ${r.has_conflict ? '<span class="conflict-badge" style="display:inline-flex;align-items:center;gap:4px;background:#ffebee;color:#c62828;border:1px solid #ef9a9a;border-radius:12px;padding:2px 8px;font-size:10px;font-weight:700;margin-left:6px;"><i class="fas fa-exclamation-triangle"></i> Conflit</span>' : ''}
                </td>
                <td>
                    <div class="table-actions">
                        <button class="btn-edit" onclick="editRequest(${r.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn-delete" onclick="deleteRequest(${r.id})"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        `).join('');

        const meta = response.meta || { total: items.length, per_page: items.length, current_page: 1, last_page: 1 };
        updatePagination('requestsPagination', meta.current_page, Math.max(1, meta.last_page), 'requests');
    }
</script>
