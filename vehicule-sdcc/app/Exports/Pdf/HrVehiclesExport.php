<?php

namespace App\Exports\Pdf;

use App\Models\Car;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class HrVehiclesExport implements FromView, WithTitle
{
    protected $vehicles;
    protected $generatedBy;
    protected $generatedAt;

    public function __construct()
    {
        $this->vehicles = Car::orderBy('name', 'asc')
            ->get(['id', 'name', 'matricule', 'model', 'year', 'km', 'status', 'availability_type', 'created_at']);
        
        $this->generatedBy = auth()->user()->name ?? 'System';
        $this->generatedAt = now();
    }

    public function view(): View
    {
        return view('exports.pdf.vehicles', [
            'vehicles' => $this->vehicles,
            'generatedBy' => $this->generatedBy,
            'generatedAt' => $this->generatedAt,
            'statistics' => $this->getStatistics()
        ]);
    }

    public function title(): string
    {
        return 'Rapport des Véhicules - SDCC';
    }

    private function getStatistics(): array
    {
        return [
            'total' => $this->vehicles->count(),
            'available' => $this->vehicles->where('status', 'available')->count(),
            'unavailable' => $this->vehicles->where('status', 'unavailable')->count(),
            'total_km' => $this->vehicles->sum('km'),
            'models' => $this->vehicles->pluck('model')->unique()->count(),
            'core_vehicles' => $this->vehicles->where('is_core', true)->count()
        ];
    }
}
