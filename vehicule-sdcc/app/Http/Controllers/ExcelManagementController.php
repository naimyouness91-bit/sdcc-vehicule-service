<?php

namespace App\Http\Controllers;

use App\Services\ExcelBackupService;
use App\Services\ExcelLockService;
use App\Services\ExcelValidationService;
use App\Services\HrExcelSyncService;
use App\Imports\HrRequestsImport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExcelManagementController extends Controller
{
    private ExcelBackupService $backupService;
    private ExcelLockService $lockService;
    private ExcelValidationService $validationService;
    private HrExcelSyncService $syncService;

    public function __construct(
        ExcelBackupService $backupService,
        ExcelLockService $lockService,
        ExcelValidationService $validationService,
        HrExcelSyncService $syncService
    ) {
        $this->backupService = $backupService;
        $this->lockService = $lockService;
        $this->validationService = $validationService;
        $this->syncService = $syncService;
    }

    /**
     * Download current Excel file for editing
     */
    public function download(): JsonResponse
    {
        try {
            // Ensure Excel file exists
            $this->syncService->ensureExcelFile();
            
            $filePath = $this->syncService->excelAbsolutePath();
            $downloadName = $this->syncService->downloadName();

            return response()->download($filePath, $downloadName);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download Excel file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload and validate modified Excel file
     */
    public function upload(Request $request): JsonResponse
    {
        try {
            // Validate uploaded file
            $validation = $this->validationService->validateUploadedFile($request->file('excel_file'));
            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file',
                    'errors' => $validation['errors']
                ], 422);
            }

            $file = $request->file('excel_file');
            $tempPath = $file->getRealPath();

            // Validate Excel structure and data
            $excelValidation = $this->validationService->validateExcelFile($tempPath);
            
            if (!empty($excelValidation['errors'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Excel file validation failed',
                    'errors' => $excelValidation['errors'],
                    'warnings' => $excelValidation['warnings']
                ], 422);
            }

            // Import and process the data
            $import = new HrRequestsImport();
            Excel::import($import, $tempPath);
            $results = $import->getResults();

            if ($results['has_errors']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data validation failed',
                    'errors' => $results['errors'],
                    'warnings' => $results['warnings']
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Excel file validated successfully',
                'data' => [
                    'total_rows' => $results['total_rows'],
                    'warnings' => $results['warnings'],
                    'preview' => array_slice($results['data'], 0, 5) // First 5 rows for preview
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process Excel file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Apply the uploaded changes (after validation)
     */
    public function applyChanges(Request $request): JsonResponse
    {
        try {
            // Check if user has lock or can acquire one
            $userId = Auth::id();
            if (!$this->lockService->isLocked() || $this->lockService->getLockInfo()['user_id'] === $userId) {
                if (!$this->lockService->isLocked()) {
                    $this->lockService->acquireLock($userId, 'Applying Excel changes');
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Excel file is currently locked by another user'
                ], 423);
            }

            // Create backup before applying changes
            $backupFilename = $this->backupService->createBackup();

            // Get the validated data from session or request
            $data = $request->input('data', []);
            
            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data provided to apply'
                ], 400);
            }

            // Save the data and rebuild Excel
            $this->syncService->saveRows($data);
            $this->syncService->writeExcel(collect($data));

            // Release the lock
            $this->lockService->releaseLock();

            return response()->json([
                'success' => true,
                'message' => 'Changes applied successfully',
                'backup_filename' => $backupFilename,
                'total_rows' => count($data)
            ]);

        } catch (\Exception $e) {
            // Ensure lock is released on error
            $this->lockService->releaseLock();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply changes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Acquire lock for Excel editing
     */
    public function acquireLock(Request $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            $reason = $request->input('reason', 'Excel editing');

            if ($this->lockService->acquireLock($userId, $reason)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lock acquired successfully',
                    'lock_info' => $this->lockService->getLockInfo()
                ]);
            } else {
                $lockInfo = $this->lockService->getLockInfo();
                return response()->json([
                    'success' => false,
                    'message' => 'Excel file is currently locked',
                    'lock_info' => $lockInfo
                ], 423);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to acquire lock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Release lock for Excel editing
     */
    public function releaseLock(): JsonResponse
    {
        try {
            $userId = Auth::id();
            $lockInfo = $this->lockService->getLockInfo();

            if (!$lockInfo || $lockInfo['user_id'] !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not hold the current lock'
                ], 403);
            }

            if ($this->lockService->releaseLock()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lock released successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to release lock'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to release lock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current lock status
     */
    public function getLockStatus(): JsonResponse
    {
        try {
            $isLocked = $this->lockService->isLocked();
            $lockInfo = $this->lockService->getLockInfo();

            return response()->json([
                'is_locked' => $isLocked,
                'lock_info' => $lockInfo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get lock status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * List available backups
     */
    public function listBackups(): JsonResponse
    {
        try {
            $backups = $this->backupService->listBackups();

            return response()->json([
                'success' => true,
                'backups' => $backups
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to list backups: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore from backup
     */
    public function restoreFromBackup(Request $request): JsonResponse
    {
        try {
            $backupFilename = $request->input('backup_filename');
            
            if (empty($backupFilename)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup filename is required'
                ], 400);
            }

            // Create backup before restore
            $preRestoreBackup = $this->backupService->createBackup();

            if ($this->backupService->restoreFromBackup($backupFilename)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Backup restored successfully',
                    'pre_restore_backup' => $preRestoreBackup
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to restore backup'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Force release lock (admin only)
     */
    public function forceReleaseLock(): JsonResponse
    {
        try {
            if ($this->lockService->forceReleaseLock()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lock force released successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No lock to release'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to force release lock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show Excel management interface
     */
    public function managementInterface()
    {
        return view('admin.excel-management');
    }

    /**
     * Get Excel file information (API)
     */
    public function getExcelInfo(): JsonResponse
    {
        try {
            $info = $this->syncService->getExcelFileInfo();
            
            return response()->json([
                'success' => true,
                'data' => $info
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get Excel info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get data statistics (API)
     */
    public function getDataStats(): JsonResponse
    {
        try {
            $stats = $this->syncService->getDataStatistics();
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get data statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}
