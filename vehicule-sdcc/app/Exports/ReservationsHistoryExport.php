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

class ReservationsHistoryExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function __construct(private readonly Collection $reservations)
    {
    }

    public function headings(): array
    {
        return [
            'N°',
            'Date de Demande',
            'Employé',
            'Service',
            'Véhicule',
            'Matricule',
            'Destination',
            'Date de Départ',
            'Date de Retour',
            'Raison',
            'Statut',
        ];
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->reservations as $index => $reservation) {
            $rows[] = [
                $index + 1,
                $reservation->created_at->format('d/m/Y H:i'),
                $reservation->user?->name ?? 'N/A',
                $reservation->user?->service ?? 'N/A',
                $reservation->car?->name ?? 'N/A',
                $reservation->car?->matricule ?? 'N/A',
                $reservation->destination ?? 'N/A',
                $reservation->start_date->format('d/m/Y'),
                $reservation->end_date->format('d/m/Y'),
                $reservation->reason ?? 'N/A',
                ucfirst($reservation->status),
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Define colors for statuses
        $statusColors = [
            'pending' => 'FFF3E0',     // Light orange
            'approved' => 'E8F5E9',    // Light green
            'rejected' => 'FFEBEE',    // Light red
            'cancelled' => 'FFEBEE',   // Light red
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

        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Style data rows
        $currentRow = 2;
        foreach ($this->reservations as $reservation) {
            $statusColor = $statusColors[strtolower($reservation->status)] ?? 'FFFFFF';

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

            $sheet->getStyle("A{$currentRow}:K{$currentRow}")->applyFromArray($rowStyle);
            $sheet->getRowDimension($currentRow)->setRowHeight(20);

            // Center align specific columns (N°, Dates, Statut)
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H{$currentRow}:I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("K{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Bold matricule column
            $sheet->getStyle("F{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("F{$currentRow}")->getFont()->getColor()->setRGB('00d084');

            $currentRow++;
        }

        // Set column widths for better visibility
        $sheet->getColumnDimension('A')->setWidth(8);   // N°
        $sheet->getColumnDimension('B')->setWidth(18);  // Date de Demande
        $sheet->getColumnDimension('C')->setWidth(20);  // Employé
        $sheet->getColumnDimension('D')->setWidth(16);  // Service
        $sheet->getColumnDimension('E')->setWidth(18);  // Véhicule
        $sheet->getColumnDimension('F')->setWidth(14);  // Matricule
        $sheet->getColumnDimension('G')->setWidth(20);  // Destination
        $sheet->getColumnDimension('H')->setWidth(15);  // Date de Départ
        $sheet->getColumnDimension('I')->setWidth(15);  // Date de Retour
        $sheet->getColumnDimension('J')->setWidth(20);  // Raison
        $sheet->getColumnDimension('K')->setWidth(14);  // Statut

        // Freeze header row
        $sheet->freezePane('A2');

        return [];
    }
}
