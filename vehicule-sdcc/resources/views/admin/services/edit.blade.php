@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Éditer Service</h1>
        <p class="text-gray-600 mt-2">Modifiez les détails du service</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('services.update', $service) }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom Interne *</label>
            <input type="text" id="name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" 
                   value="{{ old('name', $service->name) }}" required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">Nom Affiché *</label>
            <input type="text" id="display_name" name="display_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" 
                   value="{{ old('display_name', $service->display_name) }}" required>
            @error('display_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Département *</label>
            <select id="department" name="department" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                <option value="">-- Sélectionnez un Département --</option>
                <option value="General Management" @selected(old('department', $service->department) == 'General Management')>General Management</option>
                <option value="Commercial & Marketing" @selected(old('department', $service->department) == 'Commercial & Marketing')>Commercial & Marketing</option>
                <option value="Supply Chain" @selected(old('department', $service->department) == 'Supply Chain')>Supply Chain</option>
                <option value="Technical, Maintenance & HSE" @selected(old('department', $service->department) == 'Technical, Maintenance & HSE')>Technical, Maintenance & HSE</option>
                <option value="Human Resources" @selected(old('department', $service->department) == 'Human Resources')>Human Resources</option>
                <option value="Finance" @selected(old('department', $service->department) == 'Finance')>Finance</option>
                <option value="Information Systems" @selected(old('department', $service->department) == 'Information Systems')>Information Systems</option>
            </select>
            @error('department')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('description', $service->description) }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Ordre de Tri</label>
            <input type="number" id="sort_order" name="sort_order" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" 
                   value="{{ old('sort_order', $service->sort_order) }}" min="0">
            @error('sort_order')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" class="w-4 h-4 text-green-600 rounded" 
                       @checked(old('is_active', $service->is_active))>
                <span class="text-sm font-medium text-gray-700">Service Actif</span>
            </label>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition font-medium">
                <i class="fas fa-save"></i> Mettre à Jour
            </button>
            <a href="{{ route('services.index') }}" class="flex-1 bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition font-medium text-center">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>
@endsection
