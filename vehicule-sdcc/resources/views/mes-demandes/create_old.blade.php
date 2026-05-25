@extends('layouts.app')
@section('title', 'SDCC')
@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Tableau de bord</a>
            <span> > </span>
            <a href="{{ route('mes-demandes.index') }}">Mes demandes</a>
            <span> > </span>
            <span>Nouvelle demande</span>
        </div>

        <!-- Back Button -->
        <a href="{{ route('mes-demandes.index') }}" class="back-btn">
            <i class="fas fa-chevron-left"></i> Retour à mes demandes
        </a>

        <!-- Page Title -->
        <h1 class="page-title">Nouvelle demande</h1>

        <!-- Request Header Card -->
        <div class="request-header">
            <div class="request-header-left">
                <div class="request-header-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="request-header-info">
                    <h3><i class="fas fa-file-contract"></i> Demande de Véhicule de Service</h3>
                    <p>SDCC — Moyens Généraux — Service pour organisation</p>
                </div>
            </div>
            <div class="request-header-right">
                {{ strtoupper(Auth::user()->name) }}
                <div class="char-count">{{ strtolower(Auth::user()->email) }}</div>
                <div class="request-header-date">
                    {{ now()->locale('fr')->translatedFormat('l d F Y') }}
                </div>
            </div>
            <div class="car-icon">
                <i class="fas fa-car"></i>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('mes-demandes.store') }}" class="form-container">
            @csrf

            <!-- Pre-selected Car ID if coming from cars page -->
            <input type="hidden" name="car_id" value="{{ $carId ?? '' }}">

            <!-- Période demandée -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-calendar"></i> Période demandée
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>DATE D'USAGE <span class="required">*</span></label>
                        <input type="date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label>HEURE DE DÉPART <span class="required">*</span></label>
                        <input type="time" name="start_time" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>DATE DE RESTITUTION <span class="required">*</span></label>
                        <input type="date" name="end_date" required>
                    </div>
                    <div class="form-group">
                        <label>HEURE DE RESTITUTION</label>
                        <input type="time" name="end_time">
                        <div class="hint">Optionnel</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>HEURE DE RETOUR PRÉVUE</label>
                        <input type="time" name="return_time">
                        <div class="hint">Heure retour estimée (optionnel demandeur)</div>
                    </div>
                </div>
            </div>

            <!-- Informations du déplacement -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-map-marker-alt"></i> Informations du déplacement
                </div>

                <div class="form-row full">
                    <div class="form-group">
                        <label>DESTINATION <span class="required">*</span></label>
                        <input type="text" name="destination" placeholder="Ville — lieu précis (ex: Casablanca — Siège client ABC)" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>KILOMÉTRAGE PRÉVISIONNEL</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="number" name="kilometers" placeholder="ex: 240">
                            <input type="text" value="km aller-retour" style="flex: 1;" disabled>
                        </div>
                        <div class="hint">Estimation aller-retour</div>
                    </div>
                </div>
            </div>

            <!-- Motif du déplacement -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-comment-dots"></i> Motif du déplacement
                </div>

                <div class="form-row full">
                    <div class="form-group">
                        <label>MOTIF <span class="required">*</span></label>
                        <textarea name="reason" required placeholder="Décrire l'objet professionnel de ce déplacement en détail..."></textarea>
                        <div class="char-count"><span id="charCount">0</span> / 500</div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('mes-demandes.index') }}" class="btn btn-cancel">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="button" class="btn btn-draft" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimer
                </button>
                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-check"></i> Soumettre la demande
                </button>
            </div>
        </form>
    </div>

    <script>
        // Char count for textarea
        const textarea = document.querySelector('textarea[name="reason"]');
        const charCount = document.querySelector('#charCount');
        
        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
            if (this.value.length > 500) {
                this.value = this.value.substring(0, 500);
                charCount.textContent = '500';
            }
        });
    </script>

@endsection

