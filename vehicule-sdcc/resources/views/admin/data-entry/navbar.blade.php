<!-- ==================== DATA ENTRY NAVIGATION BAR ==================== -->
<div class="data-entry-container">
    <!-- Sidebar Navigation -->
    <aside class="data-entry-sidebar">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <h3>📊 Gestion des Données</h3>
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                // Employee form handling removed (archived). Use Utilisateurs page for user management.
                <li class="nav-divider"></li>

                <!-- Admin Utilities -->
                @if(auth()->user()->hasRole(['admin', 'super_admin']))
                // Employee form handling removed (archived). Use Utilisateurs page for user management.
            // Employee form handling removed (archived). Use Utilisateurs page for user management.

            <div class="form-group">
                <label for="emp_email">Email *</label>
                <input type="email" id="emp_email" name="email" placeholder="email@example.com" required>
                <span class="error-message" id="emp_email_error"></span>
            </div>

            <div class="form-group">
                <label for="emp_service">Service *</label>
                @include('utilisateurs.partials.service-dropdown', ['selectId' => 'emp_service', 'selectName' => 'service', 'required' => true])
                <span class="error-message" id="emp_service_error"></span>
            </div>

            <div class="form-group">
                <label for="emp_role">Rôle *</label>
                <select id="emp_role" name="role" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="employee">Employé</option>
                    <option value="admin">Admin</option>
                </select>
                <span class="error-message" id="emp_role_error"></span>
            </div>

            <div class="form-group">
                <label for="emp_password">Mot de passe *</label>
                <input type="password" id="emp_password" name="password" placeholder="Minimum 6 caractères" required>
                <span class="error-message" id="emp_password_error"></span>
            </div>

            <div class="form-group">
                <label for="emp_password_confirm">Confirmer mot de passe *</label>
                <input type="password" id="emp_password_confirm" name="password_confirmation" placeholder="Confirmer le mot de passe" required>
                <span class="error-message" id="emp_password_confirm_error"></span>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <span class="spinner" style="display:none;"></span>
                    <span class="text">Ajouter</span>
                </button>
                <button type="button" class="btn-cancel" id="cancelEmployeeForm">Annuler</button>
            </div>
        </form>
    </div>
</div>

<!-- Vehicle Modal -->
<div class="modal" id="vehicleModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>🚗 <span id="modalTitle">Ajouter Véhicule</span></h3>
            <button class="modal-close" id="closeVehicleModal">&times;</button>
        </div>
        <form id="vehicleForm" class="form-two-col">
            @csrf
            <div class="form-group">
                <label for="veh_name">Modèle *</label>
                <input type="text" id="veh_name" name="name" placeholder="ex. Toyota Corolla" required>
                <span class="error-message" id="veh_name_error"></span>
            </div>

            <div class="form-group">
                <label for="veh_matricule">Immatriculation *</label>
                <input type="text" id="veh_matricule" name="matricule" placeholder="ex. AB123CD" required>
                <span class="error-message" id="veh_matricule_error"></span>
            </div>

            <div class="form-group">
                <label for="veh_model">Type *</label>
                <input type="text" id="veh_model" name="model" placeholder="ex. Sedan" required>
                <span class="error-message" id="veh_model_error"></span>
            </div>

            <div class="form-group">
                <label for="veh_year">Année *</label>
                <input type="number" id="veh_year" name="year" placeholder="ex. 2024" min="2000" required>
                <span class="error-message" id="veh_year_error"></span>
            </div>

            <div class="form-group">
                <label for="veh_km">Kilométrage *</label>
                <input type="number" id="veh_km" name="km" placeholder="ex. 0" min="0" required>
                <span class="error-message" id="veh_km_error"></span>
            </div>

            <div class="form-group">
                <label for="veh_status">Statut *</label>
                <select id="veh_status" name="status" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="disponible">Disponible</option>
                    <option value="maintenance">Maintenance</option>
                </select>
                <span class="error-message" id="veh_status_error"></span>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <span class="spinner" style="display:none;"></span>
                    <span class="text">Ajouter</span>
                </button>
                <button type="button" class="btn-cancel" id="cancelVehicleForm">Annuler</button>
            </div>
        </form>
    </div>
</div>

