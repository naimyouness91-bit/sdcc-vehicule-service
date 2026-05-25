<!-- ======================= DATA ENTRY SECTION ======================= -->
<style>
    .data-entry-section {
        margin-top: 40px;
    }

    .data-entry-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 3px solid #4CAF50;
    }

    .data-entry-header h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: #333;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .data-entry-header h2 i {
        color: #4CAF50;
        font-size: 28px;
    }

    .data-entry-header .badge {
        background: #4CAF50;
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .action-card {
        background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%);
        border-left: 4px solid #4CAF50;
        padding: 18px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        background: linear-gradient(135deg, #ffffff 0%, #f9f9f9 100%);
    }

    .action-card.employees {
        border-left-color: #1976d2;
    }

    .action-card.vehicles {
        border-left-color: #f57c00;
    }

    .action-card.kilometrage {
        border-left-color: #c62828;
    }

    .action-card-icon {
        font-size: 32px;
        margin-bottom: 12px;
        color: #666;
    }

    .action-card.employees .action-card-icon {
        color: #1976d2;
    }

    .action-card.vehicles .action-card-icon {
        color: #f57c00;
    }

    .action-card.kilometrage .action-card-icon {
        color: #c62828;
    }

    .action-card h3 {
        margin: 0 0 8px 0;
        font-size: 16px;
        font-weight: 700;
        color: #333;
    }

    .action-card p {
        margin: 0 0 12px 0;
        font-size: 12px;
        color: #999;
        line-height: 1.4;
    }

    .action-card-button {
        background: linear-gradient(135deg, #4CAF50, #66BB6A);
        color: white;
        border: none;
        padding: 8px 14px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        width: 100%;
        justify-content: center;
    }

    .action-card-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .mini-stat {
        background: white;
        padding: 18px;
        border-radius: 8px;
        border-left: 4px solid;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        text-align: center;
    }

    .mini-stat.employees {
        border-left-color: #1976d2;
    }

    .mini-stat.vehicles {
        border-left-color: #f57c00;
    }

    .mini-stat.active {
        border-left-color: #2e7d32;
    }

    .mini-stat-value {
        font-size: 28px;
        font-weight: 800;
        color: #333;
        margin-bottom: 6px;
    }

    .mini-stat-label {
        font-size: 12px;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .mini-stat-icon {
        display: inline-block;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin: 0 auto 8px;
    }

    .mini-stat.employees .mini-stat-icon {
        background: rgba(25, 118, 210, 0.1);
        color: #1976d2;
    }

    .mini-stat.vehicles .mini-stat-icon {
        background: rgba(245, 124, 0, 0.1);
        color: #f57c00;
    }

    .mini-stat.active .mini-stat-icon {
        background: rgba(46, 125, 50, 0.1);
        color: #2e7d32;
    }

    @media (max-width: 768px) {
        .quick-actions {
            grid-template-columns: 1fr;
        }

        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }

        .data-entry-header h2 {
            font-size: 20px;
        }
    }
</style>

<div class="data-entry-section">
    <!-- Header -->
    <div class="data-entry-header">
        <h2>
            <i class="fas fa-database"></i>
            Gestion des Données
        </h2>
        <span class="badge">Centrale d'entrée de données</span>
    </div>

    <!-- Quick Action Cards -->
    <div class="quick-actions">
        <!-- (Employé UI archived) -->

        <!-- Add Vehicle Card -->
        <div class="action-card vehicles" onclick="openModal('addVehicleModal')">
            <div class="action-card-icon">
                <i class="fas fa-car-plus"></i>
            </div>
            <h3>Ajouter Véhicule</h3>
            <p>Enregistrer un nouveau véhicule dans la flotte</p>
            <button class="action-card-button">
                <i class="fas fa-arrow-right"></i> Commencer
            </button>
        </div>

        <!-- Add Kilometrage Card -->
        <div class="action-card kilometrage" onclick="openModal('addKilometrageModal')">
            <div class="action-card-icon">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            <h3>Enregistrer Kilométrage</h3>
            <p>Mettre à jour le kilométrage des véhicules</p>
            <button class="action-card-button">
                <i class="fas fa-arrow-right"></i> Commencer
            </button>
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="stats-row">
        <!-- Employees summary removed (archived) -->

        <div class="mini-stat vehicles">
            <div class="mini-stat-icon">
                <i class="fas fa-car"></i>
            </div>
            <div class="mini-stat-value" id="totalVehicles">0</div>
            <div class="mini-stat-label">Véhicules</div>
        </div>

        <div class="mini-stat active">
            <div class="mini-stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="mini-stat-value" id="availableVehicles">0</div>
            <div class="mini-stat-label">Disponibles</div>
        </div>
    </div>
</div>

<script>
    // Update statistics
    function updateDataEntryStats() {
        // This will be called after data loads
        const totalVeh = allData.vehicles?.length || 0;
        const available = allData.vehicles?.filter(v => v.status === 'disponible')?.length || 0;

        document.getElementById('totalVehicles').textContent = totalVeh;
        document.getElementById('availableVehicles').textContent = available;
    }

    // Call this after data loads (hook into existing data loading)
    document.addEventListener('dataLoaded', updateDataEntryStats);
</script>
