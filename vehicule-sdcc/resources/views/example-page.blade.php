@extends('layouts.app')

@section('title', 'Example Page - SDCC')

@section('content')
    <div class="breadcrumb">
        <div class="breadcrumb-item">
            <i class="fas fa-home"></i>
            <a href="{{ route('dashboard') }}" style="color: inherit; text-decoration: none;">Accueil</a>
        </div>
        <div class="breadcrumb-item">
            <i class="fas fa-chevron-right"></i>
        </div>
        <div class="breadcrumb-item active">
            <span>Exemple de Page</span>
        </div>
    </div>

    <h1 class="content-title">Page d'Exemple</h1>

    <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
        <h2 style="color: #2E7D32; margin-bottom: 15px; font-size: 20px;">Bienvenue dans la nouvelle interface</h2>
        <p style="color: #666; line-height: 1.6; margin-bottom: 15px;">
            Cette page utilise la nouvelle mise en page globale avec un navbar supérieur et une barre latérale.
        </p>
        <p style="color: #666; line-height: 1.6;">
            Pour utiliser ce layout dans vos pages, utilisez <code style="background: #f5f5f5; padding: 2px 6px; border-radius: 4px;">@extends('layouts.app')</code>
            et placez votre contenu dans <code style="background: #f5f5f5; padding: 2px 6px; border-radius: 4px;">@section('content')</code>.
        </p>
    </div>
@endsection
