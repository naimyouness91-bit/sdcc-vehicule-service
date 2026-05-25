@extends('layouts.app')
@section('title', 'SDCC - Réinitialiser Mot de Passe')
@section('content')

<style>
    :root {
        --primary: #10b981;
        --primary-light: #d1fae5;
        --primary-dark: #047857;
        --accent: #f97316;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --bg-light: #f9fafb;
        --bg-white: #ffffff;
        --border: #e5e7eb;
    }

    .content-wrapper {
        max-width: 700px;
        margin: 0 auto;
        padding: 24px;
    }

    .page-header {
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .page-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .back-link {
        color: var(--primary);
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .back-link:hover {
        gap: 10px;
    }

    .panel {
        background: var(--bg-white);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 32px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .user-info {
        background: var(--bg-light);
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 32px;
        border-left: 4px solid var(--primary);
    }

    .user-info-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid var(--border);
    }

    .user-info-item:last-child {
        border-bottom: none;
    }

    .user-info-label {
        font-weight: 600;
        color: var(--text-secondary);
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .user-info-value {
        color: var(--text-primary);
        font-weight: 500;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 24px;
    }

    .form-group label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-primary);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .form-group label .required {
        color: var(--danger);
    }

    .form-control {
        padding: 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .form-control.is-invalid {
        border-color: var(--danger);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .form-group .hint {
        font-size: 12px;
        color: var(--text-secondary);
        margin-top: 6px;
        line-height: 1.4;
    }

    .error-message {
        color: var(--danger);
        font-size: 12px;
        margin-top: 6px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid var(--border);
    }

    .btn {
        padding: 11px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--accent));
        color: white;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
    }

    .btn-primary:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-secondary {
        background: var(--bg-white);
        color: var(--text-primary);
        border: 1px solid var(--border);
    }

    .btn-secondary:hover {
        background: var(--bg-light);
        border-color: var(--primary);
    }

    .alert {
        padding: 14px 16px;
        border-radius: 8px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .alert-info {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #93c5fd;
    }

    .alert-warning {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fcd34d;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .alert-success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }

    .alert i {
        font-size: 18px;
        flex-shrink: 0;
        margin-top: 2px;
    }
</style>

<div class="content-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="fas fa-key"></i>Réinitialiser Mot de Passe
        </h1>
        <a href="{{ route('utilisateurs.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i>Retour
        </a>
    </div>

    <!-- User Information Panel -->
    <div class="panel">
        <h2 style="margin: 0 0 20px 0; font-size: 18px; font-weight: 600; color: var(--text-primary);">
            <i class="fas fa-user-circle" style="margin-right: 8px; color: var(--primary);"></i>Informations Utilisateur
        </h2>
        <div class="user-info">
            <div class="user-info-item">
                <span class="user-info-label">Nom Complet</span>
                <span class="user-info-value">{{ $user->name }}</span>
            </div>
            <div class="user-info-item">
                <span class="user-info-label">Email</span>
                <span class="user-info-value">{{ $user->email }}</span>
            </div>
            <div class="user-info-item">
                <span class="user-info-label">Service</span>
                <span class="user-info-value">{{ $user->service ?? 'Non défini' }}</span>
            </div>
            <div class="user-info-item">
                <span class="user-info-label">Rôle</span>
                <span class="user-info-value">
                    @if($user->hasRole('super_admin'))
                        Super Admin
                    @elseif($user->hasRole('admin'))
                        Admin
                    @else
                        Employé
                    @endif
                </span>
            </div>
            <div class="user-info-item">
                <span class="user-info-label">État</span>
                <span class="user-info-value">
                    @if($user->is_active ?? true)
                        <span style="color: var(--success);">✓ Actif</span>
                    @else
                        <span style="color: var(--danger);">✗ Inactif</span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Password Reset Form -->
    <div class="panel">
        <h2 style="margin: 0 0 20px 0; font-size: 18px; font-weight: 600; color: var(--text-primary);">
            <i class="fas fa-lock" style="margin-right: 8px; color: var(--primary);"></i>Nouveau Mot de Passe
        </h2>

        <!-- Alert -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <div>Entrez un nouveau mot de passe sécurisé pour cet utilisateur. Le mot de passe doit contenir au minimum 12 caractères, avec au moins une majuscule, une minuscule, un chiffre et un caractère spécial (!@#$%^&*).</div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Erreur lors de la mise à jour:</strong>
                    <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('utilisateurs.reset-password', $user) }}">
            @csrf
            
            <!-- Password Field -->
            <div class="form-group">
                <label>
                    Mot de Passe <span class="required">*</span>
                </label>
                <input 
                    type="password" 
                    name="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    placeholder="••••••••••••"
                    required
                    minlength="12"
                >
                <div class="hint">
                    <strong>Exigences du mot de passe:</strong>
                    <ul style="margin: 4px 0 0 0; padding-left: 20px;">
                        <li>Au moins 12 caractères</li>
                        <li>Au moins une lettre majuscule (A-Z)</li>
                        <li>Au moins une lettre minuscule (a-z)</li>
                        <li>Au moins un chiffre (0-9)</li>
                        <li>Au moins un caractère spécial (!@#$%^&*)</li>
                    </ul>
                </div>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password Field -->
            <div class="form-group">
                <label>
                    Confirmer Mot de Passe <span class="required">*</span>
                </label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    class="form-control @error('password_confirmation') is-invalid @enderror" 
                    placeholder="••••••••••••"
                    required
                    minlength="12"
                >
                @error('password_confirmation')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i>Mettre à Jour le Mot de Passe
                </button>
                <a href="{{ route('utilisateurs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>Annuler
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
