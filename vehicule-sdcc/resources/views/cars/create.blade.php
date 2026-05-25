@extends('layouts.app')

@section('title', 'SDCC - Ajouter un Véhicule')

@section('content')
<style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
        width: 100%;
        background: white;
        padding: 24px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        box-sizing: border-box;
    }

    .form-header {
        margin-bottom: 30px;
    }

    .form-header h1 {
        font-size: 24px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 5px;
    }

    .form-header p {
        font-size: 13px;
        color: #888;
    }

    .form-group {
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-group input,
    .form-group select {
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #4CAF50;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }

    .error-message {
        color: #c62828;
        font-size: 12px;
        margin-top: 5px;
    }

    .form-errors {
        background: #FFEBEE;
        border-left: 4px solid #c62828;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .form-errors h3 {
        color: #c62828;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .form-errors ul {
        list-style: none;
        padding-left: 0;
    }

    .form-errors li {
        color: #c62828;
        font-size: 12px;
        margin-bottom: 5px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 30px;
    }

    .submit-btn {
        flex: 1;
        background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 25%, #FFA726 75%, #FFA500 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .cancel-btn {
        flex: 1;
        background: #f5f5f5;
        color: #1a1a1a;
        border: 1px solid #ddd;
        padding: 12px 24px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
    }

    .cancel-btn:hover {
        background: #e8e8e8;
    }
</style>

<div class="form-container">
    <div class="form-header">
        <h1>Ajouter un Véhicule</h1>
        <p>Remplissez le formulaire ci-dessous pour ajouter un nouveau véhicule</p>
    </div>

    @if ($errors->any())
        <div class="form-errors">
            <h3>Erreurs de validation</h3>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('cars.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Marque / Modèle</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="ex: Toyota Corolla" required>
            @error('name') <span class="error-message">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="matricule">Plaque d'immatriculation</label>
            <input type="text" id="matricule" name="matricule" value="{{ old('matricule') }}" placeholder="ex: AB123CD" required>
            @error('matricule') <span class="error-message">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="model">Spécifications</label>
            <input type="text" id="model" name="model" value="{{ old('model') }}" placeholder="ex: 1.8 Hybrid" required>
            @error('model') <span class="error-message">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="year">Année</label>
            <input type="number" id="year" name="year" value="{{ old('year', date('Y')) }}" min="1900" max="{{ date('Y') }}" required>
            @error('year') <span class="error-message">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="km">Kilométrage</label>
            <input type="number" id="km" name="km" value="{{ old('km', 0) }}" min="0" required>
            @error('km') <span class="error-message">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="status">Statut</label>
            <select id="status" name="status" required>
                <option value="">-- Choisir un statut --</option>
                @foreach($vehicleStatusOptions as $statusValue => $statusLabel)
                    <option value="{{ $statusValue }}" {{ old('status') == $statusValue ? 'selected' : '' }}>{{ $statusLabel }}</option>
                @endforeach
            </select>
            @error('status') <span class="error-message">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="availability_type">Disponibilité</label>
            <select id="availability_type" name="availability_type" required>
                <option value="">-- Choisir une disponibilité --</option>
                @foreach($vehicleAvailabilityOptions as $typeValue => $typeLabel)
                    <option value="{{ $typeValue }}" {{ old('availability_type', 'both') == $typeValue ? 'selected' : '' }}>{{ $typeLabel }}</option>
                @endforeach
            </select>
            @error('availability_type') <span class="error-message">{{ $message }}</span> @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="submit-btn">
                <i class="fas fa-check"></i> Ajouter Véhicule
            </button>
            <a href="{{ route('cars.index') }}" class="cancel-btn">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>
@endsection
