@extends('layouts.app')

@section('title', 'SDCC - Paramètres')

@section('content')
<style>
    .settings-page {
        max-width: 860px;
        margin: 0 auto;
    }

    .settings-header {
        background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 30%, #FFA726 100%);
        color: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }

    .settings-header h1 {
        margin: 0 0 6px 0;
        font-size: 26px;
    }

    .settings-header p {
        margin: 0;
        font-size: 13px;
        opacity: 0.95;
    }

    .settings-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        padding: 22px;
        margin-bottom: 18px;
    }

    .settings-card h2 {
        font-size: 16px;
        margin: 0 0 14px 0;
        color: #1a1a1a;
    }

    .settings-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-top: 1px solid #f0f0f0;
    }

    .settings-row:first-of-type {
        border-top: none;
        padding-top: 0;
    }

    .settings-label {
        font-size: 14px;
        color: #333;
        font-weight: 600;
    }

    .settings-value {
        font-size: 13px;
        color: #666;
        text-align: right;
    }

    .settings-note {
        font-size: 12px;
        color: #888;
        margin-top: 8px;
    }

    .settings-chip {
        background: #E8F5E9;
        color: #2E7D32;
        font-size: 12px;
        font-weight: 700;
        border-radius: 999px;
        padding: 5px 10px;
        text-transform: capitalize;
    }

    .settings-form {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #333;
    }

    .form-group input {
        border: 1px solid #d9e1e7;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 14px;
    }

    .form-group input:focus {
        outline: none;
        border-color: #4CAF50;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.12);
    }

    .error-text {
        color: #c62828;
        font-size: 12px;
    }

    .success-banner {
        margin-bottom: 12px;
        padding: 10px 12px;
        border-radius: 8px;
        background: #E8F5E9;
        color: #1B5E20;
        font-size: 13px;
        font-weight: 600;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        width: fit-content;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2E7D32 0%, #FB8C00 100%);
    }
</style>

<div class="content-wrapper settings-page">
    <div class="settings-header">
        <h1>Paramètres</h1>
        <p>Configuration du compte connecté</p>
    </div>

    @if(session('success'))
        <div class="success-banner">{{ session('success') }}</div>
    @endif

    <div class="settings-card">
        <h2>Compte (Nom et Email)</h2>
        <form method="POST" action="{{ route('settings.profile.update') }}" class="settings-form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nom</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required>
                @error('name', 'profile')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                @error('email', 'profile')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="settings-row">
                <div class="settings-label">Rôle actif</div>
                <div class="settings-value">
                    <span class="settings-chip">{{ $user->primaryRole() }}</span>
                </div>
            </div>
            <div class="settings-row">
                <div class="settings-label">Service</div>
                <div class="settings-value">{{ $user->service ?? 'Non renseigné' }}</div>
            </div>

            <button type="submit" class="btn-primary">Enregistrer le profil</button>
        </form>
    </div>

    <div class="settings-card">
        <h2>Mot de passe</h2>
        <form method="POST" action="{{ route('settings.password.update') }}" class="settings-form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="current_password">Mot de passe actuel</label>
                <input id="current_password" name="current_password" type="password" required>
                @error('current_password', 'password')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Nouveau mot de passe</label>
                <input id="password" name="password" type="password" required>
                @error('password', 'password')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmer le nouveau mot de passe</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required>
            </div>

            <button type="submit" class="btn-primary">Mettre a jour le mot de passe</button>
            <p class="settings-note">Le mot de passe doit contenir au minimum 8 caracteres.</p>
        </form>
    </div>
</div>
@endsection
