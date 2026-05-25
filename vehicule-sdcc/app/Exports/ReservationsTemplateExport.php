<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReservationsTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
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

    /**
     * Return empty array (no data rows, only headers)
     */
    public function array(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header row with bold white text on dark green background
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

        // Add empty rows (10 rows) with borders for template use
        $emptyRowStyle = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'border' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E0E0E0'],
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5F5F5'],
            ],
            'font' => [
                'size' => 10,
                'color' => ['rgb' => '666666'],
            ],
        ];

        // Add 10 empty template rows
        for ($row = 2; $row <= 11; $row++) {
            $sheet->getStyle("A{$row}:K{$row}")->applyFromArray($emptyRowStyle);
            $sheet->getRowDimension($row)->setRowHeight(20);
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

        // Add comment to first cell explaining the template
        $sheet->getComment('A1')->setText("Modèle de réservations vide.\nUtilisez ce fichier pour importer des données.\nRemplissez les colonnes selon le format requis.");

        return [];
    }
}
