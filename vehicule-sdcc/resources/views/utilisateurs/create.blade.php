@extends('layouts.app')
@section('title', 'SDCC - Créer Utilisateur')
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
        max-width: 950px;
        width: 100%;
        margin: 0 auto;
        padding: 16px 20px;
        box-sizing: border-box;
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

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.full {
        grid-column: 1 / -1;
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

    .form-control:invalid {
        border-color: var(--danger);
    }

    .form-group .hint {
        font-size: 12px;
        color: var(--text-secondary);
        margin-top: 4px;
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

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
    }

    .btn-secondary {
        background: var(--bg-light);
        color: var(--text-primary);
        border: 1px solid var(--border);
    }

    .btn-secondary:hover {
        background: var(--bg-white);
        border-color: var(--text-secondary);
    }

    .alert {
        padding: 14px 16px;
        border-radius: 8px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #dc2626;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border-left: 4px solid #16a34a;
    }

    .error-message {
        color: var(--danger);
        font-size: 12px;
        margin-top: 4px;
    }
</style>

<div class="content-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="fas fa-user-plus"></i>
            Créer Utilisateur
        </h1>
        <a href="{{ route('utilisateurs.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Retour
        </a>
    </div>

    <!-- Create Form -->
    <div class="panel">
        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Erreur!</strong>
                    <ul style="margin: 4px 0 0 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('utilisateurs.store') }}" method="POST">
            @csrf

            <div class="form-grid">
                <!-- Name -->
                <div class="form-group">
                    <label for="name">
                        Nom Complet
                        <span class="required">*</span>
                    </label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required placeholder="Jean Dupont">
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">
                        Email
                        <span class="required">*</span>
                    </label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" required placeholder="jean.dupont@sdcc.ma">
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="hint">Doit être unique et en domaine @sdcc.ma</div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">
                        Mot de Passe
                        <span class="required">*</span>
                    </label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                        required placeholder="••••••••" minlength="8">
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="hint">Minimum 8 caractères</div>
                </div>

                <!-- Password Confirmation -->
                <div class="form-group">
                    <label for="password_confirmation">
                        Confirmer le mot de passe
                        <span class="required">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"
                        required placeholder="••••••••" minlength="8">
                    @error('password_confirmation')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="hint">Répétez le mot de passe</div>
                </div>

                <!-- Service -->
                <div class="form-group">
                    <label for="service">Service <span class="required">*</span></label>
                    @include('utilisateurs.partials.service-dropdown', ['selectId' => 'service', 'selectName' => 'service', 'required' => true])
                </div>

                <!-- Role -->
                <div class="form-group full">
                    <label for="role">
                        Rôle
                        <span class="required">*</span>
                    </label>
                    @php
                        $optionsService = app(App\Services\OptionsService::class);
                        $isSuperAdmin = auth()->user()?->hasRole('super_admin') ?? false;
                        $roles = $optionsService->userAssignableRoles($isSuperAdmin);
                        $oldRole = old('role');
                    @endphp
                    <select id="role" name="role" class="form-control @error('role') is-invalid @enderror" required>
                        <option value="">-- Sélectionner un rôle --</option>
                        @foreach($roles as $rKey => $rLabel)
                            <option value="{{ $rKey }}" @selected($oldRole === $rKey)>{{ $rLabel }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="hint">Super Admin a accès complet au système</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Créer l'utilisateur
                </button>
                <a href="{{ route('utilisateurs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
