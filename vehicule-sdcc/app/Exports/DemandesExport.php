<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DemandesExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function __construct(private readonly Collection $demandes)
    {
    }

    public function headings(): array
    {
        return [
            'Employé',
            'Service',
            'Véhicule',
            'Matricule',
            'Destination',
            'Date de Départ',
            'Heure de Départ',
            'Date de Retour',
            'Heure de Retour',
            'Kilométrage',
            'Raison',
            'Statut',
            'Date de Création',
        ];
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->demandes as $demande) {
            // Map status to French label
            $statusLabel = match($demande->status) {
                'pending' => 'En Attente',
                'approved' => 'Approuvée',
                'rejected' => 'Rejetée',
                'cancelled' => 'Annulée',
                default => ucfirst($demande->status),
            };

            $rows[] = [
                $demande->user?->name ?? 'N/A',
                $demande->user?->service ?? 'N/A',
                $demande->car?->name ?? 'N/A',
                $demande->car?->matricule ?? 'N/A',
                $demande->destination ?? 'Non spécifiée',
                $demande->start_date->format('d/m/Y'),
                $demande->start_time ?? '--:--',
                $demande->end_date->format('d/m/Y'),
                $demande->end_time ?? '--:--',
                $demande->kilometers ?? '-',
                $demande->reason ?? 'Aucune',
                $statusLabel,
                $demande->created_at->format('d/m/Y H:i'),
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Define colors for statuses
        $statusColors = [
            'En Attente' => 'FFF3E0',     // Light orange
            'Approuvée' => 'E8F5E9',      // Light green
            'Rejetée' => 'FFEBEE',        // Light red
            'Annulée' => 'FFEBEE',        // Light red
        ];

        // Style header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E7D32'], // Dark green
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'border' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Style data rows
        $currentRow = 2;
        foreach ($this->demandes as $demande) {
            $statusLabel = match($demande->status) {
                'pending' => 'En Attente',
                'approved' => 'Approuvée',
                'rejected' => 'Rejetée',
                'cancelled' => 'Annulée',
                default => ucfirst($demande->status),
            };

            $statusColor = $statusColors[$statusLabel] ?? 'FFFFFF';

            $rowStyle = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $statusColor],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'border' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E0E0E0'],
                    ],
                ],
                'font' => [
                    'size' => 10,
                    'color' => ['rgb' => '1A1A1A'],
                ],
            ];

            $sheet->getStyle("A{$currentRow}:M{$currentRow}")->applyFromArray($rowStyle);
            $sheet->getRowDimension($currentRow)->setRowHeight(20);

            // Center align specific columns
            $sheet->getStyle("F{$currentRow}:I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("L{$currentRow}:M{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Bold employee name and matricule
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("D{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("D{$currentRow}")->getFont()->getColor()->setRGB('2E7D32');

            $currentRow++;
        }

        // Set column widths for better visibility
        $sheet->getColumnDimension('A')->setWidth(18);  // Employé
        $sheet->getColumnDimension('B')->setWidth(16);  // Service
        $sheet->getColumnDimension('C')->setWidth(18);  // Véhicule
        $sheet->getColumnDimension('D')->setWidth(14);  // Matricule
        $sheet->getColumnDimension('E')->setWidth(20);  // Destination
        $sheet->getColumnDimension('F')->setWidth(15);  // Date de Départ
        $sheet->getColumnDimension('G')->setWidth(14);  // Heure de Départ
        $sheet->getColumnDimension('H')->setWidth(15);  // Date de Retour
        $sheet->getColumnDimension('I')->setWidth(14);  // Heure de Retour
        $sheet->getColumnDimension('J')->setWidth(12);  // Kilométrage
        $sheet->getColumnDimension('K')->setWidth(20);  // Raison
        $sheet->getColumnDimension('L')->setWidth(14);  // Statut
        $sheet->getColumnDimension('M')->setWidth(18);  // Date de Création

        // Freeze header row
        $sheet->freezePane('A2');

        return [];
    }
}
