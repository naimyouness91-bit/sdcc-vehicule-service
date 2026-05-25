<style>
    .content-wrapper {
        padding: 20px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
        gap: 30px;
    }

    .page-header-info h1 {
        font-size: 32px;
        font-weight: 700;
        color: #111;
        margin: 0 0 5px 0;
    }

    .page-header-info p {
        font-size: 14px;
        color: #111;
        font-weight: 500;
        margin: 0;
    }

    .add-request-btn {
        background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .add-request-btn:hover {
        background: linear-gradient(135deg, #2E7D32 0%, #FB8C00 100%);
    }

    .vehicles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: 34px;
        max-width: 520px;
    }

    .vehicle-card {
        border-radius: 8px;
        padding: 8px 10px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .status-wrap {
        text-align: right;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        color: #1B5E20;
        background: linear-gradient(135deg, #C8E6C9 0%, #FFE0B2 100%);
    }

    .vehicle-icon {
        font-size: 36px;
        line-height: 1;
        text-align: center;
        color: #e65100;
    }

    .vehicle-name {
        font-size: 28px;
        font-weight: 700;
        color: #111;
        margin: 2px 0 0 0;
    }

    .vehicle-model {
        font-size: 20px;
        color: #1a1a1a;
        margin: 0;
    }

    .plate-row {
        display: flex;
        gap: 6px;
        align-items: center;
        font-size: 21px;
        color: #2E7D32;
        line-height: 1.1;
    }

    .plate-row i {
        font-size: 16px;
    }

    .reserve-btn {
        background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
        color: white;
        border: none;
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        margin-top: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .reserve-btn:hover {
        background: linear-gradient(135deg, #2E7D32 0%, #FB8C00 100%);
    }

    .reserve-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #999;
    }

    .empty-state i {
        font-size: 30px;
        display: block;
        margin-bottom: 8px;
        color: #bdbdbd;
    }
</style>

<div class="content-wrapper">
    <div class="page-header">
        <div class="page-header-info">
            <h1><i class="fas fa-shuttle-van" style="margin-right: 12px;"></i>Véhicules de service</h1>
            <p><i class="fas fa-info-circle" style="margin-right: 6px;"></i>{{ count($cars) }} véhicules dans la flotte</p>
        </div>
        <a href="{{ route('mes-demandes.create') }}" class="add-request-btn">
            <i class="fas fa-plus"></i> Faire une demande
        </a>
    </div>

    <div class="vehicles-grid">
        @foreach($cars as $car)
            <div class="vehicle-card">
                <div class="status-wrap">
                    @if($car->status === 'disponible')
                        <span class="status-badge"><i class="fas fa-circle-check"></i> Disponible</span>
                    @else
                        <span class="status-badge"><i class="fas fa-screwdriver-wrench"></i> Maintenance</span>
                    @endif
                </div>

                <div class="vehicle-icon"><i class="fas fa-car" style="font-size: 40px; color: #f97316;"></i></div>

                <div>
                    <h3 class="vehicle-name">{{ $car->name }}</h3>
                    <p class="vehicle-model">{{ $car->model }}</p>
                </div>

                <div class="plate-row">
                    <i class="fas fa-id-card"></i>
                    <span>{{ strtoupper($car->matricule) }}</span>
                </div>

                @if($car->status === 'disponible')
                    <a href="{{ route('mes-demandes.create', ['car_id' => $car->id]) }}" class="reserve-btn"><i class="fas fa-calendar-check"></i> Réserver</a>
                @else
                    <button class="reserve-btn" disabled><i class="fas fa-ban"></i> Non disponible</button>
                @endif
            </div>
        @endforeach
    </div>

    @if(count($cars) == 0)
        <div class="empty-state">
            <i class="fas fa-car-side"></i>
            Aucun véhicule disponible
        </div>
    @endif
</div>
