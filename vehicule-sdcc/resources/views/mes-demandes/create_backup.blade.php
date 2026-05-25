@extends('layouts.app')
@section('title', 'SDCC - Nouvelle Demande')
@section('content')

<style>
    /* ==================== PAGE STYLES ==================== */

/* Layout global */
.row {
    display: flex;
    flex-wrap: nowrap;
}

/* Sidebar */
.sidebar {
    background: #2E7D32; /* vert foncé */
    color: white;
    min-height: 100vh;
    padding: 20px;
    border-radius: 0 12px 12px 0;
    box-shadow: 2px 0 8px rgba(0,0,0,0.1);
}

.sidebar a {
    display: block;
    color: white;
    text-decoration: none;
    margin-bottom: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.sidebar a:hover {
    color: #FFA726;
}

/* Contenu principal */
.content-wrapper {
    width: 100%; /* limité à col-md-9 */
    padding: 20px;
}

/* Breadcrumb */
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    font-size: 13px;
}

.breadcrumb a {
    color: #4CAF50;
    text-decoration: none;
    font-weight: 500;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb span {
    color: #ccc;
}

/* Back Button */
.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #4CAF50;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.back-btn:hover {
    color: #2E7D32;
}

/* Page Title */
.page-title {
    font-size: 32px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 30px 0;
}

/* Request Header */
.request-header {
    background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 25%, #FFA726 75%, #FFA500 100%);
    border-radius: 12px;
    padding: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(76, 175, 80, 0.2);
}

.request-header-left {
    display: flex;
    gap: 15px;
    align-items: flex-start;
    flex: 1;
}

.request-header-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 18px;
}

.request-header-info h3 {
    font-size: 16px;
    margin: 0 0 5px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.request-header-info p {
    font-size: 13px;
    opacity: 0.9;
    margin: 0;
}

.request-header-right {
    text-align: right;
    font-size: 13px;
}

.request-header-date {
    font-size: 11px;
    opacity: 0.8;
    margin-top: 5px;
}

.car-icon {
    font-size: 40px;
    opacity: 0.3;
    margin-left: 20px;
}

/* Form Container */
.form-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

/* Form Section */
.form-section {
    padding: 30px;
    border-bottom: 1px solid #f0f0f0;
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section-title {
    font-size: 16px;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-section-title i {
    color: #4CAF50;
    font-size: 18px;
}

/* Form Rows */
.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 20px;
}

.form-row.full {
    grid-template-columns: 1fr;
}

.form-row:last-of-type {
    margin-bottom: 0;
}

/* Form Group */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 12px;
    font-weight: 600;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-group .required {
    color: #c62828;
}

.form-group input[type="text"],
.form-group input[type="date"],
.form-group input[type="time"],
.form-group input[type="number"],
.form-group textarea,
.form-group select {
    padding: 12px 14px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    font-size: 14px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto;
    transition: all 0.3s ease;
}

.form-group input[type="text"]:focus,
.form-group input[type="date"]:focus,
.form-group input[type="time"]:focus,
.form-group input[type="number"]:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto;
}

.form-group input:disabled {
    background: #f5f5f5;
    color: #999;
    cursor: not-allowed;
}

.hint {
    font-size: 12px;
    color: #999;
    margin-top: 4px;
}

.char-count {
    font-size: 12px;
    color: #999;
    margin-top: 8px;
    text-align: right;
}

/* Form Actions */
.form-actions {
    padding: 25px 30px;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    background: #fafafa;
    border-top: 1px solid #f0f0f0;
    border-radius: 0 0 12px 12px;
}

.btn {
    padding: 12px 24px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-cancel {
    background: white;
    color: #666;
    border: 1px solid #e0e0e0;
}

.btn-cancel:hover {
    background: #f5f5f5;
    border-color: #d0d0d0;
}

.btn-draft {
    background: white;
    color: #1a1a1a;
    border: 1px solid #e0e0e0;
}

.btn-draft:hover {
    background: #f5f5f5;
    border-color: #d0d0d0;
}

.btn-submit {
    background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
}

.btn-submit:hover {
    background: linear-gradient(135deg, #2E7D32 0%, #E65100 100%);
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .request-header {
        flex-direction: column;
        text-align: center;
    }
    
    .request-header-left {
        flex-direction: column;
        align-items: center;
        width: 100%;
    }
    
    .request-header-right {
        text-align: center;
        margin-top: 15px;
    }
    
    .car-icon {
        margin-left: 0;
        margin-top: 15px;
    }
    
    .form-section {
        padding: 20px;
    }
}
</style>

<div class="row">
    <!-- Sidebar -->
     <div class="container-fluid">
        <h2>Nouvelle demande</h2>
        <form method="POST" action="{{ route('requests.store') }}">
            @csrf
            <!-- champs de formulaire ici -->
            <button type="submit" class="btn btn-primary">Soumettre</button>
        </form>
    </div>

            <!-- Back Button -->
            <a href="{{ route('mes-demandes.index') }}" class="back-btn">
                <i class="fas fa-chevron-left"></i> Retour à mes demandes
            </a>

            <!-- Page Title -->
            <h1 class="page-title">Nouvelle demande</h1>

            <!-- Request Header -->
            <div class="request-header">
                <div class="request-header-left">
                    <div class="request-header-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                    <div class="request-header-info">
                        <h3><i class="fas fa-file-contract"></i> Demande de Véhicule de Service</h3>
                        <p>SDCC — Moyens Généraux — Service pour organisation</p>
                    </div>
                </div>
                <div class="request-header-right">
                    <div><strong>{{ strtoupper(Auth::user()->name) }}</strong></div>
                    <div class="hint">{{ strtolower(Auth::user()->email) }}</div>
                    <div class="request-header-date">{{ now()->locale('fr')->translatedFormat('l d F Y') }}</div>
                </div>
                <div class="car-icon">
                    <i class="fas fa-car"></i>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('mes-demandes.store') }}" class="form-container">
                @csrf
                <input type="hidden" name="car_id" value="{{ $carId ?? '' }}">

                <!-- Sections du formulaire -->
                {{-- Période demandée --}}
                {{-- Informations du déplacement --}}
                {{-- Motif du déplacement --}}
                {{-- Actions --}}
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Character count for textarea
    const textarea = document.querySelector('textarea[name="reason"]');
    const charCount = document.querySelector('#charCount');
    
    if (textarea) {
        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
            if (this.value.length > 500) {
                this.value = this.value.substring(0, 500);
                charCount.textContent = '500';
            }
        });
    }
</script>

@endsection
