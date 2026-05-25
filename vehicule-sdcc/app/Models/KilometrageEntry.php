<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KilometrageEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'created_by',
        'recorded_at',
        'kilometers',
        'note',
    ];

    protected $casts = [
        'recorded_at' => 'date',
        'kilometers' => 'decimal:1',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

