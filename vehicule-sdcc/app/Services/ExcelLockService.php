<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class ExcelLockService
{
    private string $disk;
    private string $lockFile;
    private int $lockTimeout;

    public function __construct()
    {
        $this->disk = config('hr_excel.disk', 'local');
        $this->lockFile = 'hr/.excel_edit.lock';
        $this->lockTimeout = 300; // 5 minutes
    }

    /**
     * Acquire a lock for Excel editing
     */
    public function acquireLock(string $userId, string $reason = 'Excel editing'): bool
    {
        $disk = Storage::disk($this->disk);
        
        // Check if lock already exists and is not expired
        if ($this->isLocked()) {
            $lockInfo = $this->getLockInfo();
            if (!$lockInfo || !$this->isLockExpired($lockInfo)) {
                return false; // Lock is still valid
            }
            // Lock expired, remove it
            $this->releaseLock();
        }

        $lockData = [
            'user_id' => $userId,
            'reason' => $reason,
            'acquired_at' => now()->timestamp,
            'expires_at' => now()->addSeconds($this->lockTimeout)->timestamp
        ];

        return $disk->put($this->lockFile, json_encode($lockData));
    }

    /**
     * Release the Excel lock
     */
    public function releaseLock(): bool
    {
        $disk = Storage::disk($this->disk);
        
        if ($disk->exists($this->lockFile)) {
            return $disk->delete($this->lockFile);
        }
        
        return true;
    }

    /**
     * Check if Excel is currently locked
     */
    public function isLocked(): bool
    {
        $disk = Storage::disk($this->disk);
        
        if (!$disk->exists($this->lockFile)) {
            return false;
        }

        $lockInfo = $this->getLockInfo();
        
        if (!$lockInfo) {
            return false;
        }

        // Check if lock has expired
        if ($this->isLockExpired($lockInfo)) {
            $this->releaseLock();
            return false;
        }

        return true;
    }

    /**
     * Get current lock information
     */
    public function getLockInfo(): ?array
    {
        $disk = Storage::disk($this->disk);
        
        if (!$disk->exists($this->lockFile)) {
            return null;
        }

        $content = $disk->get($this->lockFile);
        $lockData = json_decode($content, true);

        if (!$lockData) {
            return null;
        }

        return [
            'user_id' => $lockData['user_id'] ?? null,
            'reason' => $lockData['reason'] ?? null,
            'acquired_at' => $lockData['acquired_at'] ?? null,
            'expires_at' => $lockData['expires_at'] ?? null,
            'time_remaining' => max(0, ($lockData['expires_at'] ?? 0) - now()->timestamp)
        ];
    }

    /**
     * Extend the current lock
     */
    public function extendLock(string $userId): bool
    {
        $lockInfo = $this->getLockInfo();
        
        if (!$lockInfo || $lockInfo['user_id'] !== $userId) {
            return false;
        }

        $lockData = [
            'user_id' => $userId,
            'reason' => $lockInfo['reason'],
            'acquired_at' => $lockInfo['acquired_at'],
            'expires_at' => now()->addSeconds($this->lockTimeout)->timestamp
        ];

        $disk = Storage::disk($this->disk);
        return $disk->put($this->lockFile, json_encode($lockData));
    }

    /**
     * Force release lock (admin function)
     */
    public function forceReleaseLock(): bool
    {
        return $this->releaseLock();
    }

    /**
     * Check if lock has expired
     */
    private function isLockExpired(array $lockInfo): bool
    {
        return now()->timestamp > ($lockInfo['expires_at'] ?? 0);
    }
}
