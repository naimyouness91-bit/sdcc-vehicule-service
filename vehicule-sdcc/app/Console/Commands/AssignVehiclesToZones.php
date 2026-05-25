<?php

namespace App\Console\Commands;

use App\Models\Car;
use App\Models\PlanningZone;
use App\Models\User;
use Illuminate\Console\Command;

class AssignVehiclesToZones extends Command
{
    protected $signature = 'zones:assign-vehicles';
    protected $description = 'Assign all vehicles to zones and ensure users have zones';

    public function handle()
    {
        $this->info('🔄 Assigning vehicles to zones...');

        // Get or create default zones
        $zone1 = PlanningZone::firstOrCreate(
            ['name' => 'Logistique Générale'],
            [
                'description' => 'Zone pour les demandes de transport général et déplacements professionnels',
                'status' => 'active',
            ]
        );

        $zone3 = PlanningZone::firstOrCreate(
            ['name' => 'Equipe Technique'],
            [
                'description' => 'Zone pour interventions techniques et déplacements spécialisés',
                'status' => 'active',
            ]
        );

        // Get all vehicles
        $vehicles = Car::all();
        $vehicleCount = $vehicles->count();

        if ($vehicleCount === 0) {
            $this->error('❌ No vehicles found in database!');
            return 1;
        }

        // Assign all vehicles to Logistique Générale (default zone)
        foreach ($vehicles as $vehicle) {
            $zone1->cars()->syncWithoutDetaching([$vehicle->id]);
        }

        $this->info("✅ Assigned {$vehicleCount} vehicles to 'Logistique Générale'");

        // Assign employees to zones
        $alice = User::where('email', 'alice@sdcc.ma')->first();
        if ($alice) {
            $alice->update(['planning_zone_id' => $zone1->id]);
            $zone1->users()->syncWithoutDetaching([$alice->id]);
            $this->info("✅ Assigned Alice to 'Logistique Générale'");
        }

        $bob = User::where('email', 'bob@sdcc.ma')->first();
        if ($bob) {
            $bob->update(['planning_zone_id' => $zone3->id]);
            $zone3->users()->syncWithoutDetaching([$bob->id]);
            $this->info("✅ Assigned Bob to 'Equipe Technique'");
        }

        $this->info("\n✨ Zone assignments completed successfully!");
        return 0;
    }
}
