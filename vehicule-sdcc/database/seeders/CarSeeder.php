<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ========== CREATE TEST USERS ==========
        $admin = User::updateOrCreate(
            ['email' => 'admin@sdcc.ma'],
            [
                'name' => 'Admin SDCC',
                'password' => Hash::make('password'),
                'service' => 'Moyens Généraux',
            ]
        );
        $admin->syncRoles(['admin']);

        // ========== CREATE VEHICLES ==========
        $fleet = [
            [
                'name' => 'Renault Clio 5',
                'matricule' => '23130T6',
                'model' => 'Explore 1.5 DCI',
                'year' => 2022,
                'km' => 14230,
                'status' => 'disponible',
                'availability_type' => 'both',
            ],
            [
                'name' => 'Renault Kardian',
                'matricule' => 'WW831966',
                'model' => 'Techno 1.5',
                'year' => 2023,
                'km' => 8420,
                'status' => 'disponible',
                'availability_type' => 'both',
            ],
        ];

        foreach ($fleet as $carData) {
            Car::query()->updateOrCreate(
                ['matricule' => $carData['matricule']],
                $carData + ['is_core' => true]
            );
        }
    }
}
