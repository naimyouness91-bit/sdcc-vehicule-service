<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\Pdf\HrVehiclesExport;
use App\Exports\Pdf\HrReservationsExport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PdfReportController extends Controller
{
    /**
     * Generate and download PDF report for vehicles
     */
    public function downloadVehiclesReport(Request $request): BinaryFileResponse
    {
        $this->authorizePdfDownload();

        try {
            $export = new HrVehiclesExport();
            $pdf = Pdf::loadView($export->view(), $export->view()->getData())
                ->setPaper('a4', 'portrait')
                ->setOption(['defaultFont' => 'Segoe UI'])
                ->setOption(['isHtml5ParserEnabled' => true])
                ->setOption(['isRemoteEnabled' => false]);

            $filename = 'rapport-véhicules-' . now()->format('d-m-Y-H-i') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate and download PDF report for reservations
     */
    public function downloadReservationsReport(Request $request): BinaryFileResponse
    {
        $this->authorizePdfDownload();

        try {
            $export = new HrReservationsExport();
            $pdf = Pdf::loadView($export->view(), $export->view()->getData())
                ->setPaper('a4', 'portrait')
                ->setOption(['defaultFont' => 'Segoe UI'])
                ->setOption(['isHtml5ParserEnabled' => true])
                ->setOption(['isRemoteEnabled' => false]);

            $filename = 'rapport-réservations-' . now()->format('d-m-Y-H-i') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get PDF generation status and statistics
     */
    public function getPdfStats(Request $request): JsonResponse
    {
        $this->authorizePdfDownload();

        try {
            $tab = $request->input('tab', 'vehicles');
            $stats = [];

            switch ($tab) {
                case 'vehicles':
                    $stats = [
                        'total_records' => \App\Models\Car::count(),
                        'available' => \App\Models\Car::where('status', 'disponible')->count(),
                        'maintenance' => \App\Models\Car::where('status', 'maintenance')->count(),
                        'file_size_estimate' => '~100-150 KB'
                    ];
                    break;
                case 'reservations':
                    $stats = [
                        'total_records' => \App\Models\Demande::count(),
                        'approved' => \App\Models\Demande::where('status', 'approved')->count(),
                        'pending' => \App\Models\Demande::where('status', 'pending')->count(),
                        'file_size_estimate' => '~200-300 KB'
                    ];
                    break;
                default:
                    $stats = ['error' => 'Type de rapport non reconnu'];
            }

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'ready' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Authorize PDF download based on user role
     */
    private function authorizePdfDownload(): void
    {
        $user = auth()->user();
        
        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Accès non autorisé. Seuls les administrateurs peuvent générer des rapports PDF.');
        }
    }

    /**
     * Get available report types
     */
    public function getAvailableReports(): JsonResponse
    {
        $this->authorizePdfDownload();

        $reports = [
            'vehicles' => [
                'name' => 'Rapport des Véhicules',
                'description' => 'Inventaire complet des véhicules avec leur statut',
                'icon' => 'fas fa-car',
                'route' => 'pdf.vehicles'
            ],
            'reservations' => [
                'name' => 'Rapport des Réservations',
                'description' => 'Historique des réservations et leur statut',
                'icon' => 'fas fa-calendar-check',
                'route' => 'pdf.reservations'
            ]
        ];

        return response()->json([
            'success' => true,
            'reports' => $reports
        ]);
    }
}