<!-- Kilometrage Modal -->
<div class="modal" id="kilometrageModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>⚙️ <span id="modalTitle">Ajouter Kilométrage</span></h3>
            <button class="modal-close" id="closeKilometrageModal">&times;</button>
        </div>
        <form id="kilometrageForm" class="form-two-col">
            @csrf
            <div class="form-group full-width">
                <label for="km_car_id">Véhicule *</label>
                <select id="km_car_id" name="car_id" required>
                    <option value="">-- Sélectionner --</option>
                </select>
                <span class="error-message" id="km_car_id_error"></span>
            </div>

            <div class="form-group">
                <label for="km_current">Kilométrage actuel</label>
                <input type="number" id="km_current" disabled placeholder="Auto-rempli">
            </div>

            <div class="form-group">
                <label for="km_new">Nouveau kilométrage *</label>
                <input type="number" id="km_new" name="new_km" placeholder="ex. 5000" required>
                <span class="error-message" id="km_new_error"></span>
            </div>

            <div class="form-group">
                <label for="km_date">Date *</label>
                <input type="date" id="km_date" name="update_date" required>
                <span class="error-message" id="km_date_error"></span>
            </div>

            <div class="form-group">
                <label for="km_employee">Employé (optionnel)</label>
                <select id="km_employee" name="user_id">
                    <option value="">-- Aucun --</option>
                </select>
            </div>

            <div class="form-group full-width">
                <label for="km_reason">Raison (optionnel)</label>
                <textarea id="km_reason" name="reason" placeholder="Motif de la mise à jour" rows="3"></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <span class="spinner" style="display:none;"></span>
                    <span class="text">Enregistrer</span>
                </button>
                <button type="button" class="btn-cancel" id="cancelKilometrageForm">Annuler</button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== JAVASCRIPT ==================== -->
