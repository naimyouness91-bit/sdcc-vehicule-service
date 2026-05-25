<?php

namespace App\Exports\Pdf;

use App\Models\Demande;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class HrReservationsExport implements FromView, WithTitle
{
    protected $reservations;
    protected $generatedBy;
    protected $generatedAt;

    public function __construct()
    {
        $this->reservations = Demande::with(['user', 'car'])
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'employee_name' => $reservation->user?->name ?? 'N/A',
                    'vehicle_name' => $reservation->car ? $reservation->car->name : 'N/A',
                    'destination' => $reservation->destination,
                    'start_date' => $reservation->start_date,
                    'end_date' => $reservation->end_date,
                    'start_time' => $reservation->start_time,
                    'end_time' => $reservation->end_time,
                    'kilometers' => $reservation->kilometers,
                    'status' => $reservation->status,
                    'created_at' => $reservation->created_at
                ];
            });
        
        $this->generatedBy = auth()->user()->name ?? 'System';
        $this->generatedAt = now();
    }

    public function view(): View
    {
        return view('exports.pdf.reservations', [
            'reservations' => $this->reservations,
            'generatedBy' => $this->generatedBy,
            'generatedAt' => $this->generatedAt,
            'statistics' => $this->getStatistics()
        ]);
    }

    public function title(): string
    {
        return 'Rapport des Réservations - SDCC';
    }

    private function getStatistics(): array
    {
        return [
            'total' => $this->reservations->count(),
            'approved' => $this->reservations->where('status', 'approved')->count(),
            'pending' => $this->reservations->where('status', 'pending')->count(),
            'rejected' => $this->reservations->where('status', 'rejected')->count(),
            'cancelled' => $this->reservations->where('status', 'cancelled')->count(),
            'this_month' => $this->reservations->filter(function($r) {
                return $r['start_date'] && \Carbon\Carbon::parse($r['start_date'])->isCurrentMonth();
            })->count()
        ];
    }
}
