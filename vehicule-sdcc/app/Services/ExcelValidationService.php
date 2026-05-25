<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\HrRequestsImport;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ExcelValidationService
{
    private array $requiredColumns = [
        'ID',
        'DATE_RESERVATION', 
        'DATE_USAGE',
        'HEURE_DEPART',
        'HEURE_RETOUR',
        'DATE_RESTITUTION',
        'HEURE_RESTITUTION',
        'NOM_PRENOM_DEMANDEUR',
        'USER_ID',
        'DESTINATION',
        'KILOMETRAGE_PREV',
        'MOTIF_DEPLACEMENT',
        'STATUT_AFFECTATION',
        'CAR_ID',
        'OBSERVATIONS_MG',
        'CREATED_AT'
    ];

    private array $validStatuses = ['approuvé', 'en_attente', 'rejeté', 'annulé'];

    /**
     * Validate Excel file structure and data
     */
    public function validateExcelFile(string $filePath): array
    {
        $errors = [];
        $warnings = [];

        try {
            // Check if file exists and is readable
            if (!file_exists($filePath)) {
                return ['errors' => ['File does not exist'], 'warnings' => []];
            }

            // Load the Excel file
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Get the highest row and column
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $columnCount = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

            // Check if we have at least the header row
            if ($highestRow < 2) {
                $errors[] = 'Excel file must have at least 2 rows (title + headers)';
                return ['errors' => $errors, 'warnings' => $warnings];
            }

            // Extract headers (row 2, since row 1 is title)
            $headers = [];
            for ($col = 1; $col <= 16; $col++) {
                $cellValue = $worksheet->getCellByColumnAndRow($col, 2)->getValue();
                $headers[] = trim($cellValue ?? '');
            }

            // Validate required columns
            $missingColumns = array_diff($this->requiredColumns, $headers);
            if (!empty($missingColumns)) {
                $errors[] = 'Missing required columns: ' . implode(', ', $missingColumns);
            }

            // Check for extra columns
            $extraColumns = array_diff($headers, $this->requiredColumns);
            if (!empty($extraColumns)) {
                $warnings[] = 'Extra columns found: ' . implode(', ', $extraColumns);
            }

            // Validate data rows if any exist
            if ($highestRow > 2) {
                $dataValidation = $this->validateDataRows($worksheet, $highestRow);
                $errors = array_merge($errors, $dataValidation['errors']);
                $warnings = array_merge($warnings, $dataValidation['warnings']);
            }

        } catch (\Exception $e) {
            $errors[] = 'Failed to read Excel file: ' . $e->getMessage();
        }

        return [
            'errors' => $errors,
            'warnings' => $warnings,
            'is_valid' => empty($errors)
        ];
    }

    /**
     * Validate data rows in the Excel file
     */
    private function validateDataRows($worksheet, int $highestRow): array
    {
        $errors = [];
        $warnings = [];
        $statusColumnIndex = 13; // STATUT_AFFECTATION is column M (13th column)
        $dateColumns = [2, 3, 6]; // DATE_RESERVATION, DATE_USAGE, DATE_RESTITUTION
        $timeColumns = [4, 5, 7]; // HEURE_DEPART, HEURE_RETOUR, HEURE_RESTITUTION

        for ($row = 3; $row <= $highestRow; $row++) {
            $rowErrors = [];
            $rowWarnings = [];

            // Check if row is completely empty
            $isEmpty = true;
            for ($col = 1; $col <= 16; $col++) {
                $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                if (!empty($cellValue)) {
                    $isEmpty = false;
                    break;
                }
            }

            if ($isEmpty) {
                continue;
            }

            // Validate required fields
            $employeeName = $worksheet->getCellByColumnAndRow(8, $row)->getValue(); // NOM_PRENOM_DEMANDEUR
            if (empty($employeeName)) {
                $rowErrors[] = "Row {$row}: Employee name is required";
            }

            $destination = $worksheet->getCellByColumnAndRow(10, $row)->getValue(); // DESTINATION
            if (empty($destination)) {
                $rowErrors[] = "Row {$row}: Destination is required";
            }

            // Validate status
            $status = $worksheet->getCellByColumnAndRow($statusColumnIndex, $row)->getValue();
            if (!empty($status)) {
                $normalizedStatus = strtolower(trim($status));
                $normalizedStatus = str_replace(['é', 'è', 'ê'], 'e', $normalizedStatus);
                
                if (!in_array($normalizedStatus, ['approuve', 'en_attente', 'rejete', 'annule'])) {
                    $rowWarnings[] = "Row {$row}: Invalid status '{$status}'. Valid statuses: " . implode(', ', $this->validStatuses);
                }
            }

            // Validate dates
            foreach ($dateColumns as $colIndex) {
                $dateValue = $worksheet->getCellByColumnAndRow($colIndex, $row)->getValue();
                if (!empty($dateValue)) {
                    try {
                        $date = new \DateTime($dateValue);
                    } catch (\Exception $e) {
                        $rowErrors[] = "Row {$row}, Column " . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . ": Invalid date format '{$dateValue}'";
                    }
                }
            }

            // Validate times
            foreach ($timeColumns as $colIndex) {
                $timeValue = $worksheet->getCellByColumnAndRow($colIndex, $row)->getValue();
                if (!empty($timeValue)) {
                    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $timeValue)) {
                        $rowWarnings[] = "Row {$row}, Column " . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . ": Time format should be HH:MM (24-hour format)";
                    }
                }
            }

            // Validate kilometers
            $kmValue = $worksheet->getCellByColumnAndRow(11, $row)->getValue(); // KILOMETRAGE_PREV
            if (!empty($kmValue) && !is_numeric($kmValue)) {
                $rowErrors[] = "Row {$row}: Kilometers must be a numeric value";
            }

            $errors = array_merge($errors, $rowErrors);
            $warnings = array_merge($warnings, $rowWarnings);
        }

        return [
            'errors' => $errors,
            'warnings' => $warnings
        ];
    }

    /**
     * Get validation rules for API requests
     */
    public function getValidationRules(): array
    {
        return [
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // Max 10MB
        ];
    }

    /**
     * Validate uploaded Excel file
     */
    public function validateUploadedFile($file): array
    {
        $validator = Validator::make(['file' => $file], $this->getValidationRules());

        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors()->all()
            ];
        }

        return ['valid' => true, 'errors' => []];
    }
}
