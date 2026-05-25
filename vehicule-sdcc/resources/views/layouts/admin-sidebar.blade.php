@extends('layouts.app')

@section('content')
<style>
    /* ==================== ADMIN SIDEBAR LAYOUT ==================== */
    .admin-container {
        display: flex;
        gap: 0;
        margin-top: 70px;
        min-height: calc(100vh - 70px);
        background: #f5f7fa;
    }

    /* Sidebar Navigation */
    .admin-sidebar {
        width: 280px;
        background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
        border-right: 1px solid rgba(76, 175, 80, 0.2);
        padding: 0;
        position: fixed;
        left: 0;
        top: 70px;
        bottom: 0;
        z-index: 900;
        overflow-y: auto;
        overflow-x: hidden;
        box-shadow: 4px 0 20px rgba(76, 175, 80, 0.15);
    }

    .admin-sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .admin-sidebar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
    }

    .admin-sidebar::-webkit-scrollbar-thumb {
        background: rgba(76, 175, 80, 0.6);
        border-radius: 3px;
    }

    .admin-sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(76, 175, 80, 0.9);
    }

    /* Sidebar Header */
    .sidebar-header {
        padding: 20px;
        border-bottom: 2px solid rgba(76, 175, 80, 0.3);
        background: rgba(0, 0, 0, 0.2);
    }

    .sidebar-header h3 {
        margin: 0;
        color: #4CAF50;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sidebar-header i {
        font-size: 16px;
    }

    /* Navigation Menu */
    .sidebar-menu {
        padding: 15px 0;
        list-style: none;
    }

    .sidebar-menu-section {
        margin-bottom: 20px;
    }

    .sidebar-section-title {
        padding: 12px 20px;
        color: #4CAF50;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        border-top: 1px solid rgba(76, 175, 80, 0.2);
        border-left: 3px solid #4CAF50;
        margin-top: 10px;
        background: rgba(76, 175, 80, 0.05);
    }

    .sidebar-menu-item {
        list-style: none;
    }

    .sidebar-menu-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        color: #b8bcc9;
        text-decoration: none;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        font-size: 14px;
        font-weight: 500;
        position: relative;
        cursor: pointer;
    }

    .sidebar-menu-link:hover {
        background: linear-gradient(90deg, rgba(255, 167, 38, 0.12) 0%, rgba(76, 175, 80, 0.06) 100%);
        color: #e6e8ef;
        border-left-color: #4CAF50;
        padding-left: 24px;
    }

    .sidebar-menu-link.active {
        background: linear-gradient(90deg, rgba(255, 167, 38, 0.18) 0%, rgba(76, 175, 80, 0.08) 100%);
        color: #FFA726;
        border-left-color: #FFA726;
        font-weight: 600;
        box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.18);
    }

    .sidebar-menu-link i {
        font-size: 16px;
        width: 24px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sidebar-menu-link span {
        flex: 1;
    }

    .sidebar-menu-link .badge {
        background: rgba(255, 167, 38, 0.8);
        color: #fff;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        margin-left: auto;
    }

    /* Main Content Area */
    .admin-main {
        flex: 1;
        margin-left: 280px;
        padding: 30px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    /* Content Header */
    .content-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .content-title {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .content-title h1 {
        margin: 0;
        font-size: 28px;
        color: #1a1a2e;
        font-weight: 800;
    }

    .content-title i {
        font-size: 32px;
        background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .content-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .action-btn {
        background: #4CAF50;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .action-btn:hover {
        background: #45a049;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }

    .action-btn.secondary {
        background: #f0f0f0;
        color: #333;
        border: 1px solid #ddd;
    }

    .action-btn.secondary:hover {
        background: #e0e0e0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .action-btn.danger {
        background: #f44336;
    }

    .action-btn.danger:hover {
        background: #da190b;
    }

    /* Data Tables */
    .data-table-container {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px;
        border-bottom: 1px solid #f0f0f0;
        gap: 15px;
        flex-wrap: wrap;
    }

    .search-box {
        flex: 1;
        min-width: 250px;
    }

    .search-box input {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        transition: all 0.3s ease;
    }

    .search-box input:focus {
        outline: none;
        border-color: #4CAF50;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }

    .table-filters {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .filter-select {
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
    }

    .filter-select:focus {
        outline: none;
        border-color: #4CAF50;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }

    .table-wrapper {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    thead {
        background: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
    }

    th {
        padding: 16px;
        text-align: left;
        font-weight: 700;
        color: #495057;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        border: none;
    }

    td {
        padding: 14px 16px;
        border-bottom: 1px solid #f0f0f0;
    }

    tbody tr {
        transition: all 0.2s ease;
    }

    tbody tr:hover {
        background: #f8f9fa;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    /* Status Badges */
    .status-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.disponible,
    .status-badge.approved {
        background: rgba(76, 175, 80, 0.15);
        color: #2E7D32;
    }

    .status-badge.maintenance,
    .status-badge.pending {
        background: rgba(255, 167, 38, 0.15);
        color: #E67E22;
    }

    .status-badge.rejected,
    .status-badge.cancelled {
        background: rgba(244, 67, 54, 0.15);
        color: #c62828;
    }

    /* Action Buttons in Table */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .icon-btn {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #f0f0f0;
        color: #666;
    }

    .icon-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .icon-btn.edit {
        background: rgba(33, 150, 243, 0.15);
        color: #1976d2;
    }

    .icon-btn.edit:hover {
        background: rgba(33, 150, 243, 0.3);
    }

    .icon-btn.delete {
        background: rgba(244, 67, 54, 0.15);
        color: #d32f2f;
    }

    .icon-btn.delete:hover {
        background: rgba(244, 67, 54, 0.3);
    }

    .icon-btn.view {
        background: rgba(76, 175, 80, 0.15);
        color: #2E7D32;
    }

    .icon-btn.view:hover {
        background: rgba(76, 175, 80, 0.3);
    }

    /* Empty State */
    .empty-state {
        padding: 60px 20px;
        text-align: center;
        color: #999;
    }

    .empty-state i {
        font-size: 48px;
        color: #ddd;
        margin-bottom: 15px;
    }

    .empty-state p {
        font-size: 14px;
        margin: 0;
        color: #999;
    }

    /* Tab Content */
    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .admin-sidebar {
            width: 260px;
        }

        .admin-main {
            margin-left: 260px;
        }

        .table-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .search-box {
            min-width: 100%;
        }

        .table-filters {
            justify-content: space-between;
        }
    }

    @media (max-width: 768px) {
        .admin-container {
            flex-direction: column;
            margin-top: 70px;
        }

        .admin-sidebar {
            width: 100%;
            height: auto;
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            bottom: auto;
            border-right: none;
            border-bottom: 2px solid rgba(76, 175, 80, 0.2);
            max-height: 0;
            overflow: hidden;
            margin-bottom: 0;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.1);
            transition: max-height 0.3s ease;
            z-index: 899;
        }

        .admin-sidebar.sidebar-open {
            max-height: 70vh;
            overflow-y: auto;
        }

        .admin-main {
            margin-left: 0;
            padding: 20px;
            margin-top: 0;
        }

        .content-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .sidebar-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 10px;
            padding: 15px;
        }

        .sidebar-menu-link {
            padding: 12px;
            text-align: center;
            flex-direction: column;
            gap: 6px;
        }

        .sidebar-menu-link span {
            font-size: 11px;
        }

        th {
            font-size: 11px;
            padding: 12px;
        }

        td {
            font-size: 12px;
            padding: 10px;
        }
    }

    @media (max-width: 480px) {
        .admin-main {
            padding: 15px;
        }

        .content-title h1 {
            font-size: 20px;
        }

        .action-buttons {
            flex-wrap: wrap;
        }

        .table-wrapper {
            border-radius: 8px;
        }

        table {
            font-size: 12px;
        }

        th, td {
            padding: 8px;
        }
    }
</style>

<div class="admin-container">
    <!-- Sidebar Navigation -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-cog"></i> Administration</h3>
        </div>

        <ul class="sidebar-menu">
            <!-- Data Management Section -->
            <li class="sidebar-menu-section">
                <div class="sidebar-section-title">Gestion des Données</div>
                <!-- Menu Utilisateurs supprimé -->
                <li class="sidebar-menu-item">
                    <a href="#kilometrage" class="sidebar-menu-link active" data-tab="kilometrage">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Kilométrage</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="#requests" class="sidebar-menu-link" data-tab="requests">
                        <i class="fas fa-file-alt"></i>
                        <span>Demandes</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="#reservations" class="sidebar-menu-link" data-tab="reservations">
                        <i class="fas fa-calendar-check"></i>
                        <span>Réservations</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="#planning-windows" class="sidebar-menu-link" data-tab="planning-windows">
                        <i class="fas fa-window-maximize"></i>
                        <span>Fenêtres Planification</span>
                    </a>
                </li>
                <!-- Menu Utilisateurs supprimé -->
            </li>

            <!-- System Section -->
            <li class="sidebar-menu-section">
                <div class="sidebar-section-title">Système</div>

                <li class="sidebar-menu-item">
                    <a href="{{ route('utilisateurs.index') }}" class="sidebar-menu-link">
                        <i class="fas fa-users"></i>
                        <span>Gestion des Utilisateurs</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a href="{{ route('settings.show') }}" class="sidebar-menu-link">
                        <i class="fas fa-sliders-h"></i>
                        <span>Paramètres</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a href="{{ route('logout') }}" class="sidebar-menu-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </li>
        </ul>
    </aside>

    <!-- Main Content Area -->
    <main class="admin-main">
        <!-- Content Header -->
        <div class="content-header">
            <div class="content-title">
                <i class="fas fa-chart-line"></i>
                <h1 id="page-title">Gestion Centralisée des Données</h1>
            </div>
            <div class="content-actions">
                <button class="action-btn" id="export-btn" onclick="exportCurrentTab()">
                    <i class="fas fa-file-csv"></i> Exporter CSV
                </button>
                <button class="action-btn" id="pdf-btn" onclick="downloadPdfReport()">
                    <i class="fas fa-file-pdf"></i> Télécharger PDF
                </button>
            </div>
        </div>

        <!-- Tab Contents - Will be populated dynamically -->
        @yield('admin-content')
    </main>
</div>

<script>
    // Tab Switching Functionality
    document.querySelectorAll('[data-tab]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            const tabName = this.dataset.tab;
            
            // Update active link
            document.querySelectorAll('.sidebar-menu-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');

            // Load tab content
            loadTabContent(tabName);

            // Update page title
            updatePageTitle(tabName);

            // Scroll to top
            document.querySelector('.admin-main').scrollTop = 0;

            // Close sidebar on mobile after selection
            if (window.innerWidth <= 768) {
                const sidebar = document.querySelector('.admin-sidebar');
                sidebar.classList.remove('sidebar-open');
            }
        });
    });

    function loadTabContent(tabName) {
        const container = document.querySelector('.admin-main');
        
        // Show loading state
        const contentArea = container.querySelector('[data-content-area]') || document.createElement('div');
        contentArea.innerHTML = '<div style="padding: 40px; text-align: center;"><i class="fas fa-spinner fa-spin" style="font-size: 28px; color: #4CAF50;"></i></div>';

        // Fetch content via AJAX
        fetch(`/admin/tab/${tabName}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            contentArea.innerHTML = html;
            contentArea.setAttribute('data-content-area', tabName);
            
            // Initialize table features (search, filters, actions)
            initializeTableFeatures();
        })
        .catch(error => {
            console.error('Error loading tab:', error);
            contentArea.innerHTML = '<div style="padding: 40px; text-align: center; color: #f44336;"><i class="fas fa-exclamation-circle" style="font-size: 28px;"></i><p style="margin-top: 15px;">Erreur lors du chargement du contenu. Veuillez réessayer.</p></div>';
        });
    }

    function updatePageTitle(tabName) {
        const titles = {
            'kilometrage': 'Gestion du Kilométrage',
            'requests': 'Gestion des Demandes',
            'reservations': 'Gestion des Réservations',
            'planning-windows': 'Gestion des Fenêtres de Planification',
            'notifications': 'Notifications'
        };

        const icons = {
            'kilometrage': 'fas fa-tachometer-alt',
            'requests': 'fas fa-file-alt',
            'reservations': 'fas fa-calendar-check',
            'planning-windows': 'fas fa-window-maximize',
            'notifications': 'fas fa-bell'
        };

        document.getElementById('page-title').textContent = titles[tabName] || 'Admin Dashboard';
        document.querySelector('.content-title i').className = icons[tabName] || 'fas fa-chart-line';
    }

    function initializeTableFeatures() {
        // Search functionality
        const searchInputs = document.querySelectorAll('.search-box input');
        searchInputs.forEach(input => {
            input.addEventListener('keyup', function() {
                filterTable(this);
            });
        });

        // Filter functionality
        const filterSelects = document.querySelectorAll('.filter-select');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                filterTable(this);
            });
        });
    }

    function filterTable(element) {
        const table = element.closest('.data-table-container').querySelector('table');
        const rows = table.querySelectorAll('tbody tr');
        const searchValue = element.value.toLowerCase();

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    }

    function exportCurrentTab() {
        const activeTab = document.querySelector('.sidebar-menu-link.active');
        if (!activeTab) {
            alert('Aucun onglet actif trouvé');
            return;
        }

        const tabName = activeTab.dataset.tab;
        const table = document.querySelector('.data-table-container table');
        if (!table) {
            alert('Aucun tableau à exporter');
            return;
        }

        // Show loading indicator
        const exportBtn = document.getElementById('export-btn');
        const originalContent = exportBtn.innerHTML;
        exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exportation...';
        exportBtn.disabled = true;

        // Generate professional CSV with metadata
        const timestamp = new Date().toISOString();
        const userName = '{{ Auth::user()->name }}';
        const tabTitles = {
            'kilometrage': 'Kilométrage',
            'requests': 'Demandes',
            'reservations': 'Réservations',
            'planning-windows': 'Fenêtres de Planification',
            'notifications': 'Notifications'
        };

        const delimiter = ';';
        let csv = [];
        
        // Add metadata header
        csv.push(`"# Export SDCC - ${tabTitles[tabName] || tabName}"`);
        csv.push(`"# Généré le: ${new Date().toLocaleString('fr-FR')}"`);
        csv.push(`"# Utilisateur: ${userName}"`);
        csv.push(`"# Onglet: ${tabTitles[tabName] || tabName}"`);
        csv.push(`"# Total d'enregistrements: ${table.querySelectorAll('tbody tr').length}"`);
        csv.push(''); // Empty line after metadata

        // Add table headers
        const headerRow = table.querySelector('thead tr');
        if (headerRow) {
            const headers = headerRow.querySelectorAll('th');
            const csvHeader = [];
            headers.forEach(th => {
                const value = th.textContent.trim().replace(/"/g, '""');
                csvHeader.push(`"${value}"`);
            });
            csv.push(csvHeader.join(delimiter));
        }

        // Add table data (exclude action buttons)
        const dataRows = table.querySelectorAll('tbody tr');
        dataRows.forEach(row => {
            const cols = row.querySelectorAll('td');
            const csvRow = [];
            
            // Skip last column if it's actions (usually the last column)
            const colCount = cols.length - 1;
            for (let i = 0; i < colCount; i++) {
                const col = cols[i];
                let value = col.textContent.trim().replace(/"/g, '""');
                
                // Clean up role badges and other UI elements
                value = value.replace(/\s+/g, ' ').trim();
                
                csvRow.push(`"${value}"`);
            }
            csv.push(csvRow.join(delimiter));
        });

        // Add footer
        csv.push('');
        csv.push(`"# Fin du rapport - ${new Date().toLocaleString('fr-FR')}"`);

        // UTF-8 BOM + CRLF for better Excel compatibility
        const csvContent = '\uFEFF' + csv.join('\r\n') + '\r\n';
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        const filename = `export-${tabTitles[tabName] || tabName}-${new Date().getTime()}.csv`;
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);

        // Restore button state
        setTimeout(() => {
            exportBtn.innerHTML = originalContent;
            exportBtn.disabled = false;
        }, 1000);
    }

    function downloadPdfReport() {
        const activeTab = document.querySelector('.sidebar-menu-link.active');
        if (!activeTab) {
            alert('Aucun onglet actif trouvé');
            return;
        }

        const tabName = activeTab.dataset.tab;
        
        // Check if PDF is available for this tab
        const availableTabs = ['reservations'];
        if (!availableTabs.includes(tabName)) {
            alert('Le rapport PDF n\'est pas disponible pour cet onglet');
            return;
        }

        // Show loading indicator
        const pdfBtn = document.getElementById('pdf-btn');
        const originalContent = pdfBtn.innerHTML;
        pdfBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Génération...';
        pdfBtn.disabled = true;

        // Get PDF stats first (optional - shows progress)
        fetch(`/api/pdf/stats?tab=${tabName}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log(`Génération du PDF: ${data.stats.total_records} enregistrements (${data.stats.file_size_estimate})`);
                }
            })
            .catch(error => {
                console.warn('Impossible de récupérer les statistiques PDF:', error);
            });

        // Download the PDF
        const pdfUrl = `/pdf/${tabName}`;
        const link = document.createElement('a');
        link.href = pdfUrl;
        link.download = ''; // Let the server set the filename
        link.style.display = 'none';
        document.body.appendChild(link);
        
        // Handle download completion
        link.onload = function() {
            setTimeout(() => {
                pdfBtn.innerHTML = originalContent;
                pdfBtn.disabled = false;
                document.body.removeChild(link);
            }, 1000);
        };

        link.onerror = function() {
            alert('Erreur lors du téléchargement du PDF');
            pdfBtn.innerHTML = originalContent;
            pdfBtn.disabled = false;
            document.body.removeChild(link);
        };

        link.click();
        
        // Fallback: restore button state after 5 seconds
        setTimeout(() => {
            pdfBtn.innerHTML = originalContent;
            pdfBtn.disabled = false;
        }, 5000);
    }

    // Mobile sidebar toggle
    function toggleSidebar() {
        const sidebar = document.querySelector('.admin-sidebar');
        sidebar.classList.toggle('sidebar-open');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('sidebarToggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                const sidebar = document.querySelector('.admin-sidebar');
                if (sidebar) {
                    sidebar.classList.toggle('sidebar-open');
                    toggleBtn.classList.toggle('active');
                }
            });
        }

        // Close sidebar when clicking outside on mobile
        if (window.innerWidth <= 768) {
            document.addEventListener('click', function(e) {
                const sidebar = document.querySelector('.admin-sidebar');
                const toggleBtn = document.getElementById('sidebarToggle');
                
                if (sidebar && toggleBtn && 
                    !sidebar.contains(e.target) && 
                    !toggleBtn.contains(e.target) &&
                    sidebar.classList.contains('sidebar-open')) {
                    sidebar.classList.remove('sidebar-open');
                    toggleBtn.classList.remove('active');
                }
            });
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.querySelector('.admin-sidebar');
            if (window.innerWidth > 768) {
                sidebar.classList.remove('sidebar-open');
            }
        });

        // Load kilometrage tab by default
        loadTabContent('kilometrage');
    });

</script>

@yield('admin-scripts')
@endsection
