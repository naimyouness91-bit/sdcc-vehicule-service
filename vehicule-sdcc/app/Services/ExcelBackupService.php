<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExcelBackupService
{
    private string $disk;
    private string $backupPath;

    public function __construct()
    {
        $this->disk = config('hr_excel.disk', 'local');
        $this->backupPath = 'hr/backups/';
    }

    /**
     * Create a backup of the current Excel file
     */
    public function createBackup(): string
    {
        $excelPath = config('hr_excel.excel_path', 'hr/main_requests.xlsx');
        $disk = Storage::disk($this->disk);

        if (!$disk->exists($excelPath)) {
            throw new \Exception('Excel file does not exist');
        }

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $backupFilename = "main_requests_backup_{$timestamp}.xlsx";
        $backupFullPath = $this->backupPath . $backupFilename;

        // Ensure backup directory exists
        if (!$disk->exists($this->backupPath)) {
            $disk->makeDirectory($this->backupPath);
        }

        // Copy the file
        $disk->copy($excelPath, $backupFullPath);

        // Clean old backups (keep last 10)
        $this->cleanupOldBackups();

        return $backupFilename;
    }

    /**
     * Restore from a backup file
     */
    public function restoreFromBackup(string $backupFilename): bool
    {
        $excelPath = config('hr_excel.excel_path', 'hr/main_requests.xlsx');
        $backupFullPath = $this->backupPath . $backupFilename;
        $disk = Storage::disk($this->disk);

        if (!$disk->exists($backupFullPath)) {
            throw new \Exception('Backup file does not exist');
        }

        // Create backup of current file before restore
        $this->createBackup();

        // Restore the backup
        return $disk->copy($backupFullPath, $excelPath);
    }

    /**
     * List all available backups
     */
    public function listBackups(): array
    {
        $disk = Storage::disk($this->disk);

        if (!$disk->exists($this->backupPath)) {
            return [];
        }

        $files = $disk->files($this->backupPath);
        $backups = [];

        foreach ($files as $file) {
            if (str_ends_with($file, '.xlsx')) {
                $filename = basename($file);
                $timestamp = $this->extractTimestampFromFilename($filename);
                $backups[] = [
                    'filename' => $filename,
                    'timestamp' => $timestamp,
                    'size' => $disk->size($file),
                    'created_at' => $disk->lastModified($file)
                ];
            }
        }

        // Sort by timestamp (newest first)
        usort($backups, fn($a, $b) => $b['created_at'] <=> $a['created_at']);

        return $backups;
    }

    /**
     * Delete a specific backup
     */
    public function deleteBackup(string $backupFilename): bool
    {
        $backupFullPath = $this->backupPath . $backupFilename;
        $disk = Storage::disk($this->disk);

        if (!$disk->exists($backupFullPath)) {
            return false;
        }

        return $disk->delete($backupFullPath);
    }

    /**
     * Clean old backups (keep last 10)
     */
    private function cleanupOldBackups(): void
    {
        $backups = $this->listBackups();
        
        if (count($backups) > 10) {
            $disk = Storage::disk($this->disk);
            $toDelete = array_slice($backups, 10); // Keep first 10, delete rest
            
            foreach ($toDelete as $backup) {
                $disk->delete($this->backupPath . $backup['filename']);
            }
        }
    }

    /**
     * Extract timestamp from backup filename
     */
    private function extractTimestampFromFilename(string $filename): string
    {
        if (preg_match('/main_requests_backup_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})\.xlsx/', $filename, $matches)) {
            return Carbon::createFromFormat('Y-m-d_H-i-s', $matches[1])->format('Y-m-d H:i:s');
        }
        
        return 'Unknown';
    }
}
