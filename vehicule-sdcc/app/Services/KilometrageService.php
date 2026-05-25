<?php

namespace App\Services;

use App\Models\Car;
use App\Models\User;
use App\Notifications\VehicleMileageExceeded;
use App\Notifications\VehicleMileageWarning;

class KilometrageService
{
    protected int $threshold;

    public function __construct(int $threshold = 5000)
    {
        $this->threshold = $threshold;
    }

    /**
     * Check all vehicles and send notifications when needed.
     * Returns an array with summary.
     */
    public function checkAndNotify(): array
    {
        $cars = Car::all();
        $admins = User::role('admin')->get();

        $summary = ['exceeded' => [], 'warning' => []];

        foreach ($cars as $car) {
            $status = $car->mileageStatus($this->threshold);

            if ($status === 'Dépassé') {
                $summary['exceeded'][] = $car->id;
                foreach ($admins as $admin) {
                    $admin->notify(new VehicleMileageExceeded($car));
                }
            } elseif ($status === 'Bientôt atteint') {
                $summary['warning'][] = $car->id;
                foreach ($admins as $admin) {
                    $admin->notify(new VehicleMileageWarning($car, $this->threshold));
                }
            }
        }

        return $summary;
    }
}
