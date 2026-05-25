@extends('layouts.app')

@section('title', 'SDCC - Gestion des Zones')

@section('content')
<div style="background: linear-gradient(135deg, #f5f7fa 0%, #f0f3f7 100%); min-height: 100vh; padding: 16px 20px;">
    <div style="max-width: none; width: 100%; margin: 0; padding: 0; box-sizing: border-box;">

        {{-- Header Section --}}
        <div style="margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 20px;">
                <div>
                    <h1 style="margin: 0; color: #1a2332; font-size: 32px; font-weight: 700; display: flex; align-items: center; gap: 12px;">
                        <i class="fas fa-map-marker-alt" style="color: #4CAF50; font-size: 36px;"></i>
                        Gestion des Zones
                    </h1>
                    <p style="margin: 8px 0 0; color: #667085; font-size: 15px;">
                        Gérez les zones de planification et assignez des utilisateurs et des véhicules
                    </p>
                </div>

                <a href="{{ route('zones.create') }}" style="
                    background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
                    color: white;
                    padding: 14px 28px;
                    border-radius: 12px;
                    text-decoration: none;
                    display: inline-flex;
                    align-items: center;
                    gap: 10px;
                    font-weight: 600;
                    font-size: 15px;
                    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
                ">
                    <i class="fas fa-plus-circle"></i> Nouvelle Zone
                </a>
            </div>
        </div>

        {{-- Zones Grid --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 20px;">

            @forelse($zones as $zone)
                <div style="
                    background: white;
                    border-radius: 16px;
                    padding: 28px;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                    transition: all 0.3s ease;
                    border-left: 5px solid {{ $zone->status === 'active' ? '#4CAF50' : '#999' }};
                    position: relative;
                ">

                    {{-- STATUS (ONLY ACTIVE / INACTIVE) --}}
                    <div style="
                        position: absolute;
                        top: 20px;
                        right: 20px;
                        padding: 8px 14px;
                        border-radius: 20px;
                        font-size: 12px;
                        font-weight: 700;
                        text-transform: uppercase;
                        background: {{ $zone->status === 'active'
                            ? 'linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%)'
                            : 'linear-gradient(135deg, #e2e3e5 0%, #d6d7db 100%)' }};
                        color: {{ $zone->status === 'active' ? '#2E7D32' : '#424242' }};
                        display: flex;
                        align-items: center;
                        gap: 6px;
                    ">
                        <i class="fas {{ $zone->status === 'active' ? 'fa-check-circle' : 'fa-circle-xmark' }}"></i>
                        {{ $zone->status === 'active' ? 'Active' : 'Inactive' }}
                    </div>

                    {{-- Zone Header --}}
                    <div style="margin-bottom: 20px; padding-right: 120px;">
                        <h3 style="margin: 0 0 8px; color: #1a2332; font-size: 20px; font-weight: 700;">
                            {{ $zone->name }}
                        </h3>
                        <p style="margin: 0; color: #667085; font-size: 14px;">
                            {{ $zone->description ?: '— Aucune description —' }}
                        </p>
                    </div>

                    <div style="height: 1px; background: #e5e7eb; margin: 18px 0;"></div>

                    {{-- Stats --}}
                    <div style="display: flex; gap: 20px; margin-bottom: 24px;">
                        <div style="flex: 1; display: flex; align-items: center; gap: 12px;">
                            <i class="fas fa-users" style="color: #4CAF50; font-size: 20px;"></i>
                            <div>
                                <div style="font-size: 22px; font-weight: 700;">{{ $zone->users_count ?? 0 }}</div>
                                <div style="font-size: 12px; color: #999;">Utilisateurs</div>
                                @if($zone->users_count > 0)
                                    <div style="font-size: 10px; color: #4CAF50; margin-top: 4px;">
                                        <i class="fas fa-robot"></i> Auto-assignés
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div style="flex: 1; display: flex; align-items: center; gap: 12px;">
                            <i class="fas fa-car" style="color: #FFA726; font-size: 20px;"></i>
                            <div>
                                <div style="font-size: 22px; font-weight: 700;">{{ $zone->cars_count ?? 0 }}</div>
                                <div style="font-size: 12px; color: #999;">Véhicules</div>
                                @if($zone->cars_count > 0)
                                    <div style="font-size: 10px; color: #FFA726; margin-top: 4px;">
                                        <i class="fas fa-link"></i> Assignés
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div style="display: flex; gap: 12px;">
                        <a href="{{ route('zones.edit', $zone) }}" style="
                            flex: 1;
                            background: #4CAF50;
                            color: white;
                            padding: 12px;
                            border-radius: 10px;
                            text-align: center;
                            text-decoration: none;
                            font-weight: 600;
                        ">
                            <i class="fas fa-pen"></i> Éditer
                        </a>

                        <form method="POST" action="{{ route('zones.destroy', $zone) }}" style="flex: 1;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="
                                width: 100%;
                                background: #ff8c00;
                                color: white;
                                padding: 12px;
                                border: none;
                                border-radius: 10px;
                                font-weight: 600;
                                cursor: pointer;
                            ">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                    </div>

                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 80px; background: white; border-radius: 16px;">
                    <i class="fas fa-inbox" style="font-size: 50px; opacity: 0.3;"></i>
                    <p>Aucune zone créée</p>
                </div>
            @endforelse

        </div>
    </div>
</div>
@endsection