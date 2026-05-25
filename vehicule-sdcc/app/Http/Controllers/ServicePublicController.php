<?php

namespace App\Http\Controllers;

use App\Models\Service;

class ServicePublicController extends Controller
{
    /**
     * Display services publicly
     */
    public function index()
    {
        // Temporary debug response to isolate errors in view rendering
        try {
            $services = Service::where('is_active', true)
                ->orderBy('department')
                ->orderBy('sort_order')
                ->get()
                ->groupBy('department');

            // return view for normal operation
            return view('services.index', compact('services'));
        } catch (\Throwable $e) {
            // Log and return minimal response to aid debugging
            logger()->error('ServicePublicController@index error: '.$e->getMessage(), ['exception' => $e]);
            return response('Service page error: '.$e->getMessage(), 500);
        }
    }

    /**
     * Get services grouped by department (API)
     */
    public function grouped()
    {
        $services = Service::where('is_active', true)
            ->orderBy('department')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('department');

        return response()->json($services);
    }

    /**
     * Get services for a specific department
     */
    public function byDepartment($department)
    {
        $services = Service::where('department', $department)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('display_name')
            ->get();

        return response()->json($services);
    }
}
