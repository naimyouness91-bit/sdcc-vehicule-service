<?php

namespace Database\Seeders;

use App\Models\PlanningZone;
use App\Models\User;
use App\Models\Car;
use Illuminate\Database\Seeder;

class PlanningZoneSeeder extends Seeder
{
    /**
     * Seed planning zones with users and vehicles
     */
    public function run(): void
    {
        // ========== ZONE 1: GENERAL LOGISTICS ==========
        // For general-purpose employees using the main fleet
        $zone1 = PlanningZone::firstOrCreate(
            ['name' => 'Logistique Générale'],
            [
                'description' => 'Zone pour les demandes de transport général et déplacements professionnels',
                'status' => 'active',
            ]
        );

        // ========== ZONE 2: COMMERCIAL ==========
        // For commercial/sales team
        $zone2 = PlanningZone::firstOrCreate(
            ['name' => 'Equipe Commerciale'],
            [
                'description' => 'Zone réservée aux commerciaux pour visites clients et prospection',
                'status' => 'active',
            ]
        );

        // ========== ZONE 3: TECHNICAL ==========
        // For technical teams
        $zone3 = PlanningZone::firstOrCreate(
            ['name' => 'Equipe Technique'],
            [
                'description' => 'Zone pour interventions techniques et déplacements spécialisés',
                'status' => 'active',
            ]
        );

        // Find or create users and assign zones
        // Admin user - Logistique Générale (default zone)
        $admin = User::where('email', 'admin@sdcc.ma')->first();
        if ($admin) {
            $admin->update(['planning_zone_id' => $zone1->id]);
            $zone1->users()->syncWithoutDetaching([$admin->id]);
        }

        // ========== ASSIGN VEHICLES TO ZONES ==========
        $vehicles = Car::all();

        // All vehicles available to Logistique Générale (base zone)
        foreach ($vehicles as $vehicle) {
            $zone1->cars()->syncWithoutDetaching([$vehicle->id]);
        }

        // Assign specific vehicles to Commercial and Technical if needed
        // (You can customize this logic based on your business rules)
        if ($vehicles->count() > 1) {
            // Second vehicle for commercial (if available)
            $zone2->cars()->syncWithoutDetaching([$vehicles[1]->id] ?? []);
        }

        if ($vehicles->count() > 0) {
            // First vehicle for technical (shared)
            $zone3->cars()->syncWithoutDetaching([$vehicles[0]->id] ?? []);
        }
    }
}
