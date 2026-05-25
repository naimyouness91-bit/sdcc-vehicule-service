<?php

namespace App\Services;

use App\Exports\HrRequestsExport;
use App\Imports\HrRequestsImport;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class HrExcelSyncService
{
    private const LEGACY_JSON_PATH  = 'hr/latest_requests.json';
    private const LEGACY_EXCEL_PATH = 'hr/latest_requests.xlsx';

    // ──────────────────────────────────────────────────────────────
    //  Public API
    // ──────────────────────────────────────────────────────────────

    public function all(): Collection
    {
        return collect($this->loadRows());
    }

    public function ensureExcelFile(): void
    {
        $this->writeExcel($this->all());
        $this->cleanupLegacyFiles();
    }

    /**
     * Persist a new vehicle-assignment request and rebuild the Excel file.
     * All fields map 1-to-1 with the planification_affectations columns.
     */
    public function recordSubmission(User $employee, array $payload): void
    {
        $rows = $this->loadRows();

        $rows[] = $this->buildRow($employee, $payload);

        $this->saveRows($rows);
        $this->writeExcel(collect($rows));
        $this->cleanupLegacyFiles();
    }

    /**
     * Update STATUT_AFFECTATION (and optionally OBSERVATIONS_MG) for an
     * existing row identified by employee name + destination.
     * Walks backwards so the most-recent duplicate is updated first.
     */
    public function updateRequestStatus(
        string  $employeeName,
        string  $destination,
        string  $statusType,
        ?string $observations = null
    ): void {
        $rows = $this->loadRows();

        for ($i = count($rows) - 1; $i >= 0; $i--) {
            if (
                ($rows[$i]['employee']    ?? '') === $employeeName &&
                ($rows[$i]['destination'] ?? '') === $destination
            ) {
                $rows[$i]['status_type']     = $statusType;
                $rows[$i]['status']          = $this->statusLabel($statusType);
                $rows[$i]['observations_mg'] = $observations
                    ?? ($rows[$i]['observations_mg'] ?? '');
                break;
            }
        }

        $this->saveRows($rows);
        $this->writeExcel(collect($rows));
        $this->cleanupLegacyFiles();
    }

    // ──────────────────────────────────────────────────────────────
    //  Path / disk helpers (public for controllers/jobs)
    // ──────────────────────────────────────────────────────────────

    public function excelAbsolutePath(): string
    {
        return Storage::disk($this->disk())->path($this->excelPath());
    }

    public function excelRelativePath(): string
    {
        return $this->excelPath();
    }

    public function disk(): string
    {
        return (string) config('hr_excel.disk', 'local');
    }

    public function downloadName(): string
    {
        return (string) config('hr_excel.download_name', 'hr_requests_live.xlsx');
    }

    // ──────────────────────────────────────────────────────────────
    //  Internal helpers
    // ──────────────────────────────────────────────────────────────

    /**
     * Build a single row array whose keys match every column in
     * HrRequestsExport::map() — one place to maintain the schema.
     */
    private function buildRow(User $employee, array $payload): array
    {
        $dateUsage  = $payload['date_usage']  ?? now()->toDateString();
        $dateReturn = $payload['date_return'] ?? $dateUsage;

        $startTime = $payload['start_time'] ?? null;
        $endTime   = $payload['end_time']   ?? null;
        $timeRange = $payload['time'] ?? ($startTime && $endTime
            ? "{$startTime} - {$endTime}"
            : '08:00 - 17:00');

        return [
            // ── identity ──────────────────────────────────────────
            'id'                => now()->timestamp . '_' . substr((string) mt_rand(), -4),
            'user_id'           => $employee->id,
            'employee'          => $employee->name,
            'initial'           => strtoupper(substr($employee->name, 0, 1)),

            // ── dates & times ─────────────────────────────────────
            'request_date'      => now()->toDateString(),           // DATE_RESERVATION
            'date_usage'        => $dateUsage,                      // DATE_USAGE
            'date_return'       => $dateReturn,                     // DATE_RESTITUTION
            'start_time'        => $startTime,                      // HEURE_DEPART
            'end_time'          => $endTime,                        // HEURE_RETOUR
            'return_time'       => $payload['return_time'] ?? $endTime, // HEURE_RESTITUTION
            'time'              => $timeRange,                      // legacy range string

            // ── trip details ──────────────────────────────────────
            'request_type'      => $payload['request_type']      ?? 'Mission',
            'destination'       => $payload['destination']       ?? '',  // DESTINATION
            'km'                => (int) ($payload['km'] ?? $payload['kilometers'] ?? 0), // KILOMETRAGE_PREV
            'motif_deplacement' => $payload['motif_deplacement'] ?? ($payload['reason'] ?? ''), // MOTIF_DEPLACEMENT
            'notes'             => $payload['notes']             ?? ($payload['reason'] ?? ''),

            // ── vehicle ───────────────────────────────────────────
            'vehicle'           => $payload['vehicle'] ?? ($payload['car_id'] ?? ''), // CAR_ID
            'car_id'            => $payload['car_id']  ?? null,

            // ── status — stored as French label to match Excel cell ──
            'status_type'       => 'pending',                      // raw key (internal use)
            'status'            => $this->statusLabel('pending'),  // STATUT_AFFECTATION

            // ── manager notes ─────────────────────────────────────
            'observations_mg'   => $payload['observations_mg'] ?? '', // OBSERVATIONS_MG

            // ── audit ─────────────────────────────────────────────
            'created_at'        => now()->toDateTimeString(),      // CREATED_AT
        ];
    }

    /**
     * French display labels written directly into the STATUT_AFFECTATION cell.
     * These must match the keys in HrRequestsExport after accent-normalisation:
     *   approuvé  → approuve  → green
     *   en_attente             → orange
     *   rejeté    → rejete    → red
     *   annulé    → annule    → red
     */
    private function statusLabel(string $statusType): string
    {
        return match ($statusType) {
            'approved'  => 'approuvé',
            'rejected'  => 'rejeté',
            'cancelled' => 'annulé',
            default     => 'en_attente',
        };
    }

    private function writeExcel(Collection $rows): void
    {
        Excel::store(
            new HrRequestsExport($rows->values()),
            $this->excelPath(),
            $this->disk()
        );
    }

    private function loadRows(): array
    {
        $disk = Storage::disk($this->disk());

        if ($disk->exists($this->jsonPath())) {
            $decoded = json_decode((string) $disk->get($this->jsonPath()), true);
            return is_array($decoded) ? $decoded : [];
        }

        // One-time migration from legacy path so no existing data is lost
        if ($this->jsonPath() !== self::LEGACY_JSON_PATH && $disk->exists(self::LEGACY_JSON_PATH)) {
            $decoded = json_decode((string) $disk->get(self::LEGACY_JSON_PATH), true);
            $rows    = is_array($decoded) ? $decoded : [];

            if ($rows !== []) {
                $this->saveRows($rows);
            }

            return $rows;
        }

        return [];
    }

    public function saveRows(array $rows): void
    {
        Storage::disk($this->disk())->put(
            $this->jsonPath(),
            json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    private function jsonPath(): string
    {
        return (string) config('hr_excel.json_path', 'hr/latest_requests.json');
    }

    private function excelPath(): string
    {
        return (string) config('hr_excel.excel_path', 'hr/latest_requests.xlsx');
    }

    private function cleanupLegacyFiles(): void
    {
        $disk = Storage::disk($this->disk());

        if ($this->jsonPath() !== self::LEGACY_JSON_PATH && $disk->exists(self::LEGACY_JSON_PATH)) {
            $disk->delete(self::LEGACY_JSON_PATH);
        }

        if ($this->excelPath() !== self::LEGACY_EXCEL_PATH && $disk->exists(self::LEGACY_EXCEL_PATH)) {
            $disk->delete(self::LEGACY_EXCEL_PATH);
        }
    }

    // ──────────────────────────────────────────────────────────────
    //  External Excel Management Methods
    // ──────────────────────────────────────────────────────────────

    /**
     * Import data from external Excel file
     */
    public function importFromExcel(string $filePath): array
    {
        try {
            $import = new HrRequestsImport();
            Excel::import($import, $filePath);
            $results = $import->getResults();

            if ($results['has_errors']) {
                throw new \Exception('Import validation failed: ' . implode(', ', $results['errors']));
            }

            return $results;
        } catch (\Exception $e) {
            throw new \Exception('Failed to import Excel: ' . $e->getMessage());
        }
    }

    
    /**
     * Get current Excel file modification time
     */
    public function getExcelModificationTime(): ?int
    {
        $disk = Storage::disk($this->disk());
        
        if (!$disk->exists($this->excelPath())) {
            return null;
        }

        return $disk->lastModified($this->excelPath());
    }

    /**
     * Check if Excel file has been modified since given timestamp
     */
    public function isExcelModifiedSince(int $timestamp): bool
    {
        $modificationTime = $this->getExcelModificationTime();
        
        return $modificationTime ? $modificationTime > $timestamp : false;
    }

    /**
     * Get Excel file size in bytes
     */
    public function getExcelFileSize(): int
    {
        $disk = Storage::disk($this->disk());
        
        if (!$disk->exists($this->excelPath())) {
            return 0;
        }

        return $disk->size($this->excelPath());
    }

    /**
     * Validate Excel file exists and is readable
     */
    public function validateExcelFile(): bool
    {
        $disk = Storage::disk($this->disk());
        
        if (!$disk->exists($this->excelPath())) {
            return false;
        }

        try {
            $filePath = $disk->path($this->excelPath());
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get Excel file info (size, modification time, etc.)
     */
    public function getExcelFileInfo(): array
    {
        $disk = Storage::disk($this->disk());
        
        if (!$disk->exists($this->excelPath())) {
            return [
                'exists' => false,
                'size' => 0,
                'modified_at' => null,
                'is_valid' => false
            ];
        }

        return [
            'exists' => true,
            'size' => $disk->size($this->excelPath()),
            'modified_at' => $disk->lastModified($this->excelPath()),
            'is_valid' => $this->validateExcelFile(),
            'path' => $this->excelPath(),
            'absolute_path' => $this->excelAbsolutePath()
        ];
    }

    /**
     * Force rebuild Excel file from JSON data
     */
    public function rebuildExcel(): void
    {
        $rows = $this->loadRows();
        $this->writeExcel(collect($rows));
    }

    /**
     * Get data statistics
     */
    public function getDataStatistics(): array
    {
        $rows = $this->loadRows();
        $stats = [
            'total_requests' => count($rows),
            'by_status' => [],
            'by_employee' => [],
            'by_destination' => [],
            'date_range' => [
                'earliest' => null,
                'latest' => null
            ]
        ];

        foreach ($rows as $row) {
            // Count by status
            $status = $row['status'] ?? 'unknown';
            $stats['by_status'][$status] = ($stats['by_status'][$status] ?? 0) + 1;

            // Count by employee
            $employee = $row['employee'] ?? 'unknown';
            $stats['by_employee'][$employee] = ($stats['by_employee'][$employee] ?? 0) + 1;

            // Count by destination
            $destination = $row['destination'] ?? 'unknown';
            $stats['by_destination'][$destination] = ($stats['by_destination'][$destination] ?? 0) + 1;

            // Date range
            $requestDate = $row['request_date'] ?? null;
            if ($requestDate) {
                if (!$stats['date_range']['earliest'] || $requestDate < $stats['date_range']['earliest']) {
                    $stats['date_range']['earliest'] = $requestDate;
                }
                if (!$stats['date_range']['latest'] || $requestDate > $stats['date_range']['latest']) {
                    $stats['date_range']['latest'] = $requestDate;
                }
            }
        }

        return $stats;
    }
}