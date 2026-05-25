<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Car;
use App\Models\PlanificationAffectation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class PrintController extends Controller
{
    /**
     * Print vehicles list
     */
    public function printVehicles(Request $request): View
    {
        $vehicles = Car::orderBy('brand', 'asc')
            ->orderBy('model', 'asc')
            ->get(['id', 'brand', 'model', 'license_plate', 'type', 'kilometrage', 'fuel_type', 'status', 'created_at']);

        return view('admin.print.vehicles', compact('vehicles'));
    }

    /**
     * Print reservations list
     */
    public function printReservations(Request $request): View
    {
        $reservations = PlanificationAffectation::with(['employee', 'car'])
            ->orderBy('date_usage', 'desc')
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'employee_name' => $reservation->employee?->name ?? 'N/A',
                    'vehicle_name' => $reservation->car ? ($reservation->car->brand . ' ' . $reservation->car->model) : 'N/A',
                    'destination' => $reservation->destination,
                    'date_usage' => $reservation->date_usage,
                    'start_time' => $reservation->start_time,
                    'end_time' => $reservation->end_time,
                    'status' => $reservation->status_type ?? 'pending',
                    'created_at' => $reservation->created_at
                ];
            });

        return view('admin.print.reservations', compact('reservations'));
    }

    /**
     * Get print preview data for current tab
     */
    public function getPrintPreview(Request $request): JsonResponse
    {
        $tab = $request->input('tab', 'vehicles');
        
        try {
            switch ($tab) {
                case 'vehicles':
                    $vehicles = Car::orderBy('brand', 'asc')
                        ->get(['id', 'brand', 'model', 'license_plate', 'type', 'kilometrage', 'status', 'created_at']);
                    
                    $preview = [
                        'title' => 'Liste des Véhicules',
                        'count' => $vehicles->count(),
                        'data' => $vehicles->take(5)->map(function($vehicle) {
                            return [
                                'brand' => $vehicle->brand,
                                'model' => $vehicle->model,
                                'license_plate' => $vehicle->license_plate,
                                'status' => $vehicle->status,
                                'created_at' => $vehicle->created_at->format('d/m/Y')
                            ];
                        })
                    ];
                    break;
                    
                default:
                    $preview = ['title' => 'Aperçu non disponible', 'count' => 0, 'data' => []];
            }

            return response()->json([
                'success' => true,
                'preview' => $preview
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération de l\'aperçu: ' . $e->getMessage()
            ], 500);
        }
    }
}
