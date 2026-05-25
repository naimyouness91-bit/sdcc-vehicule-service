<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

class HrRequestsImport implements ToCollection, WithStartRow
{
    public $data = [];
    public $errors = [];
    public $warnings = [];

    /**
     * Start reading from row 3 (after title and header rows)
     */
    public function startRow(): int
    {
        return 3;
    }

    /**
     * Process the Excel collection
     */
    public function collection(Collection $rows)
    {
        $this->data = [];
        $this->errors = [];
        $this->warnings = [];
        $rowNumber = 3; // Start from row 3

        foreach ($rows as $row) {
            try {
                $processedRow = $this->processRow($row->toArray(), $rowNumber);
                if ($processedRow) {
                    $this->data[] = $processedRow;
                }
            } catch (\Exception $e) {
                $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
            }
            
            $rowNumber++;
        }
    }

    /**
     * Process a single row
     */
    private function processRow(array $row, int $rowNumber): ?array
    {
        // Check if row is completely empty
        if (empty(array_filter($row))) {
            return null;
        }

        // Map Excel columns to our internal structure
        $processedRow = [
            'id' => $this->getValue($row, 0),
            'request_date' => $this->parseDate($this->getValue($row, 1), $rowNumber, 'DATE_RESERVATION'),
            'date_usage' => $this->parseDate($this->getValue($row, 2), $rowNumber, 'DATE_USAGE'),
            'start_time' => $this->parseTime($this->getValue($row, 3), $rowNumber, 'HEURE_DEPART'),
            'end_time' => $this->parseTime($this->getValue($row, 4), $rowNumber, 'HEURE_RETOUR'),
            'date_return' => $this->parseDate($this->getValue($row, 5), $rowNumber, 'DATE_RESTITUTION'),
            'return_time' => $this->parseTime($this->getValue($row, 6), $rowNumber, 'HEURE_RESTITUTION'),
            'employee' => $this->getValue($row, 7),
            'user_id' => $this->getValue($row, 8),
            'destination' => $this->getValue($row, 9),
            'km' => $this->parseInteger($this->getValue($row, 10), $rowNumber, 'KILOMETRAGE_PREV'),
            'motif_deplacement' => $this->getValue($row, 11),
            'status' => $this->validateStatus($this->getValue($row, 12), $rowNumber),
            'vehicle' => $this->getValue($row, 13),
            'observations_mg' => $this->getValue($row, 14),
            'created_at' => $this->parseDateTime($this->getValue($row, 15), $rowNumber, 'CREATED_AT'),
        ];

        // Validate required fields
        if (empty($processedRow['employee'])) {
            $this->errors[] = "Row {$rowNumber}: Employee name (NOM_PRENOM_DEMANDEUR) is required";
        }

        if (empty($processedRow['destination'])) {
            $this->errors[] = "Row {$rowNumber}: Destination is required";
        }

        // Generate ID if not provided
        if (empty($processedRow['id'])) {
            $processedRow['id'] = now()->timestamp . '_' . substr((string) mt_rand(), -4);
        }

        // Set created_at if not provided
        if (empty($processedRow['created_at'])) {
            $processedRow['created_at'] = now()->toDateTimeString();
        }

        return $processedRow;
    }

    /**
     * Get value from array safely
     */
    private function getValue(array $row, int $index): string
    {
        return $row[$index] ?? '';
    }

    /**
     * Parse date value
     */
    private function parseDate(string $value, int $rowNumber, string $fieldName): string
    {
        if (empty($value)) {
            return '';
        }

        try {
            $date = new \DateTime($value);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            $this->warnings[] = "Row {$rowNumber}: Invalid date format in {$fieldName}: '{$value}'. Expected format: YYYY-MM-DD";
            return $value;
        }
    }

    /**
     * Parse time value
     */
    private function parseTime(string $value, int $rowNumber, string $fieldName): string
    {
        if (empty($value)) {
            return '';
        }

        // Check if it's already in correct format
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $value)) {
            return $value;
        }

        $this->warnings[] = "Row {$rowNumber}: Invalid time format in {$fieldName}: '{$value}'. Expected format: HH:MM (24-hour)";
        return $value;
    }

    /**
     * Parse integer value
     */
    private function parseInteger(string $value, int $rowNumber, string $fieldName): int
    {
        if (empty($value)) {
            return 0;
        }

        $cleaned = preg_replace('/[^0-9]/', '', $value);
        
        if (!is_numeric($cleaned)) {
            $this->warnings[] = "Row {$rowNumber}: Invalid number format in {$fieldName}: '{$value}'";
            return 0;
        }

        return (int) $cleaned;
    }

    /**
     * Validate status value
     */
    private function validateStatus(string $value, int $rowNumber): string
    {
        if (empty($value)) {
            return 'en_attente';
        }

        $normalizedStatus = strtolower(trim($value));
        $normalizedStatus = str_replace(['é', 'è', 'ê'], 'e', $normalizedStatus);

        $validStatuses = [
            'approuve' => 'approuvé',
            'en_attente' => 'en_attente',
            'en attente' => 'en_attente',
            'rejete' => 'rejeté',
            'annule' => 'annulé'
        ];

        if (isset($validStatuses[$normalizedStatus])) {
            return $validStatuses[$normalizedStatus];
        }

        $this->warnings[] = "Row {$rowNumber}: Invalid status '{$value}'. Using 'en_attente' instead. Valid statuses: approuvé, en_attente, rejeté, annulé";
        return 'en_attente';
    }

    /**
     * Parse datetime value
     */
    private function parseDateTime(string $value, int $rowNumber, string $fieldName): string
    {
        if (empty($value)) {
            return now()->toDateTimeString();
        }

        try {
            $date = new \DateTime($value);
            return $date->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            $this->warnings[] = "Row {$rowNumber}: Invalid datetime format in {$fieldName}: '{$value}'. Using current time instead.";
            return now()->toDateTimeString();
        }
    }

    /**
     * Get import results
     */
    public function getResults(): array
    {
        return [
            'data' => $this->data,
            'errors' => $this->errors,
            'warnings' => $this->warnings,
            'total_rows' => count($this->data),
            'has_errors' => !empty($this->errors),
            'has_warnings' => !empty($this->warnings)
        ];
    }
}
