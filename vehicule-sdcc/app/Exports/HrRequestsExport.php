<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class HrRequestsExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    public function __construct(
        private readonly Collection $requests
    ) {}

    public function collection(): Collection
    {
        return $this->requests;
    }

    // ── 16 columns — exact names and order from the image ────────────
    public function headings(): array
    {
        return [
            'ID',                    // A
            'DATE_RESERVATION',      // B
            'DATE_USAGE',            // C
            'HEURE_DEPART',          // D
            'HEURE_RETOUR',          // E
            'DATE_RESTITUTION',      // F
            'HEURE_RESTITUTION',     // G
            'NOM_PRENOM_DEMANDEUR',  // H
            'USER_ID',               // I
            'DESTINATION',           // J
            'KILOMETRAGE_PREV',      // K
            'MOTIF_DEPLACEMENT',     // L
            'STATUT_AFFECTATION',    // M  ← stores French label (approuvé / en_attente / annulé)
            'CAR_ID',                // N
            'OBSERVATIONS_MG',       // O
            'CREATED_AT',            // P
        ];
    }

    public function map($row): array
    {
        // Fallback: parse legacy "08:00 - 17:00" range string if discrete fields absent
        $range = (string) ($row['time'] ?? '');
        [$heureDepart, $heureRetour] = array_pad(
            array_map('trim', explode('-', $range)),
            2,
            ''
        );

        $createdAt = isset($row['created_at'])
            ? Carbon::parse($row['created_at'])->format('Y-m-d H:i:s')
            : Carbon::now()->format('Y-m-d H:i:s');

        // ── CRITICAL FIX: cell must contain the French label, not the raw key ──
        // The color logic in AfterSheet reads this cell and matches French values.
        // Priority: status (French label already set) → status_type (English key, convert) → default
        $statusLabel = $row['status']
            ?? $this->statusLabel($row['status_type'] ?? 'pending');

        return [
            $row['id']              ?? '',                          // A  ID
            $row['request_date']    ?? '',                          // B  DATE_RESERVATION
            $row['date_usage']      ?? '',                          // C  DATE_USAGE
            $row['start_time']      ?? $heureDepart,               // D  HEURE_DEPART
            $row['end_time']        ?? $heureRetour,               // E  HEURE_RETOUR
            $row['date_return']     ?? '',                          // F  DATE_RESTITUTION
            $row['return_time']     ?? ($row['end_time'] ?? ''),   // G  HEURE_RESTITUTION
            $row['employee']        ?? '',                          // H  NOM_PRENOM_DEMANDEUR
            $row['user_id']         ?? '',                          // I  USER_ID
            $row['destination']     ?? '',                          // J  DESTINATION
            $row['km']              ?? '',                          // K  KILOMETRAGE_PREV
            $row['motif_deplacement'] ?? ($row['notes'] ?? ''),    // L  MOTIF_DEPLACEMENT
            $statusLabel,                                           // M  STATUT_AFFECTATION
            $row['vehicle'] ?: ($row['car_id'] ?? ''),             // N  CAR_ID
            $row['observations_mg'] ?? '',                          // O  OBSERVATIONS_MG
            $createdAt,                                             // P  CREATED_AT
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet      = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // ── Row 1 : Title banner ──────────────────────────────────
                $sheet->insertNewRowBefore(1, 1);
                $sheet->mergeCells('A1:P1');
                $sheet->setCellValue(
                    'A1',
                    "TABLE planification_affectations — Données d'exemple (SDCC)"
                );
                $sheet->getStyle('A1:P1')->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '5B7A2E'], // dark olive green from image
                    ],
                    'font' => [
                        'bold'  => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size'  => 11,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(24);

                // ── Row 2 : Column headers ────────────────────────────────
                $sheet->getStyle('A2:P2')->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E8820C'], // orange from image
                    ],
                    'font' => [
                        'bold'  => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size'  => 9,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => 'D0D7E2'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(32);

                // ── Rows 3+ : data rows ───────────────────────────────────
                for ($row = 3; $row <= $highestRow + 1; $row++) {
                    // Read the French label that map() wrote into column M
                    $statusRaw = strtolower(trim((string) $sheet->getCell('M' . $row)->getValue()));
                    // Normalise accented chars for reliable matching
                    $status = $this->normalizeStatus($statusRaw);

                    // Color map keyed on normalised French labels (as seen in image)
                    $statusColors = [
                        'approuve'   => ['bg' => '00B050', 'fg' => 'FFFFFF'], // green
                        'en_attente' => ['bg' => 'FFC000', 'fg' => '000000'], // orange
                        'en attente' => ['bg' => 'FFC000', 'fg' => '000000'],
                        'rejete'     => ['bg' => 'FF0000', 'fg' => 'FFFFFF'], // red
                        'annule'     => ['bg' => 'FF0000', 'fg' => 'FFFFFF'], // red (row 8 in image)
                    ];

                    if (isset($statusColors[$status])) {
                        $c         = $statusColors[$status];
                        $cellStyle = $sheet->getStyle('M' . $row);
                        $cellStyle->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB($c['bg']);
                        $cellStyle->getFont()->getColor()->setRGB($c['fg']);
                        $cellStyle->getFont()->setBold(true);
                        $cellStyle->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }

                    // Row borders
                    $sheet->getStyle('A' . $row . ':P' . $row)
                        ->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN)
                        ->getColor()->setRGB('D9D9D9');

                    // Zebra striping (even rows light grey, as in image)
                    if ($row % 2 === 0) {
                        $sheet->getStyle('A' . $row . ':P' . $row)
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F5F5F5');
                    }
                }

                // ── Auto-size all 16 columns ──────────────────────────────
                foreach (range('A', 'P') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // ── Freeze title + header rows ────────────────────────────
                $sheet->freezePane('A3');
            },
        ];
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    /**
     * Convert English status_type keys → French display labels
     * matching exactly what is visible in the image cells.
     */
    public function statusLabel(string $statusType): string
    {
        return match ($statusType) {
            'approved'  => 'approuvé',
            'rejected'  => 'rejeté',
            'cancelled' => 'annulé',
            default     => 'en_attente',
        };
    }

    /**
     * Strip accents and lowercase for reliable color-map lookup.
     * "Approuvé" → "approuve", "En attente" → "en attente", etc.
     */
    private function normalizeStatus(string $value): string
    {
        $map = ['é' => 'e', 'è' => 'e', 'ê' => 'e', 'à' => 'a', 'â' => 'a', 'î' => 'i', 'û' => 'u'];
        return str_replace(array_keys($map), array_values($map), strtolower($value));
    }
}