<style>
    /* ==================== LAYOUT ==================== */
    .data-entry-container {
        display: flex;
        gap: 0;
        height: calc(100vh - 200px);
        background: #f5f5f5;
        border-radius: 8px;
        overflow: hidden;
        margin-top: 20px;
    }

    /* ==================== SIDEBAR ==================== */
    .data-entry-sidebar {
        width: 280px;
        background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
        color: white;
        display: flex;
        flex-direction: column;
        border-right: 3px solid #3498db;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
        transition: transform 0.3s ease;
        z-index: 100;
    }

    .sidebar-header {
        padding: 20px;
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .sidebar-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sidebar-toggle {
        display: none;
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        padding: 5px;
    }

    .sidebar-toggle span {
        display: block;
        width: 20px;
        height: 2px;
        background: white;
        margin: 5px 0;
        transition: 0.3s;
    }

    .sidebar-nav {
        flex: 1;
        padding: 15px 0;
        overflow-y: auto;
    }

    .sidebar-nav ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .nav-item {
        width: 100%;
        padding: 15px 20px;
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.7);
        text-align: left;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }

    .nav-item:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border-left-color: #3498db;
    }

    .nav-item.active {
        background: rgba(52, 152, 219, 0.2);
        color: white;
        border-left-color: #3498db;
        font-weight: 600;
    }

    .nav-icon {
        font-size: 18px;
        flex-shrink: 0;
    }

    .nav-label {
        flex: 1;
    }

    .nav-badge {
        background: #e74c3c;
        color: white;
        font-size: 12px;
        padding: 2px 6px;
        border-radius: 10px;
        font-weight: 600;
        flex-shrink: 0;
    }

    .nav-divider {
        height: 1px;
        background: rgba(255, 255, 255, 0.1);
        margin: 10px 0;
    }

    .sidebar-footer {
        padding: 15px 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 12px;
        color: rgba(255, 255, 255, 0.5);
        text-align: center;
    }

    /* ==================== MAIN CONTENT ==================== */
    .data-entry-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: white;
        overflow: hidden;
    }

    .content-header {
        padding: 20px 30px;
        border-bottom: 2px solid #ecf0f1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(90deg, #f8f9fa 0%, #ffffff 100%);
    }

    .content-title {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
        color: #2c3e50;
    }

    .content-actions {
        display: flex;
        gap: 10px;
    }

    .btn-add-primary,
    .btn-export {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .btn-add-primary {
        background: linear-gradient(90deg, #4CAF50, #66BB6A);
        color: white;
    }

    .btn-add-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }

    .btn-export {
        background: linear-gradient(90deg, #1976d2, #1e88e5);
        color: white;
    }

    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
    }

    .tabs-content {
        flex: 1;
        overflow: auto;
        padding: 20px 30px;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }

    /* ==================== MODALS ==================== */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        padding: 20px;
        border-bottom: 2px solid #4CAF50;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(90deg, #4CAF50, #66BB6A);
        color: white;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 28px;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-close:hover {
        opacity: 0.8;
    }

    .form-two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        padding: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-weight: 600;
        margin-bottom: 6px;
        color: #2c3e50;
        font-size: 13px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        transition: border-color 0.3s ease;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #4CAF50;
        box-shadow: 0 0 5px rgba(76, 175, 80, 0.2);
    }

    .error-message {
        color: #e74c3c;
        font-size: 12px;
        margin-top: 4px;
        display: none;
    }

    .error-message.show {
        display: block;
    }

    .form-actions {
        grid-column: 1 / -1;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 10px;
        padding-top: 15px;
        border-top: 1px solid #ecf0f1;
    }

    .btn-submit,
    .btn-cancel {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-submit {
        background: #4CAF50;
        color: white;
    }

    .btn-submit:hover {
        background: #45a049;
    }

    .btn-cancel {
        background: #ecf0f1;
        color: #2c3e50;
    }

    .btn-cancel:hover {
        background: #d5dbdb;
    }

    .spinner {
        display: inline-block;
        width: 12px;
        height: 12px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* ==================== RESPONSIVE ==================== */
    @media (max-width: 1024px) {
        .data-entry-sidebar {
            width: 240px;
        }
    }

    @media (max-width: 768px) {
        .data-entry-container {
            flex-direction: column;
            height: auto;
            min-height: 500px;
        }

        .data-entry-sidebar {
            width: 100%;
            max-height: 70px;
            flex-direction: row;
            border-right: none;
            border-bottom: 3px solid #3498db;
        }

        .sidebar-header {
            flex: 1;
            padding: 15px;
            border-bottom: none;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-toggle {
            display: block;
        }

        .sidebar-nav {
            position: absolute;
            top: 70px;
            left: 0;
            right: 0;
            background: #2c3e50;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            z-index: 99;
        }

        .sidebar-nav.active {
            max-height: 400px;
            border-bottom: 2px solid #3498db;
        }

        .sidebar-nav ul {
            flex-direction: column;
        }

        .nav-item {
            border-left: 3px solid transparent;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-footer {
            display: none;
        }

        .content-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }

        .content-actions {
            width: 100%;
        }

        .btn-add-primary,
        .btn-export {
            flex: 1;
        }

        .tabs-content {
            padding: 15px;
        }

        .form-two-col {
            grid-template-columns: 1fr;
        }

        .modal-content {
            width: 95%;
        }
    }

    @media (max-width: 480px) {
        .content-title {
            font-size: 18px;
        }

        .btn-label {
            display: none;
        }

        .modal-header h3 {
            font-size: 16px;
        }

        .tabs-content {
            padding: 10px;
        }
    }
</style>

<script>
    // ==================== TAB SWITCHING ==================== 
    document.querySelectorAll('.nav-item').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            switchTab(tabName);
        });
    });

    function switchTab(tabName) {
        // Update sidebar active state
        document.querySelectorAll('.nav-item').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');

        // Update content pane
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('active');
        });
        document.getElementById(`tab-${tabName}`).classList.add('active');

        // Update title
        const titles = {
            'employees': 'Employés',
            'vehicles': 'Véhicules',
            'kilometrage': 'Kilométrage',
            'requests': 'Demandes',
            'reservations': 'Réservations',
            'zones': 'Zones'
        };
        document.getElementById('contentTitle').textContent = titles[tabName] || 'Section';

        // Load data for the tab
        loadTabData(tabName);

        // Update add button
        updateAddButton(tabName);
    }

    function loadTabData(tabName) {
        // This will be populated with real data loading logic
        console.log('Loading data for tab:', tabName);
    }

    function updateAddButton(tabName) {
        const btnAddNew = document.getElementById('btnAddNew');
        const modals = {
            'employees': 'employeeModal',
            'vehicles': 'vehicleModal',
            'kilometrage': 'kilometrageModal'
        };

        btnAddNew.onclick = () => {
            if (modals[tabName]) {
                document.getElementById(modals[tabName]).classList.add('active');
            }
        };
    }

    // ==================== MODAL MANAGEMENT ==================== 
    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }

    document.getElementById('closeVehicleModal').addEventListener('click', () => closeModal('vehicleModal'));
    document.getElementById('closeKilometrageModal').addEventListener('click', () => closeModal('kilometrageModal'));

    document.getElementById('cancelVehicleForm').addEventListener('click', () => closeModal('vehicleModal'));
    document.getElementById('cancelKilometrageForm').addEventListener('click', () => closeModal('kilometrageModal'));

    // Close modal when clicking outside
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    });

    // Close modal with ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(modal => {
                modal.classList.remove('active');
            });
        }
    });

    // ==================== SIDEBAR TOGGLE (Mobile) ==================== 
    document.getElementById('sidebarToggle').addEventListener('click', () => {
        document.querySelector('.sidebar-nav').classList.toggle('active');
    });

    // ==================== FORM SUBMISSION HANDLERS ==================== 
    
    // Employee Form
    document.getElementById('employeeForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const btn = e.target.querySelector('.btn-submit');
        const spinner = btn.querySelector('.spinner');
        const text = btn.querySelector('.text');
        
        btn.disabled = true;
        spinner.style.display = 'inline-block';
        
        try {
            const response = await fetch('#', { /* archived endpoint */
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast('Employé ajouté avec succès', 'success');
                closeModal('employeeModal');
                document.getElementById('employeeForm').reset();
                loadEmployeesData();
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const errorEl = document.getElementById(`emp_${field}_error`);
                        if (errorEl) {
                            errorEl.textContent = data.errors[field][0];
                            errorEl.classList.add('show');
                        }
                    });
                }
                showToast(data.message || 'Erreur lors de l\'ajout', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Erreur réseau', 'error');
        } finally {
            btn.disabled = false;
            spinner.style.display = 'none';
        }
    });
    
    // Vehicle Form
    document.getElementById('vehicleForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const btn = e.target.querySelector('.btn-submit');
        const spinner = btn.querySelector('.spinner');
        
        btn.disabled = true;
        spinner.style.display = 'inline-block';
        
        try {
            const response = await fetch('{{ route("data-entry.vehicles.store") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast('Véhicule ajouté avec succès', 'success');
                closeModal('vehicleModal');
                document.getElementById('vehicleForm').reset();
                loadVehiclesData();
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const errorEl = document.getElementById(`veh_${field}_error`);
                        if (errorEl) {
                            errorEl.textContent = data.errors[field][0];
                            errorEl.classList.add('show');
                        }
                    });
                }
                showToast(data.message || 'Erreur lors de l\'ajout', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Erreur réseau', 'error');
        } finally {
            btn.disabled = false;
            spinner.style.display = 'none';
        }
    });
    
    // Kilometrage Form
    document.getElementById('kilometrageForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const btn = e.target.querySelector('.btn-submit');
        const spinner = btn.querySelector('.spinner');
        
        btn.disabled = true;
        spinner.style.display = 'inline-block';
        
        try {
            const response = await fetch('{{ route("data-entry.kilometrage.store") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast('Kilométrage enregistré', 'success');
                closeModal('kilometrageModal');
                document.getElementById('kilometrageForm').reset();
                loadKilometrageData();
                loadVehiclesData();
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const errorEl = document.getElementById(`km_${field}_error`);
                        if (errorEl) {
                            errorEl.textContent = data.errors[field][0];
                            errorEl.classList.add('show');
                        }
                    });
                }
                showToast(data.message || 'Erreur', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Erreur réseau', 'error');
        } finally {
            btn.disabled = false;
            spinner.style.display = 'none';
        }
    });

    // ==================== POPULATE VEHICLE DROPDOWN ==================== 
    async function populateVehicleDropdown() {
        try {
            const response = await fetch('{{ route("data-entry.vehicles.get") }}');
            const vehicles = await response.json();
            
            const select = document.getElementById('km_car_id');
            select.innerHTML = '<option value="">-- Sélectionner --</option>';
            
            vehicles.forEach(v => {
                const option = document.createElement('option');
                option.value = v.id;
                option.textContent = `${v.name} (${v.km} km)`;
                option.dataset.km = v.km;
                select.appendChild(option);
            });
        } catch (err) {
            console.error('Error loading vehicles:', err);
        }
    }

    // Update current km when vehicle selected
    document.getElementById('km_car_id').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const km = selected.dataset.km || 0;
        document.getElementById('km_current').value = km;
    });

    // Employees dropdown population removed (archived). Use Utilisateurs page for full user lists.

    // ==================== CLEAR FORM ERRORS ON INPUT ==================== 
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('focus', function() {
            const fieldName = this.name || this.id;
            const errorEl = document.querySelector(`.error-message[id*="${fieldName}"]`);
            if (errorEl) {
                errorEl.classList.remove('show');
                errorEl.textContent = '';
            }
        });
    });

    // ==================== EXPORT FUNCTIONALITY ==================== 
    document.getElementById('btnExport').addEventListener('click', async () => {
        const activeTab = document.querySelector('.nav-item.active').dataset.tab;
        
        if (activeTab === 'employees') {
            showToast('Export des employés...', 'info');
            // TODO: Implement export for employees
        } else if (activeTab === 'vehicles') {
            showToast('Export des véhicules...', 'info');
            // TODO: Implement export for vehicles
        } else if (activeTab === 'kilometrage') {
            showToast('Export du kilométrage...', 'info');
            // TODO: Implement export for kilometrage
        } else {
            showToast('Export non disponible pour cette section', 'info');
        }
    });

    // ==================== INITIALIZE ==================== 
    document.addEventListener('DOMContentLoaded', () => {
        switchTab('kilometrage');
        populateVehicleDropdown();
        
        // Set today's date as default for kilometrage
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('km_date').value = today;
    });

</script>
