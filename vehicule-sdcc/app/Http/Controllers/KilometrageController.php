<?php

namespace App\Http\Controllers;

use App\Services\KilometrageService;
use App\Models\Car;
use Illuminate\Http\Request;

class KilometrageController extends Controller
{
    protected KilometrageService $service;

    public function __construct(KilometrageService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    public function index()
    {
        $cars = Car::orderBy('name')->get();

        return view('kilometrage.index', compact('cars'));
    }

    /**
     * Trigger check and notify (can be used for manual trigger)
     */
    public function check(Request $request)
    {
        $summary = $this->service->checkAndNotify();
        return redirect()->back()->with('success', 'Vérification effectuée.')->with('summary', $summary);
    }
}
