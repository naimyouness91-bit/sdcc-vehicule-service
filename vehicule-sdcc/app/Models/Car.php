<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'matricule',
        'model',
        'year',
        'km',
        'kilometrage_max',
        'kilometrage_actuel',
        'status',
        'availability_type',
        'is_core',
    ];

    protected $casts = [
        'year' => 'integer',
        'km' => 'integer',
        'kilometrage_max' => 'integer',
        'kilometrage_actuel' => 'integer',
        'is_core' => 'boolean',
    ];

    /**
     * Remaining kilometers before reaching the max.
     */
    public function remainingKilometers(): int
    {
        return max(0, ($this->kilometrage_max ?? 0) - ($this->kilometrage_actuel ?? 0));
    }

    /**
     * Get mileage status: Normal, Bientôt atteint, Dépassé
     */
    public function mileageStatus(int $threshold = 5000): string
    {
        $current = $this->kilometrage_actuel ?? 0;
        $max = $this->kilometrage_max ?? 0;

        if ($max > 0 && $current >= $max) {
            return 'Dépassé';
        }

        if ($max > 0 && ($max - $current) <= $threshold) {
            return 'Bientôt atteint';
        }

        return 'Normal';
    }

    /**
     * Percentage of mileage used (0..100).
     */
    public function mileagePercentage(): float
    {
        $max = $this->kilometrage_max ?? 0;
        if ($max <= 0) return 0.0;
        return min(100.0, ($this->kilometrage_actuel ?? 0) / $max * 100.0);
    }

    public function demandes(): HasMany
    {
        return $this->hasMany(Demande::class);
    }

    public function kilometrageEntries(): HasMany
    {
        return $this->hasMany(KilometrageEntry::class);
    }
}
