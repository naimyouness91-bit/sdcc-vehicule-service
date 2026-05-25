@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Gestion des Services</h1>
        <a href="{{ route('services.create') }}" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition">
            <i class="fas fa-plus"></i> Ajouter un Service
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filter by Department -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow">
        <form method="GET" class="flex gap-4 items-center">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filtrer par Département</label>
                <select name="department" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">-- Tous les Départements --</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept }}" @selected(request('department') == $dept)>
                            {{ $dept }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="pt-6">
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Services Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-200 border-b border-gray-300">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nom Interne</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nom Affiché</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Département</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Description</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Ordre</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actif</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($services as $service)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-6 py-3 text-sm text-gray-900">{{ $service->name }}</td>
                        <td class="px-6 py-3 text-sm text-gray-900 font-medium">{{ $service->display_name }}</td>
                        <td class="px-6 py-3 text-sm text-gray-700">{{ $service->department }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ substr($service->description, 0, 50) }}...</td>
                        <td class="px-6 py-3 text-sm text-gray-900">{{ $service->sort_order }}</td>
                        <td class="px-6 py-3 text-sm">
                            @if ($service->is_active)
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Actif</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold">Inactif</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center text-sm">
                            <a href="{{ route('services.edit', $service) }}" class="text-blue-600 hover:text-blue-800 mr-3">
                                <i class="fas fa-edit"></i> Éditer
                            </a>
                            <form action="{{ route('services.destroy', $service) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Êtes-vous sûr?')">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Aucun service trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($services->hasPages())
        <div class="mt-6">
            {{ $services->links() }}
        </div>
    @endif
</div>
@endsection
