@extends('layouts.app')

@section('title', 'SDCC - Véhicules de service')

@section('content')
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
        color: #1a1a1a;
        margin: 0 0 5px 0;
    }

    .page-header-info p {
        font-size: 14px;
        color: #666;
        font-weight: 500;
        margin: 0;
    }

    .add-request-btn {
        background: #1e88e5;
        color: white;
        border: none;
        padding: 12px 28px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 8px rgba(30, 136, 229, 0.3);
    }

    .add-request-btn:hover {
        background: #1565c0;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(30, 136, 229, 0.4);
    }

    .vehicles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 40px;
        margin-bottom: 40px;
    }

    .vehicle-card {
        background: white;
        border-radius: 10px;
        padding: 28px 24px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        gap: 16px;
        position: relative;
    }

    .vehicle-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }

    .status-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        width: fit-content;
        margin: 0 auto;
    }

    .status-available {
        background: #C8E6C9;
        color: #2E7D32;
        border: 1.5px solid #4CAF50;
    }

    .status-maintenance {
        background: #ffebee;
        color: #c62828;
        border: 1.5px solid #ef5350;
    }

    .vehicle-icon {
        font-size: 64px;
        color: #f44336;
        margin: 0 auto;
    }

    .vehicle-name {
        font-size: 18px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
    }

    .vehicle-model {
        font-size: 13px;
        color: #888;
        font-weight: 500;
        margin: 4px 0 0 0;
    }

    .license-plate {
        background: white;
        border: 2px solid #1e88e5;
        border-radius: 4px;
        padding: 8px 16px;
        font-size: 14px;
        font-weight: 700;
        color: #1e88e5;
        letter-spacing: 1px;
        font-family: 'Courier New', monospace;
        text-align: center;
        margin: 8px 0;
    }

    .reserve-btn {
        background: #1e88e5;
        color: white;
        border: none;
        padding: 12px 28px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
        box-shadow: 0 2px 6px rgba(30, 136, 229, 0.3);
        margin: 4px 0 0 0;
    }

    .reserve-btn:hover {
        background: #1565c0;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(30, 136, 229, 0.4);
    }

    .admin-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 8px;
        flex-wrap: wrap;
    }

    .admin-btn {
        flex: 1;
        min-width: 80px;
        padding: 10px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        color: white;
        transition: all 0.3s;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .edit-btn {
        background: #4CAF50;
    }

    .edit-btn:hover {
        background: #2E7D32;
        transform: translateY(-1px);
    }

    .delete-btn {
        background: #f44336;
    }

    .delete-btn:hover {
        background: #d32f2f;
        transform: translateY(-1px);
    }

    .empty-state {
        text-align: center;
        padding: 80px 40px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .empty-state i {
        font-size: 60px;
        color: #ddd;
        display: block;
        margin-bottom: 20px;
    }

    .empty-state p {
        color: #999;
        font-size: 16px;
        font-weight: 500;
        margin: 0;
    }

    @media (max-width: 1024px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .add-request-btn {
            width: 100%;
            justify-content: center;
        }

        .vehicles-grid {
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 30px;
        }
    }

    @media (max-width: 768px) {
        .content-wrapper {
            padding: 15px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header-info h1 {
            font-size: 26px;
        }

        .vehicles-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .vehicle-card {
            padding: 20px 16px;
            gap: 12px;
        }

        .vehicle-icon {
            font-size: 48px;
        }

        .vehicle-name {
            font-size: 16px;
        }

        .admin-btn {
            min-width: 70px;
            padding: 8px 12px;
            font-size: 11px;
        }
    }

    @media (max-width: 480px) {
        .content-wrapper {
            padding: 12px;
        }

        .page-header-info h1 {
            font-size: 22px;
        }

        .vehicles-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .vehicle-card {
            padding: 18px 14px;
            gap: 10px;
        }

        .vehicle-icon {
            font-size: 40px;
        }

        .vehicle-name {
            font-size: 15px;
        }
    }
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-info">
            <h1>Véhicules de service</h1>
            <p>{{ count($cars) ?? 2 }} véhicules dans la flotte</p>
        </div>
        <a href="{{ route('mes-demandes.create') }}" class="add-request-btn">
            <i class="fas fa-plus"></i> Faire une demande
        </a>
    </div>

    <!-- Vehicles Grid -->
    <div class="vehicles-grid">
        @foreach($cars as $car)
            <div class="vehicle-card">
                <div class="vehicle-icon"><i class="fas fa-car" style="font-size: 32px; color: #4CAF50;"></i></div>
                
                <div>
                    <h3 class="vehicle-name">{{ $car->name }}</h3>
                    <p class="vehicle-model">{{ $car->model }}</p>
                </div>

                <div class="license-plate">{{ $car->matricule }}</div>

                <div>
                    @if($car->status === 'disponible')
                        <span class="status-badge status-available">Disponible</span>
                    @else
                        <span class="status-badge status-maintenance">Maintenance</span>
                    @endif
                </div>

                @if($car->status === 'disponible')
                    <a href="{{ route('mes-demandes.create', ['car_id' => $car->id]) }}" class="reserve-btn">
                        Réserver
                    </a>
                @else
                    <button class="reserve-btn" disabled style="opacity: 0.5; cursor: not-allowed;">
                        Non disponible
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    @if(count($cars) == 0)
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Aucun véhicule disponible</p>
        </div>
    @endif
</div>

@endsection
