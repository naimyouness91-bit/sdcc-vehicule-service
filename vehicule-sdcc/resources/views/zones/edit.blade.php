@extends('layouts.app')

@section('title', 'SDCC - Modifier la Zone')

@section('content')
<div style="background: linear-gradient(135deg, #f5f7fa 0%, #f0f3f7 100%); min-height: 100vh; padding: 16px 20px;">
    <div style="max-width: 1100px; width: 100%; margin: 0 auto; padding: 0; box-sizing: border-box;">

        {{-- Header --}}
        <div style="margin-bottom: 32px;">
            <a href="{{ route('zones.index') }}" style="
                display: inline-flex; align-items: center; gap: 8px;
                color: #667085; font-size: 14px; text-decoration: none;
                margin-bottom: 16px; transition: color 0.2s;
            " onmouseover="this.style.color='#00a86b'" onmouseout="this.style.color='#667085'">
                <i class="fas fa-arrow-left"></i> Retour aux zones
            </a>

            <h1 style="margin: 0; color: #1a2332; font-size: 28px; font-weight: 700; display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-pen" style="color: #00d084; font-size: 28px;"></i>
                Modifier la Zone
                <span style="
                    background: linear-gradient(135deg,#d4f0e5,#b8e6d4);
                    color: #00613e; padding: 6px 14px; border-radius: 20px;
                    font-size: 16px; font-weight: 600;
                ">{{ $zone->name }}</span>
            </h1>

            <p style="margin: 8px 0 0; color: #667085; font-size: 15px;">
                Modifiez les informations, les utilisateurs et les véhicules assignés à cette zone.
            </p>
        </div>

        {{-- Flash --}}
        @if ($message = Session::get('success'))
            <div style="
                background: linear-gradient(135deg,#d4f0e5,#b8e6d4);
                border-left: 4px solid #00a86b; color: #00613e;
                padding: 16px 20px; border-radius: 12px; margin-bottom: 24px;
                display: flex; align-items: center; gap: 12px;
            ">
                <i class="fas fa-check-circle"></i>
                <div style="flex:1;">{{ $message }}</div>
                <button onclick="this.parentElement.style.display='none'" style="background:none;border:none;cursor:pointer;">×</button>
            </div>
        @endif

        {{-- Errors --}}
        @if ($errors->any())
            <div style="
                background: linear-gradient(135deg,#fff3e0,#ffe0b2);
                border-left: 4px solid #e65100;
                padding: 16px 20px; border-radius: 12px; margin-bottom: 24px;
            ">
                <strong>Veuillez corriger :</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM --}}
        <div style="background:white;border-radius:16px;padding:32px;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

            <form method="POST" action="{{ route('zones.update', $zone) }}">
                @csrf
                @method('PUT')

                {{-- ZONE DETAILS --}}
                <div style="background:#f8f9fa;padding:20px;border-radius:12px;margin-bottom:24px;">
                    <h3 style="margin:0 0 16px;color:#1a2332;font-size:18px;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-map-marker-alt" style="color:#4CAF50;"></i>
                        Informations de la Zone
                    </h3>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                        {{-- NAME --}}
                        <div>
                            <label>Nom</label>
                            <input type="text" name="name" value="{{ old('name', $zone->name) }}"
                                style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;">
                        </div>

                        {{-- STATUS --}}
                        <div>
                            <label style="font-weight:600;">Statut</label>
                            <div style="display:flex;gap:12px;margin-top:8px;">
                                @foreach([
                                    'active' => ['Active', '#00d084', '#00613e', '#d4f0e5', 'fa-check-circle'],
                                    'in_progress' => ['En cours', '#FFA726', '#bf360c', '#fff3e0', 'fa-hourglass-half'],
                                    'inactive' => ['Inactive', '#bdbdbd', '#757575', '#f5f5f5', 'fa-times-circle'],
                                ] as $value => $props)

                                <label style="
                                    flex:1;cursor:pointer;
                                    border:2px solid {{ old('status', $zone->status) === $value ? $props[1] : '#e5e7eb' }};
                                    background:{{ old('status', $zone->status) === $value ? $props[3] : 'white' }};
                                    padding:10px;border-radius:8px;
                                    display:flex;align-items:center;gap:8px;
                                ">
                                    <input type="radio" name="status" value="{{ $value }}"
                                        {{ old('status', $zone->status) === $value ? 'checked' : '' }}
                                        style="accent-color:{{ $props[1] }};">

                                    <i class="fas {{ $props[4] }}" style="color:{{ $props[1] }}"></i>

                                    <span style="color:{{ $props[2] }};font-weight:600;">
                                        {{ $props[0] }}
                                    </span>
                                </label>

                                @endforeach
                            </div>
                        </div>

                        {{-- TEAM --}}
                        <div>
                            <label style="font-weight:600;">Équipe</label>
                            <input type="text" name="team" value="{{ old('team', $zone->team) }}" placeholder="Ex: Technique, Logistique, Commercial..."
                                style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;">
                            <div style="font-size:11px;color:#667085;margin-top:4px;">
                                <i class="fas fa-info-circle"></i> Les utilisateurs seront automatiquement assignés à la zone correspondante
                            </div>
                        </div>

                        {{-- DESCRIPTION --}}
                        <div style="grid-column:1 / -1;">
                            <label>Description</label>
                            <textarea name="description" rows="3"
                                style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;">{{ old('description', $zone->description) }}</textarea>
                        </div>
                    </div>

                {{-- USERS ASSIGNMENT --}}
                <div style="background:#e3f2fd;padding:20px;border-radius:12px;margin-bottom:24px;">
                    <h3 style="margin:0 0 16px;color:#1a2332;font-size:18px;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-users" style="color:#4CAF50;"></i>
                        Utilisateurs Assignés
                        <span style="margin-left:auto;font-size:14px;color:#667085;">
                            {{ $zone->users->count() }} sélectionné(s)
                        </span>
                    </h3>

                    <div style="max-height:200px;overflow-y:auto;border:1px solid #ddd;border-radius:8px;padding:12px;">
                        @foreach($employees as $employee)
                            <label style="
                                display:flex;align-items:center;gap:8px;padding:8px;margin-bottom:4px;
                                border-radius:6px;cursor:pointer;
                                background:{{ in_array($employee->id, $zone->users->pluck('id')->toArray()) ? '#e8f5e8' : 'white' }};
                                border:1px solid {{ in_array($employee->id, $zone->users->pluck('id')->toArray()) ? '#4CAF50' : '#ddd' }};
                            ">
                                <input type="checkbox" name="user_ids[]" value="{{ $employee->id }}"
                                    {{ in_array($employee->id, $zone->users->pluck('id')->toArray()) ? 'checked' : '' }}
                                    style="accent-color:#4CAF50;">

                                <div style="flex:1;">
                                    <div style="font-weight:600;color:#1a2332;">{{ $employee->name }}</div>
                                    <div style="font-size:12px;color:#667085;">{{ $employee->email }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- VEHICLES ASSIGNMENT --}}
                <div style="background:#fff3e0;padding:20px;border-radius:12px;margin-bottom:24px;">
                    <h3 style="margin:0 0 16px;color:#1a2332;font-size:18px;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-car" style="color:#FFA726;"></i>
                        Véhicules Assignés
                        <span style="margin-left:auto;font-size:14px;color:#667085;">
                            {{ $zone->cars->count() }} sélectionné(s)
                        </span>
                    </h3>

                    <div style="max-height:200px;overflow-y:auto;border:1px solid #ddd;border-radius:8px;padding:12px;">
                        @foreach($cars as $car)
                            <label style="
                                display:flex;align-items:center;gap:8px;padding:8px;margin-bottom:4px;
                                border-radius:6px;cursor:pointer;
                                background:{{ in_array($car->id, $zone->cars->pluck('id')->toArray()) ? '#e8f5e8' : 'white' }};
                                border:1px solid {{ in_array($car->id, $zone->cars->pluck('id')->toArray()) ? '#FFA726' : '#ddd' }};
                            ">
                                <input type="checkbox" name="car_ids[]" value="{{ $car->id }}"
                                    {{ in_array($car->id, $zone->cars->pluck('id')->toArray()) ? 'checked' : '' }}
                                    style="accent-color:#FFA726;">

                                <div style="flex:1;">
                                    <div style="font-weight:600;color:#1a2332;">{{ $car->name }}</div>
                                    <div style="font-size:12px;color:#667085;">
                                        {{ $car->model }} - {{ $car->matricule }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- BUTTONS --}}
                <div style="display:flex;gap:12px;">
                    <a href="{{ route('zones.index') }}"
                        style="flex:1;text-align:center;padding:14px;border:1px solid #ddd;border-radius:10px;text-decoration:none;">
                        Annuler
                    </a>

                    <button type="submit"
                        style="flex:2;background:#00d084;color:white;border:none;border-radius:10px;font-weight:600;">
                        <i class="fas fa-save" style="margin-right:8px;"></i>
                        Sauvegarder les Modifications
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection