<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the services.
     */
    public function index()
    {
        $services = Service::orderBy('department')
            ->orderBy('sort_order')
            ->orderBy('display_name')
            ->paginate(50);

        $departments = Service::select('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department')
            ->filter();

        return view('admin.services.index', compact('services', 'departments'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        $departments = Service::select('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department')
            ->filter();

        return view('admin.services.create', compact('departments'));
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:services,name|string|max:255',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'department' => 'required|string|max:255',
            'sort_order' => 'integer|min:0|max:999',
            'is_active' => 'boolean',
        ]);

        Service::create($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service créé avec succès.');
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service)
    {
        $departments = Service::select('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department')
            ->filter();

        return view('admin.services.edit', compact('service', 'departments'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|unique:services,name,' . $service->id . '|string|max:255',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'department' => 'required|string|max:255',
            'sort_order' => 'integer|min:0|max:999',
            'is_active' => 'boolean',
        ]);

        $service->update($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service mis à jour avec succès.');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Service supprimé avec succès.');
    }

    /**
     * Get services grouped by department (for API)
     */
    public function grouped()
    {
        $grouped = Service::where('is_active', true)
            ->orderBy('department')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('department');

        return response()->json($grouped);
    }
}
