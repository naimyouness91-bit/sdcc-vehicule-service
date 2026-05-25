<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'department',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all active services
     */
    public static function allActive()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('display_name')
            ->get();
    }

    /**
     * Get services by department
     */
    public static function byDepartment($department)
    {
        return self::where('department', $department)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('display_name')
            ->get();
    }

    /**
     * Get all departments
     */
    public static function departments()
    {
        return self::select('department')
            ->distinct()
            ->where('is_active', true)
            ->whereNotNull('department')
            ->pluck('department')
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Get services grouped by department
     */
    public static function grouped()
    {
        return self::where('is_active', true)
            ->orderBy('department')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('department');
    }
}
