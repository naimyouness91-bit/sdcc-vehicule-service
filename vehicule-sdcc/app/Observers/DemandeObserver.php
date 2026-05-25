<?php

namespace App\Observers;

use App\Models\Demande;
use App\Models\Car;
use Illuminate\Support\Facades\DB;
use Throwable;

class DemandeObserver
{
    public function created(Demande $demande): void
    {
        // If a demande is created already approved (rare), try to apply mileage
        $this->applyMileageIfNeeded($demande);
    }

    public function updated(Demande $demande): void
    {
        // If status changed to approved, apply mileage
        if ($demande->wasChanged('status') && $demande->status === Demande::STATUS_APPROVED) {
            $this->applyMileageIfNeeded($demande);
        }
    }

    protected function applyMileageIfNeeded(Demande $demande): void
    {
        if ($demande->mileage_applied) {
            return; // already applied
        }

        $distance = (int) ($demande->distance_travelled ?? $demande->kilometers ?? 0);
        if ($distance <= 0) {
            return; // nothing to apply
        }

        DB::beginTransaction();
        try {
            $car = Car::lockForUpdate()->find($demande->car_id);
            if (! $car) {
                DB::rollBack();
                return;
            }

            $car->kilometrage_actuel = max(0, ($car->kilometrage_actuel ?? 0) + $distance);
            $car->save();

            $demande->mileage_applied = true;
            $demande->save();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
        }
    }
}
