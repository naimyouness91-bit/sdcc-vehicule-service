<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;

class ServiceApiController extends Controller
{
    /**
     * Get services grouped by department
     */
    public function grouped()
    {
        $services = Service::where('is_active', true)
            ->orderBy('department')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('department')
            ->map(function ($group) {
                return $group->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'display_name' => $service->display_name,
                        'department' => $service->department,
                    ];
                })->values();
            });

        return response()->json($services);
    }

    /**
     * Get all departments
     */
    public function departments()
    {
        $departments = Service::where('is_active', true)
            ->select('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department')
            ->filter()
            ->values();

        return response()->json($departments);
    }

    /**
     * Get services by department
     */
    public function byDepartment($department)
    {
        $services = Service::where('department', $department)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('display_name')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'display_name' => $service->display_name,
                    'department' => $service->department,
                ];
            });

        return response()->json($services);
    }
}
