@extends('layouts.app')
@section('title', 'SDCC - Nouvelle Demande')
@section('content')

<style>
    .back-link {
        display: inline-block;
        color: #111;
        text-decoration: none;
        font-size: 14px;
        margin-bottom: 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .back-link:hover {
        color: #111;
        text-decoration: none;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #111;
        margin: 10px 0 5px 0;
    }

    .page-subtitle {
        font-size: 13px;
        color: #111;
        margin-bottom: 25px;
        font-weight: 500;
    }

    .request-card {
        background: linear-gradient(135deg, #e8f5e9 0%, #fff3e0 100%);
        border-radius: 8px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
        border-left: 4px solid #4CAF50;
        box-shadow: 0 2px 6px rgba(76, 175, 80, 0.1);
    }

    .request-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
        box-shadow: 0 2px 6px rgba(76, 175, 80, 0.3);
    }

    .request-info h4 {
        font-size: 14px;
        margin: 0 0 3px 0;
        color: #1a1a1a;
        font-weight: 600;
    }

    .request-info p {
        font-size: 12px;
        color: #666;
        margin: 0;
    }

    .form-section {
        margin-bottom: 0;
    }

    .form-section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 15px;
        margin-bottom: 20px;
        border-bottom: 2px solid transparent;
        border-image: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%) 1;
    }

    .form-section-header i {
        color: #4CAF50;
        font-size: 18px;
        background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 1px 1px rgba(76, 175, 80, 0.2));
    }

    .form-section-title {
        font-size: 13px;
        font-weight: 600;
        background: linear-gradient(135deg, #4CAF50 0%, #FFA726 75%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        margin-bottom: 20px;
    }

    .form-row.full {
        grid-template-columns: 1fr;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-size: 12px;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .form-group label span {
        color: #FFA726;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 13px;
        font-family: inherit;
        background: white;
        transition: all 0.3s ease;
    }

    .form-group input[type="date"],
    .form-group input[type="time"],
    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group select {
        padding: 10px 12px 10px 12px;
    }

    /* Icon input styling - adds left padding for icon */
    .form-group > div[style*="position: relative"] > input,
    .form-group > div[style*="position: relative"] > select,
    .form-group > div[style*="position: relative"] > textarea {
        padding: 10px 12px 10px 36px !important;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border: 1px solid transparent;
        background: white;
        box-shadow: 0 0 0 2px white, 0 0 0 4px #4CAF50, inset 0 0 0 1px #FFA726;
    }

    .form-group > div[style*="position: relative"] > input:focus,
    .form-group > div[style*="position: relative"] > select:focus,
    .form-group > div[style*="position: relative"] > textarea:focus {
        outline: none;
        border: 1px solid transparent;
        background: white;
        box-shadow: 0 0 0 2px white, 0 0 0 4px #4CAF50, inset 0 0 0 1px #FFA726;
        padding: 10px 12px 10px 36px !important;
    }

    /* Input prefix icon animation */
    .form-group > div[style*="position: relative"] > i {
        transition: color 0.3s ease, left 0.3s ease;
        z-index: 1;
    }

    .form-group > div[style*="position: relative"] > input:focus ~ i,
    .form-group > div[style*="position: relative"] > select:focus ~ i,
    .form-group > div[style*="position: relative"] > textarea:focus ~ i {
        color: #4CAF50 !important;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }

    .hint {
        font-size: 11px;
        color: #999;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .hint i {
        font-size: 10px;
        color: #BBB;
    }

    .char-counter {
        text-align: right;
        font-size: 12px;
        color: #FFA726;
        margin-top: 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 4px;
    }

    .char-counter i {
        font-size: 11px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-top: 40px;
        padding-top: 25px;
        border-top: 1px solid #f0f0f0;
    }

    .btn {
        padding: 12px 32px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .btn i {
        font-size: 14px;
    }

    .btn-secondary {
        background: white;
        color: #666;
        border: 1px solid #ddd;
    }

    .btn-secondary:hover {
        background: #f5f5f5;
        border-color: #bbb;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-primary {
        background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(76, 175, 80, 0.3);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2E7D32 0%, #E65100 100%);
        box-shadow: 0 4px 8px rgba(76, 175, 80, 0.4);
        transform: translateY(-2px);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .print-sheet {
        display: none;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .request-card {
            flex-direction: column;
            text-align: center;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

@includeIf('partials.print-styles')

<div class="content-wrapper">
    <a href="{{ route('mes-demandes.index') }}" class="back-link"><i class="fas fa-arrow-left"></i> Retour</a>

    <h1 class="page-title"><i class="fas fa-clipboard-list" style="margin-right: 12px; color: #4CAF50;"></i>Nouvelle demande de véhicule</h1>
    <p class="page-subtitle"><i class="fas fa-info-circle" style="margin-right: 8px; color: #667085;"></i>Soumis aux Moyens Généraux pour approbation</p>

    <div class="print-sheet" id="printSheet">
        <h2>SDCC - Demande de vehicule</h2>
        <div class="print-grid">
            <div><strong>Employe:</strong> {{ Auth::user()->name }}</div>
            <div><strong>Service:</strong> {{ Auth::user()->service ?? 'N/A' }}</div>
            <div><strong>Vehicule:</strong> <span id="printVehicle">Non selectionne</span></div>
            <div><strong>Statut:</strong> En attente</div>
            <div><strong>Date usage:</strong> <span id="printStartDate">-</span></div>
            <div><strong>Heure depart:</strong> <span id="printStartTime">-</span></div>
            <div><strong>Date restitution:</strong> <span id="printEndDate">-</span></div>
            <div><strong>Heure restitution:</strong> <span id="printEndTime">-</span></div>
            <div style="grid-column: 1 / -1;"><strong>Destination:</strong> <span id="printDestination">-</span></div>
        </div>
    </div>

    <!-- User Card -->
    <div class="request-card">
        <div class="request-avatar"><i class="fas fa-user" style="font-size: 24px;"></i></div>
        <div class="request-info">
            <h4><i class="fas fa-id-badge" style="margin-right: 6px; color: #4CAF50; font-size: 13px;"></i>{{ Auth::user()->name }}</h4>
            <p><i class="fas fa-building" style="margin-right: 6px; color: #999; font-size: 12px;"></i>{{ Auth::user()->service ?? 'Moyens Généraux' }}</p>
            @if (Auth::user()->getZone())
                <p style="font-size: 11px; color: #4CAF50; font-weight: 600; margin-top: 5px;">
                    <i class="fas fa-location-dot" style="margin-right: 5px;"></i>Zone: {{ Auth::user()->getZone()->name }}
                </p>
            @else
                <p style="font-size: 11px; color: #e53935; font-weight: 600; margin-top: 5px;">
                    <i class="fas fa-circle-xmark" style="margin-right: 5px;"></i>Aucune zone assignée
                </p>
            @endif
        </div>
    </div>

    @if (!Auth::user()->getZone())
        <div style="background:#fff3e0;border-left:4px solid #ffa726;color:#e65100;padding:12px 14px;margin-bottom:16px;border-radius:6px;font-weight:600;display:flex;align-items:flex-start;gap:10px;">
            <i class="fas fa-triangle-exclamation" style="font-size: 16px; flex-shrink: 0; margin-top: 2px;"></i>
            <div>
                <strong>Attention:</strong> Vous n'avez pas de zone de planification assignée. Vous pouvez créer une demande avec les véhicules disponibles en mode général. Veuillez contacter l'administrateur pour finaliser votre affectation de zone.
            </div>
        </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('mes-demandes.store') }}">
        @csrf
        {{-- Champ Employé visible uniquement pour admin/super_admin --}}
        @if(Auth::user()->hasAnyRole(['admin','super_admin']))
        <div class="form-section" style="margin-bottom: 22px;">
            <div class="form-section-header" style="margin-bottom: 12px;">
                <i class="fas fa-user"></i>
                <span class="form-section-title">Employé</span>
            </div>
            <div class="form-row full" style="margin-bottom: 0;">
                <div class="form-group">
                    <label><i class="fas fa-user" style="margin-right: 6px; color: #4CAF50;"></i>EMPLOYÉ <span style="color: #FFA726;">*</span></label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <i class="fas fa-user" style="position: absolute; left: 12px; color: #999; font-size: 14px; pointer-events: none;"></i>
                        <select id="employeeIdField" name="employee_id" required style="padding: 10px 12px 10px 36px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; width: 100%;">
                            <option value="" disabled>-- Sélectionner un employé --</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->name }} ({{ $employee->service }})</option>
                            @endforeach
                        </select>
                    </div>
                    @if($errors->has('employee_id'))
                        <div style="color: #e53935; font-weight: 600; margin-top:6px;">{{ $errors->first('employee_id') }}</div>
                    @endif
                    <small class="form-text text-muted">L’employé pour qui la réservation est créée.</small>
                </div>
            </div>
        </div>
        @else
            <input type="hidden" name="employee_id" value="{{ Auth::id() }}">
        @endif
        <div class="form-section" style="margin-bottom: 22px;">
            <div class="form-section-header" style="margin-bottom: 12px;">
                <i class="fas fa-car"></i>
                <span class="form-section-title">Véhicule</span>
            </div>
            
            <div class="form-row full" style="margin-bottom: 0;">
                <div class="form-group">
                    <label><i class="fas fa-car" style="margin-right: 6px; color: #4CAF50;"></i>VÉHICULE <span style="color: #FFA726;">*</span></label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <i class="fas fa-car" style="position: absolute; left: 12px; color: #999; font-size: 14px; pointer-events: none;"></i>
                        <select id="carIdField" name="car_id" required style="padding: 10px 12px 10px 36px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; width: 100%;">
                            <option value="" selected disabled>-- Sélectionner un véhicule --</option>
                            @forelse($availableVehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" data-availability="{{ $vehicle->availability_type ?? 'both' }}" {{ $carId == $vehicle->id ? 'selected' : '' }}>
                                    {{ $carId == $vehicle->id ? '✓ ' : '' }}{{ $vehicle->name }} ({{ $vehicle->matricule }})
                                </option>
                            @empty
                                <option value="" disabled>Aucun véhicule disponible</option>
                            @endforelse
                        </select>
                    </div>
                    @if ($carId)
                        <div style="background: linear-gradient(135deg, #d4edda 0%, #c8e6c9 100%); border-left: 3px solid #4CAF50; padding: 8px 12px; margin-top: 8px; border-radius: 4px; display: flex; align-items: center; gap: 8px; font-size: 12px; color: #1b5e20; font-weight: 600;">
                            <i class="fas fa-check-circle"></i>
                            <span>Véhicule présélectionné</span>
                        </div>
                    @endif
                    <div id="vehicleAvailabilityAlert" style="display: none; background-color: #FFF3E0; border: 1px solid #FFE0B2; border-left: 4px solid #FFA726; border-radius: 6px; padding: 12px 14px; margin-top: 8px; font-size: 12px; color: #E65100; align-items: flex-start; gap: 10px;">
                        <i class="fas fa-calendar-times" style="flex-shrink: 0; margin-top: 2px; font-size: 14px;"></i>
                        <div>
                            <strong>Disponibilité restreinte:</strong> Ce véhicule n'est disponible que le <strong>weekend (samedi et dimanche)</strong>. Vous ne pourrez le réserver que pour ces jours.
                        </div>
                    </div>
                    <div class="hint" id="carHint"><i class="fas fa-info-circle" style="margin-right: 5px;"></i>Sélectionnez une date pour vérifier la disponibilité pour cette date spécifique (semaine/weekend)</div>
                </div>
            </div>
        </div>

        <!-- Période demandée -->
        <div class="form-section">
            <div class="form-section-header">
                <i class="fas fa-calendar-days"></i>
                <span class="form-section-title">Période demandée</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-calendar-check" style="margin-right: 6px; color: #4CAF50;"></i>DATE D'USAGE <span style="color: #FFA726;">*</span></label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <i class="fas fa-calendar" style="position: absolute; left: 12px; color: #999; font-size: 14px; pointer-events: none;"></i>
                        <input type="date" id="startDateField" name="start_date" required placeholder="mm/dd/yyyy" value="{{ old('start_date', $startDate ?? '') }}" style="padding: 10px 12px 10px 36px; width: 100%;">
                    </div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-clock" style="margin-right: 6px; color: #4CAF50;"></i>HEURE DE DÉPART <span style="color: #FFA726;">*</span></label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <i class="fas fa-hourglass-start" style="position: absolute; left: 12px; color: #999; font-size: 14px; pointer-events: none;"></i>
                        <input type="time" name="start_time" required placeholder="--:-- --" style="padding: 10px 12px 10px 36px; width: 100%;">
                    </div>
                </div>
            </div>

            <!-- Reservation Notice -->
            <div style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(255, 152, 0, 0.08) 100%); border: 1px solid rgba(76, 175, 80, 0.2); border-left: 4px solid #4CAF50; border-radius: 6px; padding: 12px 14px; margin-bottom: 20px; font-size: 13px;">
                <div style="display: flex; gap: 10px; align-items: flex-start;">
                    <i class="fas fa-info-circle" style="color: #2E7D32; margin-top: 2px; flex-shrink: 0;"></i>
                    <div style="color: #2E7D32; line-height: 1.5;">
                        <strong>Important:</strong> Les réservations urgentes sont autorisées immédiatement si le véhicule est disponible.
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-calendar-check" style="margin-right: 6px; color: #4CAF50;"></i>DATE DE RESTITUTION <span style="color: #FFA726;">*</span></label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <i class="fas fa-calendar" style="position: absolute; left: 12px; color: #999; font-size: 14px; pointer-events: none;"></i>
                        <input type="date" name="end_date" required placeholder="mm/dd/yyyy" value="{{ old('end_date', $startDate ?? '') }}" style="padding: 10px 12px 10px 36px; width: 100%;">
                    </div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-undo" style="margin-right: 6px; color: #4CAF50;"></i>HEURE DE RESTITUTION</label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <i class="fas fa-hourglass-end" style="position: absolute; left: 12px; color: #999; font-size: 14px; pointer-events: none;"></i>
                        <input type="time" name="end_time" placeholder="--:-- --" style="padding: 10px 12px 10px 36px; width: 100%;">
                    </div>
                </div>
            </div>

            <!-- 'Heure de retour prévue' removed (no longer needed) -->
        </div>

        <!-- Déplacement -->
        <div class="form-section" style="margin-top: 30px;">
            <div class="form-section-header">
                <i class="fas fa-map-marker-alt"></i>
                <span class="form-section-title">Déplacement</span>
            </div>

            <div class="form-row full">
                <div class="form-group">
                    <label><i class="fas fa-location-dot" style="margin-right: 6px; color: #4CAF50;"></i>DESTINATION <span style="color: #FFA726;">*</span></label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <i class="fas fa-map-pin" style="position: absolute; left: 12px; color: #999; font-size: 14px; pointer-events: none; z-index: 1;"></i>
                        <select id="destinationSelect" name="destination" required data-old-value="{{ old('destination', '') }}" style="padding: 10px 12px 10px 36px; width: 100%; box-sizing: border-box;">
                            <option value="" {{ old('destination') ? '' : 'selected' }} disabled>-- Sélectionner une destination --</option>
                            <option value="Casablanca" {{ old('destination') === 'Casablanca' ? 'selected' : '' }}>Casablanca</option>
                            <option value="Rabat" {{ old('destination') === 'Rabat' ? 'selected' : '' }}>Rabat</option>
                            <option value="Marrakech" {{ old('destination') === 'Marrakech' ? 'selected' : '' }}>Marrakech</option>
                            <option value="Jorf Lasfar" {{ old('destination') === 'Jorf Lasfar' ? 'selected' : '' }}>Jorf Lasfar</option>
                            <option value="Station-service Kénitra" {{ old('destination') === 'Station-service Kénitra' ? 'selected' : '' }}>Station-service Kénitra</option>
                            <option value="Station-service Sidi Kacem" {{ old('destination') === 'Station-service Sidi Kacem' ? 'selected' : '' }}>Station-service Sidi Kacem</option>
                            <option value="Station-service ADM Oualidia" {{ old('destination') === 'Station-service ADM Oualidia' ? 'selected' : '' }}>Station-service ADM Oualidia</option>
                            <option value="Station-service Aït Ourir" {{ old('destination') === 'Station-service Aït Ourir' ? 'selected' : '' }}>Station-service Aït Ourir</option>
                            <option value="Station-service Sfassif" {{ old('destination') === 'Station-service Sfassif' ? 'selected' : '' }}>Station-service Sfassif</option>
                            <option value="Station-service Oued Laabid" {{ old('destination') === 'Station-service Oued Laabid' ? 'selected' : '' }}>Station-service Oued Laabid</option>
                            <option value="Station-service Guisser" {{ old('destination') === 'Station-service Guisser' ? 'selected' : '' }}>Station-service Guisser</option>
                            <option value="Station-service Toualaa" {{ old('destination') === 'Station-service Toualaa' ? 'selected' : '' }}>Station-service Toualaa</option>
                            <option value="Station-service Bouskoura" {{ old('destination') === 'Station-service Bouskoura' ? 'selected' : '' }}>Station-service Bouskoura</option>
                            <option value="Station-service Aït Malek" {{ old('destination') === 'Station-service Aït Malek' ? 'selected' : '' }}>Station-service Aït Malek</option>
                            <option value="Station-service Agadir" {{ old('destination') === 'Station-service Agadir' ? 'selected' : '' }}>Station-service Agadir</option>
                            <option value="__other__" style="border-top: 1px solid #ccc; font-weight: bold;" {{ old('destination') === '__other__' ? 'selected' : '' }}>✏️ Autre destination</option>
                        </select>
                    </div>
                    <div id="customDestinationWrapper" style="{{ (old('destination') === '__other__' || old('custom_destination')) ? 'display: flex;' : 'display: none;' }} margin-top: 10px; position: relative; align-items: center;">
                        <i class="fas fa-pen" style="position: absolute; left: 12px; color: #999; font-size: 14px; pointer-events: none; z-index: 1;"></i>
                        <input 
                            type="text" 
                            id="customDestinationInput" 
                            name="custom_destination"
                            placeholder="Entrez une destination personnalisée" 
                            value="{{ old('custom_destination', '') }}"
                            autocomplete="off"
                            style="padding: 10px 12px 10px 36px; width: 100%; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;"
                        >
                    </div>
                    @if($errors->has('destination'))
                        <div style="color: #e53935; font-weight: 600; margin-top: 6px;">{{ $errors->first('destination') }}</div>
                    @endif
                    @if($errors->has('custom_destination'))
                        <div style="color: #e53935; font-weight: 600; margin-top: 6px;">{{ $errors->first('custom_destination') }}</div>
                    @endif
                </div>
            </div>

            <style>
                /* Dropdown list */
                #destinationDropdown {
                    position: absolute;
                    top: 100%;
                    left: 0;
                    right: 0;
                    background: white;
                    border: 1px solid #e0e0e0;
                    border-top: none;
                    border-radius: 0 0 6px 6px;
                    max-height: 220px;
                    overflow-y: auto;
                    z-index: 100;
                    box-shadow: 0 6px 16px rgba(0,0,0,0.12);
                }

                /* Each suggestion row */
                .destination-suggestion {
                    padding: 10px 14px;
                    cursor: pointer;
                    border-bottom: 1px solid #f0f0f0;
                    font-size: 13px;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    transition: background 0.15s ease, color 0.15s ease;
                }

                .destination-suggestion:last-child {
                    border-bottom: none;
                }

                .destination-suggestion:hover,
                .destination-suggestion.highlighted {
                    background: linear-gradient(135deg, #e8f5e9 0%, #fff8e1 100%);
                    color: #1b5e20;
                    font-weight: 500;
                }

                .destination-suggestion i {
                    color: #4CAF50;
                    font-size: 12px;
                    flex-shrink: 0;
                }

                /* Bold-highlight the matched portion */
                .destination-suggestion .dest-match {
                    font-weight: 700;
                    color: #2E7D32;
                }
            </style>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-road" style="margin-right: 6px; color: #4CAF50;"></i>KILOMÉTRAGE PRÉVU (KM)</label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <i class="fas fa-tachometer-alt" style="position: absolute; left: 12px; color: #999; font-size: 14px; pointer-events: none;"></i>
                        <input type="number" name="kilometers" placeholder="ex: 240" style="padding: 10px 12px 10px 36px; width: 100%;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Motif -->
        <div class="form-section" style="margin-top: 30px;">
            <div class="form-section-header">
                <i class="fas fa-comment-dots"></i>
                <span class="form-section-title">Motif du déplacement</span>
            </div>

            <div class="form-row full">
                <div class="form-group">
                    <label><i class="fas fa-pen-fancy" style="margin-right: 6px; color: #4CAF50;"></i>MOTIF <span style="color: #FFA726;">*</span></label>
                    <div style="position: relative; display: flex;">
                        <i class="fas fa-comment" style="position: absolute; left: 12px; top: 12px; color: #999; font-size: 14px; pointer-events: none;"></i>
                        <textarea name="reason" required placeholder="Décrivez l'objet professionnel..." style="padding: 10px 12px 10px 36px; width: 100%; padding-left: 36px;"></textarea>
                    </div>
                    <div class="char-counter"><i class="fas fa-keyboard" style="margin-right: 4px;"></i><span id="charCount">0</span> / 500</div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
            <a href="{{ route('mes-demandes.index') }}" class="btn btn-secondary">
                <i class="fas fa-times-circle"></i> Annuler
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Soumettre la demande
            </button>
        </div>
    </form>
</div>

<script>
    const textarea = document.querySelector('textarea[name="reason"]');
    const charCount = document.querySelector('#charCount');
    const startDateField = document.getElementById('startDateField');
    const carSelect = document.getElementById('carIdField');
    const carHint = document.getElementById('carHint');
    const vehicleAvailabilityAlert = document.getElementById('vehicleAvailabilityAlert');
    const preselectedCarId = @json($carId ?? null);

    // Weekend-only day restrictions (no fixed times enforced)
    const WEEKEND_ONLY = {
        DEPARTURE_DAY: 5, // 0=Sun, 1=Mon, 5=Fri, 6=Sat (Friday)
        RETURN_DAY: 1, // Monday
    };

    // Function to get ISO day of week (0=Sun, 1=Mon, 5=Fri, 6=Sat)
    function getISODayOfWeek(dateString) {
        const date = new Date(dateString + 'T00:00:00');
        return date.getDay(); // 0=Sunday, 1=Monday, ..., 6=Saturday
    }

    // Function to get French day name
    function getFrenchDayName(dateString) {
        const date = new Date(dateString + 'T00:00:00');
        const dayNames = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
        return dayNames[date.getDay()];
    }

    // Function to validate weekend-only reservation (day restrictions only, no time restrictions)
    function validateWeekendOnlyReservation() {
        if (!startDateField.value || !carSelect.value) return null;

        const selectedCarOption = carSelect.options[carSelect.selectedIndex];
        const availability = selectedCarOption.dataset.availability || 'both';
        
        // Only validate if vehicle is weekend-only
        if (availability !== 'weekend') return null;

        const startDate = startDateField.value;
        const endDate = document.querySelector('input[name="end_date"]')?.value || '';

        const startDayOfWeek = getISODayOfWeek(startDate);
        const endDayOfWeek = getISODayOfWeek(endDate);

        // ===== RULE 1: START DATE MUST BE FRIDAY =====
        if (startDayOfWeek !== WEEKEND_ONLY.DEPARTURE_DAY) {
            const dayName = getFrenchDayName(startDate);
            return {
                field: 'start_date',
                message: `Ce véhicule ne peut être réservé que le vendredi. Vous avez sélectionné un ${dayName}.`,
            };
        }

        // ===== RULE 2: END DATE MUST BE MONDAY =====
        if (endDate) {
            if (endDayOfWeek !== WEEKEND_ONLY.RETURN_DAY) {
                const dayName = getFrenchDayName(endDate);
                return {
                    field: 'end_date',
                    message: `La restitution doit se faire le lundi. Vous avez sélectionné un ${dayName}.`,
                };
            }
        }

        return null; // All validations passed - times are not restricted
    }

    // Function to check if a date is a weekend (Saturday=6, Sunday=0)
    function isWeekend(dateString) {
        const date = new Date(dateString + 'T00:00:00');
        const dayOfWeek = date.getDay();
        return dayOfWeek === 0 || dayOfWeek === 6;
    }

    // Function to check vehicle availability for selected date
    function validateVehicleAvailabilityForDate() {
        if (!startDateField.value || !carSelect.value) return;

        const selectedCarOption = carSelect.options[carSelect.selectedIndex];
        const availability = selectedCarOption.dataset.availability || 'both';

        // Remove any previously added error alerts
        const existingAlert = carHint.parentNode.querySelector('div[style*="FFEBEE"]');
        if (existingAlert) existingAlert.remove();

        // For weekend-only vehicles, validate the complete reservation
        if (availability === 'weekend') {
            const validationError = validateWeekendOnlyReservation();
            if (validationError) {
                const alert = document.createElement('div');
                alert.style.cssText = 'background-color: #FFEBEE; border: 1px solid #FFCDD2; border-left: 4px solid #E53935; border-radius: 6px; padding: 12px 14px; margin-top: 8px; font-size: 12px; color: #C62828; display: flex; align-items: flex-start; gap: 10px;';
                alert.innerHTML = `
                    <i class="fas fa-exclamation-circle" style="flex-shrink: 0; margin-top: 2px; font-size: 14px;"></i>
                    <div>
                        <strong>Erreur de réservation:</strong> ${validationError.message}
                    </div>
                `;
                carHint.style.display = 'none';
                carHint.parentNode.insertBefore(alert, carHint.nextSibling);
                return false;
            } else {
                carHint.style.display = 'block';
                return true;
            }
        }
    }

    // Update vehicle availability alert when car is selected
    carSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const availability = selectedOption.dataset.availability || 'both';
        
        if (availability === 'weekend') {
            vehicleAvailabilityAlert.style.display = 'flex';
            vehicleAvailabilityAlert.innerHTML = `
                <i class="fas fa-calendar" style="flex-shrink: 0; margin-top: 2px; font-size: 14px;"></i>
                <div>
                    <strong>Disponibilité:</strong> Ce véhicule ne peut être réservé que du <strong>vendredi au lundi</strong>. Vous pouvez choisir n'importe quelle heure.
                </div>
            `;
            // Re-validate if dates are selected
            validateVehicleAvailabilityForDate();
        } else {
            vehicleAvailabilityAlert.style.display = 'none';
            // Remove any error alerts
            const existingAlert = carHint.parentNode.querySelector('div[style*="FFEBEE"]');
            if (existingAlert) existingAlert.remove();
            carHint.style.display = 'block';
        }
    });

    // Validate when start date is changed
    startDateField.addEventListener('change', function() {
        validateVehicleAvailabilityForDate();
        refreshCarsForDate(startDateField.value);
    });

    // Validate when end date is changed
    document.querySelector('input[name="end_date"]')?.addEventListener('change', function() {
        validateVehicleAvailabilityForDate();
    });

    function fillPrintSummary() {
        const carId = document.getElementById('carIdField')?.value || '';
        const carText = (document.getElementById('carIdField')?.selectedOptions?.[0]?.textContent || '').trim();
        const startDate = document.querySelector('input[name="start_date"]')?.value || '-';
        const startTime = document.querySelector('input[name="start_time"]')?.value || '-';
        const endDate = document.querySelector('input[name="end_date"]')?.value || '-';
        const endTime = document.querySelector('input[name="end_time"]')?.value || '-';
        
        // Get destination from select or custom input
        const select = document.getElementById('destinationSelect');
        const customInput = document.getElementById('customDestinationInput');
        let destination = '-';
        if (select && select.value && select.value !== '__other__') {
            destination = select.value;
        } else if (select && select.value === '__other__' && customInput) {
            destination = customInput.value || '-';
        }

        const vehicleValue = carId ? (carText || `Vehicule #${carId}`) : 'Non selectionne';
        document.getElementById('printVehicle').textContent = vehicleValue;
        document.getElementById('printStartDate').textContent = startDate;
        document.getElementById('printStartTime').textContent = startTime;
        document.getElementById('printEndDate').textContent = endDate;
        document.getElementById('printEndTime').textContent = endTime;
        document.getElementById('printDestination').textContent = destination;
    }

    async function refreshCarsForDate(dateValue) {
        if (!carSelect) return;

        if (!dateValue) {
            // Reset to initial vehicles when date is cleared
            const availableVehicles = @json($availableVehicles);
            if (availableVehicles && availableVehicles.length > 0) {
                carSelect.innerHTML = [
                    '<option value="" disabled>-- Sélectionner un véhicule --</option>',
                    ...availableVehicles.map(v => `<option value="${v.id}" data-availability="${v.availability_type || 'both'}" ${String(v.id) === String(preselectedCarId) ? 'selected' : ''}>${String(v.id) === String(preselectedCarId) ? '✓ ' : ''}${v.name} (${v.matricule})</option>`)
                ].join('');
                if (preselectedCarId) {
                    carSelect.value = String(preselectedCarId);
                }
            } else {
                carSelect.innerHTML = '<option value="" selected disabled>Aucun véhicule disponible</option>';
            }
            if (carHint) {
                if (preselectedCarId) {
                    carHint.innerHTML = '<i class="fas fa-check-circle" style="margin-right: 5px; color: #4CAF50;"></i><strong>Véhicule présélectionné!</strong> Sélectionnez une date pour vérifier la disponibilité pour cette date';
                } else {
                    carHint.innerHTML = '<i class="fas fa-info-circle" style="margin-right: 5px;"></i>Sélectionnez une date pour vérifier la disponibilité pour cette date spécifique (semaine/weekend)';
                }
            }
            return;
        }

        carSelect.innerHTML = '<option value="" selected disabled>Chargement...</option>';
        if (carHint) carHint.textContent = 'Chargement des véhicules disponibles...';

        try {
            // S'assurer qu'on a toujours une date valide
            const finalDate = dateValue || new Date().toISOString().slice(0, 10);
            
            const res = await fetch(`{{ route('cars.available') }}?date=${encodeURIComponent(finalDate)}`, {
                method: 'GET',
                headers: { 
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            });

            // Essayer de parser la réponse JSON peu importe le status
            let data = {};
            try {
                data = await res.json();
            } catch (jsonErr) {
                console.error('Réponse non-JSON du serveur', { status: res.status, statusText: res.statusText });
                throw new Error(`Erreur serveur: ${res.status} ${res.statusText}`);
            }

            // Si le serveur retourna un JSON avec success: false
            if (data.success === false) {
                console.warn('API returned success=false', data);
                carSelect.innerHTML = '<option value="" selected disabled>Aucun véhicule disponible</option>';
                if (carHint) carHint.innerHTML = `<i class="fas fa-info-circle" style="margin-right: 5px; color: #ffa726;"></i>${data.message || 'Aucun véhicule disponible pour cette date.'}`;
                return;
            }

            // Succès - traiter les véhicules
            const cars = data.cars || [];
            const dayType = data.day_type || '';
            const hasZone = data.has_zone !== false;
            const warning = data.warning || '';

            // ========== ZONE WARNING ==========
            if (!hasZone && warning) {
                if (carHint) carHint.innerHTML = `<span style="color: #e65100; font-weight: 600;"><i class="fas fa-triangle-exclamation"></i> ${warning}</span>`;
            }
            // ==================================

            const labelDay = dayType === 'weekend' ? 'Weekend (Sam–Dim)' : 'Semaine (Lun–Ven)';
            if (carHint) carHint.innerHTML = `<i class="fas fa-calendar-check" style="margin-right: 5px; color: #4CAF50;"></i>Véhicules disponibles pour: <strong>${labelDay}</strong>`;

            if (!cars.length) {
                carSelect.innerHTML = '<option value="" selected disabled>Aucun véhicule disponible pour cette date</option>';
                return;
            }

            const previous = carSelect.value || '';
            const desired = (preselectedCarId && String(preselectedCarId)) || previous;

            carSelect.innerHTML = [
                '<option value="" disabled>-- Choisir un véhicule --</option>',
                ...cars.map(c => `<option value="${c.id}" data-availability="${c.availability_type || 'both'}" ${String(c.id) === String(desired) ? 'selected' : ''}>${String(c.id) === String(desired) ? '✓ ' : ''}${c.name} (${c.matricule})</option>`)
            ].join('');

            if (desired && cars.some(c => String(c.id) === String(desired))) {
                carSelect.value = String(desired);
            } else {
                carSelect.value = '';
            }
        } catch (e) {
            console.error('Erreur lors du chargement des véhicules:', {
                message: e.message,
                error: e,
                dateValue: dateValue,
                route: '{{ route('cars.available') }}'
            });
            
            carSelect.innerHTML = '<option value="" selected disabled>⚠️ Erreur de chargement</option>';
            if (carHint) {
                carHint.innerHTML = `<span style="color: #e53935; font-weight: 600;">
                    <i class="fas fa-exclamation-triangle" style="margin-right: 5px;"></i>
                    Impossible de charger les véhicules.<br/>
                    <small style="font-weight: 400;">Vérifiez votre connexion et réessayez. Si le problème persiste, contactez l'administrateur.</small>
                </span>`;
            }
        }
    }
    
    if (textarea) {
        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
            if (this.value.length > 500) {
                this.value = this.value.substring(0, 500);
                charCount.textContent = '500';
            }
        });
    }

    window.addEventListener('beforeprint', fillPrintSummary);

    if (startDateField) {
        // ===== RESERVATION DATE RULE =====
        // Keep same-day reservations possible, block only past dates.
        function setMinimumReservationDate() {
            const now = new Date();
            const minimumDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            
            // Format as YYYY-MM-DD for the date input
            const year = minimumDate.getFullYear();
            const month = String(minimumDate.getMonth() + 1).padStart(2, '0');
            const day = String(minimumDate.getDate()).padStart(2, '0');
            const minDateString = `${year}-${month}-${day}`;
            
            startDateField.min = minDateString;
        }

        setMinimumReservationDate();

        // Note: startDateField.addEventListener('change', ...) is now above in the new validation section
        // This prevents duplicate listeners

    // Pre-select vehicle if car_id parameter is provided and highlight it
    if (preselectedCarId && carSelect) {
        carSelect.value = String(preselectedCarId);
        
        // Trigger change event to show availability alert if needed
        const event = new Event('change', { bubbles: true });
        carSelect.dispatchEvent(event);
        
        // Update the hint to show user a vehicle is pre-selected
        if (carHint) {
            carHint.innerHTML = '<i class="fas fa-check-circle" style="margin-right: 5px; color: #4CAF50;"></i><strong>Véhicule présélectionné!</strong> Sélectionnez une date pour vérifier la disponibilité pour cette date';
        }
    }

    // Only refresh on date change if a date is selected
    if (startDateField && startDateField.value) {
        refreshCarsForDate(startDateField.value);
    }

    // Form submission validation: ensure all constraints are met
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!startDateField.value || !carSelect.value) return; // Let server validation handle

        const selectedCarOption = carSelect.options[carSelect.selectedIndex];
        const availability = selectedCarOption.dataset.availability || 'both';

        // For weekend-only vehicles, validate complete reservation
        if (availability === 'weekend') {
            const validationError = validateWeekendOnlyReservation();
            if (validationError) {
                e.preventDefault();
                const dayNames = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
                let fullMessage = '❌ Cette réservation n\'est pas autorisée:\n\n' + validationError.message;
                
                // Add helpful info
                fullMessage += '\n\n📋 Rappel des règles:\n';
                fullMessage += '• Récupération: Vendredi à partir de 17:00\n';
                fullMessage += '• Restitution: Lundi avant 12:00\n';
                fullMessage += '• Exemple: Vendredi 18:00 → Lundi 10:00 ✅';
                
                alert(fullMessage);
                return false;
            }
        }
    });

    // ===== DESTINATION SELECT HANDLER =====
    (function () {
        const select = document.getElementById('destinationSelect');
        const customWrapper = document.getElementById('customDestinationWrapper');
        const customInput = document.getElementById('customDestinationInput');

        if (!select || !customWrapper || !customInput) return;

        // Show/hide custom destination input when "Other" option is selected
        select.addEventListener('change', function() {
            if (this.value === '__other__') {
                customWrapper.style.display = 'flex';
                customInput.required = true;
                customInput.focus();
            } else {
                customWrapper.style.display = 'none';
                customInput.required = false;
                customInput.value = '';
            }
        });

        // When form is submitted, ensure the correct destination value is used
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                // If user chose 'Other', ensure we submit the custom value as 'destination'
                if (select.value === '__other__') {
                    if (!customInput.value.trim()) {
                        e.preventDefault();
                        alert('Veuillez entrer une destination personnalisée.');
                        customInput.focus();
                        return false;
                    }

                    // Ensure custom_destination has name so it's submitted
                    if (!customInput.getAttribute('name')) customInput.setAttribute('name', 'custom_destination');
                    
                    // Remove name from select so browser doesn't submit it, and add hidden input with final value
                    select.removeAttribute('name');
                    let hidden = form.querySelector('input[name="destination"][type="hidden"]');
                    if (!hidden) {
                        hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'destination';
                        form.appendChild(hidden);
                    }
                    hidden.value = customInput.value.trim();
                } else {
                    // Ensure select has name so its value is submitted and remove any temporary hidden input
                    if (!select.getAttribute('name')) select.setAttribute('name', 'destination');
                    const hidden = form.querySelector('input[name="destination"][type="hidden"]');
                    if (hidden) hidden.remove();
                    
                    // Remove name from custom_destination so it's not submitted when not needed
                    customInput.removeAttribute('name');
                    customInput.value = '';
                }
            });
        }

        // Pre-fill destination if error on form submission
        const initialDestination = select.getAttribute('data-old-value');
        if (initialDestination && initialDestination !== '__other__') {
            select.value = initialDestination;
        } else if (initialDestination === '__other__' || (customInput.value && !select.value)) {
            select.value = '__other__';
            customWrapper.style.display = 'flex';
            customInput.required = true;
        }
    })();
</script>

@endsection
