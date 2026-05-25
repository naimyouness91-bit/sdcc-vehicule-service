<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Demande extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'car_id',
        'destination',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'return_time',
        'kilometers',
        'distance_travelled',
        'reason',
        'status',
        'has_conflict',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'kilometers' => 'integer',
        'distance_travelled' => 'integer',
        'mileage_applied' => 'boolean',
        'has_conflict' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}