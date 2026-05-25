@extends('layouts.app')
@section('title', 'SDCC - Modifier la Réservation #{{ $reservation->id }}')
@section('content')

<style>
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #4CAF50;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    .back-link:hover { color: #2E7D32; transform: translateX(-4px); }

    .page-title { font-size: 26px; font-weight: 700; color: #111; margin: 0 0 6px 0; }
    .page-subtitle { font-size: 13px; color: #666; margin: 0 0 28px 0; }

    .form-card {
        background: white;
        border-radius: 10px;
        padding: 32px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        max-width: 920px;
        margin: 0 auto;
    }

    .form-section { margin-bottom: 32px; }

    .form-section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 14px;
        margin-bottom: 22px;
        border-bottom: 2px solid transparent;
        border-image: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%) 1;
    }

    .form-section-header i {
        font-size: 17px;
        background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .form-section-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 0;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 22px;
        margin-bottom: 18px;
    }

    .form-row.full { grid-template-columns: 1fr; }

    .form-group { display: flex; flex-direction: column; }

    .form-group label {
        font-size: 12px;
        font-weight: 600;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 7px;
    }

    .form-group label span { color: #FFA726; }

    .form-group input,
    .form-group textarea,
    .form-group select {
        padding: 10px 12px;
        border: 1.5px solid #e0e0e0;
        border-radius: 6px;
        font-size: 13px;
        font-family: inherit;
        background: white;
        transition: all 0.25s ease;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: #4CAF50;
        box-shadow: 0 0 0 3px rgba(76,175,80,0.12);
    }

    .form-group textarea { resize: vertical; min-height: 100px; }

    .error-message { color: #e53935; font-size: 12px; margin-top: 5px; display: block; }

    .error-alert {
        background: #ffebee;
        border-left: 4px solid #e53935;
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 22px;
        font-size: 13px;
        color: #c62828;
    }

    .status-info-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .status-info-badge.pending  { background:#fff3e0; color:#f57c00; }
    .status-info-badge.approved { background:#e8f5e9; color:#2e7d32; }
    .status-info-badge.cancelled{ background:#ffebee; color:#c62828; }
    .status-info-badge.rejected { background:#fce4ec; color:#ad1457; }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-top: 36px;
        padding-top: 24px;
        border-top: 1px solid #f0f0f0;
    }

    .btn {
        padding: 11px 28px;
        border-radius: 6px;
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

    .btn-primary {
        background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(76,175,80,0.25);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2E7D32 0%, #E65100 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76,175,80,0.35);
    }

    .btn-secondary {
        background: white;
        color: #555;
        border: 1.5px solid #ddd;
    }

    .btn-secondary:hover { background: #f5f5f5; border-color: #bbb; }

    /* Destination autocomplete */
    .dest-wrapper { position: relative; }
    .dest-wrapper input { width: 100%; box-sizing: border-box; }

    #editDestinationDropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1.5px solid #e0e0e0;
        border-top: none;
        border-radius: 0 0 6px 6px;
        max-height: 220px;
        overflow-y: auto;
        z-index: 999;
        display: none;
        box-shadow: 0 6px 16px rgba(0,0,0,0.12);
    }

    .dest-suggestion {
        padding: 10px 14px;
        cursor: pointer;
        border-bottom: 1px solid #f5f5f5;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: background 0.15s ease;
    }

    .dest-suggestion:last-child { border-bottom: none; }
    .dest-suggestion i { color: #4CAF50; font-size: 12px; }

    .dest-suggestion:hover,
    .dest-suggestion.highlighted {
        background: linear-gradient(135deg, #e8f5e9 0%, #fff8e1 100%);
        color: #1b5e20;
        font-weight: 500;
    }

    .dest-match { font-weight: 700; color: #2E7D32; }

    @media (max-width: 768px) {
        .form-row { grid-template-columns: 1fr; gap: 14px; }
        .form-card { padding: 20px; }
        .form-actions { flex-direction: column; }
        .btn { justify-content: center; }
    }
</style>

<div style="padding: 30px;">
    <a href="{{ route('admin.reservations.index') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Retour aux réservations
    </a>

    <h1 class="page-title">
        <i class="fas fa-edit" style="color: #4CAF50; margin-right: 10px;"></i>
        Modifier la Réservation #{{ $reservation->id }}
    </h1>
    <p class="page-subtitle">
        Modifiez les informations de cette réservation puis enregistrez.
    </p>

    {{-- Current status badge --}}
    <div>
        <span class="status-info-badge {{ $reservation->status }}">
            @if($reservation->status === 'pending')
                <i class="fas fa-hourglass-half"></i> En attente
            @elseif($reservation->status === 'approved')
                <i class="fas fa-check-circle"></i> Approuvée
            @elseif($reservation->status === 'cancelled')
                <i class="fas fa-times-circle"></i> Annulée
            @elseif($reservation->status === 'rejected')
                <i class="fas fa-ban"></i> Rejetée
            @endif
        </span>
    </div>

    @if ($errors->any())
        <div class="error-alert">
            <strong><i class="fas fa-exclamation-triangle"></i> Erreur:</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-card">
        <form action="{{ route('admin.reservations.update', $reservation->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Section 1: Employee & Vehicle --}}
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-user"></i>
                    <h2 class="form-section-title">Employé et Véhicule</h2>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="user_id">Employé <span>*</span></label>
                        <select id="user_id" name="user_id" required>
                            <option value="">-- Sélectionner un employé --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ (old('user_id', $reservation->user_id) == $user->id) ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="car_id">Véhicule <span>*</span></label>
                        <select id="car_id" name="car_id" required>
                            <option value="">-- Sélectionner un véhicule --</option>
                            @foreach($cars as $car)
                                <option value="{{ $car->id }}"
                                    {{ (old('car_id', $reservation->car_id) == $car->id) ? 'selected' : '' }}>
                                    {{ $car->name }} ({{ $car->matricule }})
                                    {{ $car->status !== 'disponible' ? ' — ' . ucfirst($car->status) : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('car_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 2: Dates & Times --}}
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-calendar"></i>
                    <h2 class="form-section-title">Dates et Heures</h2>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="start_date">Date de Départ <span>*</span></label>
                        <input type="date" id="start_date" name="start_date" required
                               value="{{ old('start_date', $reservation->start_date->format('Y-m-d')) }}">
                        @error('start_date') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="start_time">Heure de Départ <span>*</span></label>
                        <input type="time" id="start_time" name="start_time" required
                               value="{{ old('start_time', $reservation->start_time) }}">
                        @error('start_time') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="end_date">Date de Retour <span>*</span></label>
                        <input type="date" id="end_date" name="end_date" required
                               value="{{ old('end_date', $reservation->end_date->format('Y-m-d')) }}">
                        @error('end_date') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="end_time">Heure de Retour</label>
                        <input type="time" id="end_time" name="end_time"
                               value="{{ old('end_time', $reservation->end_time) }}">
                        @error('end_time') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="return_time">Heure de Retour Finale</label>
                        <input type="time" id="return_time" name="return_time"
                               value="{{ old('return_time', $reservation->return_time) }}">
                    </div>

                    <div class="form-group">
                        <label for="kilometers">Kilométrage Prévu (km)</label>
                        <input type="number" id="kilometers" name="kilometers" min="0"
                               value="{{ old('kilometers', $reservation->kilometers) }}"
                               placeholder="0">
                    </div>
                </div>
            </div>

            {{-- Section 3: Destination & Reason --}}
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-map-marker-alt"></i>
                    <h2 class="form-section-title">Destination et Motif</h2>
                </div>

                <div class="form-row full">
                    <div class="form-group">
                        <label for="editDestinationInput">Destination <span>*</span></label>
                        <div class="dest-wrapper">
                            <input
                                type="text"
                                id="editDestinationInput"
                                name="destination"
                                required
                                autocomplete="off"
                                placeholder="Ex: Casablanca, Station-service..."
                                value="{{ old('destination', $reservation->destination) }}"
                            >
                            <div id="editDestinationDropdown"></div>
                        </div>
                        @error('destination') <span class="error-message">{{ $message }}</span> @enderror
                        <p style="font-size:11px; color:#999; margin-top:5px;">
                            <i class="fas fa-info-circle"></i>
                            Tapez pour voir les suggestions ou saisissez une destination personnalisée
                        </p>
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-group">
                        <label for="reason">Motif de la Réservation <span>*</span></label>
                        <textarea id="reason" name="reason" required
                                  placeholder="Décrivez le motif de cette réservation...">{{ old('reason', $reservation->reason) }}</textarea>
                        @error('reason') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 4: Status --}}
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-check-circle"></i>
                    <h2 class="form-section-title">Statut</h2>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="status">Statut de la Réservation</label>
                        <select id="status" name="status">
                            <option value="pending"   {{ old('status', $reservation->status) === 'pending'   ? 'selected' : '' }}>En Attente</option>
                            <option value="approved"  {{ old('status', $reservation->status) === 'approved'  ? 'selected' : '' }}>Approuvée</option>
                            <option value="cancelled" {{ old('status', $reservation->status) === 'cancelled' ? 'selected' : '' }}>Annulée</option>
                            <option value="rejected"  {{ old('status', $reservation->status) === 'rejected'  ? 'selected' : '' }}>Rejetée</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="form-actions">
                <a href="{{ route('admin.reservations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const DESTINATIONS = [
        'Casablanca','Rabat','Marrakech','Jorf Lasfar',
        'Station-service Kénitra','Station-service Sidi Kacem',
        'Station-service ADM Oualidia','Station-service Aït Ourir',
        'Station-service Sfassif','Station-service Oued Laabid',
        'Station-service Guisser','Station-service Toualaa',
        'Station-service Bouskoura','Station-service Aït Malek',
        'Station-service Agadir',
    ];

    const input    = document.getElementById('editDestinationInput');
    const dropdown = document.getElementById('editDestinationDropdown');
    let highlighted = -1;

    if (!input || !dropdown) return;

    function highlightMatch(text, query) {
        const idx = text.toLowerCase().indexOf(query.toLowerCase());
        if (idx === -1) return text;
        return text.slice(0, idx)
             + '<span class="dest-match">' + text.slice(idx, idx + query.length) + '</span>'
             + text.slice(idx + query.length);
    }

    function getSuggestions(query) {
        const q = query.toLowerCase().trim();
        if (!q) return [];
        return DESTINATIONS
            .filter(d => d.toLowerCase().includes(q))
            .sort((a, b) => (a.toLowerCase().startsWith(q) ? 0 : 1) - (b.toLowerCase().startsWith(q) ? 0 : 1));
    }

    function renderDropdown(query) {
        const suggestions = getSuggestions(query);
        highlighted = -1;
        if (!suggestions.length) { dropdown.style.display = 'none'; dropdown.innerHTML = ''; return; }

        dropdown.innerHTML = suggestions.map((s, i) =>
            `<div class="dest-suggestion" data-value="${s}" data-index="${i}">
                <i class="fas fa-map-marker-alt"></i>
                <span>${highlightMatch(s, query)}</span>
             </div>`
        ).join('');

        dropdown.style.display = 'block';

        dropdown.querySelectorAll('.dest-suggestion').forEach(item => {
            item.addEventListener('mousedown', e => {
                e.preventDefault();
                input.value = item.dataset.value;
                dropdown.style.display = 'none';
                input.focus();
            });
            item.addEventListener('mouseenter', () => {
                dropdown.querySelectorAll('.dest-suggestion').forEach(s => s.classList.remove('highlighted'));
                item.classList.add('highlighted');
                highlighted = parseInt(item.dataset.index);
            });
        });
    }

    function updateHighlight(items) {
        items.forEach((item, i) => {
            item.classList.toggle('highlighted', i === highlighted);
            if (i === highlighted) item.scrollIntoView({ block: 'nearest' });
        });
    }

    input.addEventListener('input', () => renderDropdown(input.value));
    input.addEventListener('focus', () => { if (input.value.trim()) renderDropdown(input.value); });

    input.addEventListener('keydown', e => {
        const items = Array.from(dropdown.querySelectorAll('.dest-suggestion'));
        if (!items.length || dropdown.style.display === 'none') return;
        if (e.key === 'ArrowDown') { e.preventDefault(); highlighted = Math.min(highlighted + 1, items.length - 1); updateHighlight(items); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); highlighted = Math.max(highlighted - 1, -1); updateHighlight(items); }
        else if (e.key === 'Enter') { e.preventDefault(); if (highlighted >= 0 && items[highlighted]) { input.value = items[highlighted].dataset.value; dropdown.style.display = 'none'; } }
        else if (e.key === 'Escape') { dropdown.style.display = 'none'; }
    });

    document.addEventListener('click', e => {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) dropdown.style.display = 'none';
    });
})();
</script>

@endsection
