@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('services.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left"></i> Retour à la Liste
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">{{ $service->display_name }}</h1>
            <p class="text-gray-600 mt-2">{{ $service->name }}</p>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-600 font-medium">Département</p>
                <p class="text-lg text-gray-900">{{ $service->department }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-medium">Ordre de Tri</p>
                <p class="text-lg text-gray-900">{{ $service->sort_order }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-medium">Statut</p>
                @if ($service->is_active)
                    <span class="text-lg bg-green-100 text-green-800 px-3 py-1 rounded-full font-semibold">Actif</span>
                @else
                    <span class="text-lg bg-gray-100 text-gray-800 px-3 py-1 rounded-full font-semibold">Inactif</span>
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-600 font-medium">Créé le</p>
                <p class="text-lg text-gray-900">{{ $service->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        @if ($service->description)
            <div class="mb-6">
                <p class="text-sm text-gray-600 font-medium mb-2">Description</p>
                <p class="text-gray-900">{{ $service->description }}</p>
            </div>
        @endif

        <div class="flex gap-4 pt-6 border-t border-gray-200">
            <a href="{{ route('services.edit', $service) }}" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
                <i class="fas fa-edit"></i> Éditer
            </a>
            <form action="{{ route('services.destroy', $service) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition" onclick="return confirm('Êtes-vous sûr?')">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
