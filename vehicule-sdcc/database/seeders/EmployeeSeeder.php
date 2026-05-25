<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get the employee role
        $employeeRole = Role::findOrCreate('employee', 'web');
        
        // Ensure the permission exists
        $permission = Permission::findOrCreate('reservations.own.manage', 'web');
        
        // Assign the permission to the employee role
        $employeeRole->syncPermissions([$permission]);
        
        // Create test employees with the permission
        $employees = [
            [
                'name' => 'Ahmed Bennani',
                'email' => 'ahmed@sdcc.ma',
                'password' => Hash::make('password'),
                'service' => 'Commerciale',
            ],
            [
                'name' => 'Fatima El Khatib',
                'email' => 'fatima@sdcc.ma',
                'password' => Hash::make('password'),
                'service' => 'Technique',
            ],
            [
                'name' => 'Mohammed Khalid',
                'email' => 'mohammed@sdcc.ma',
                'password' => Hash::make('password'),
                'service' => 'Moyens Généraux',
            ],
        ];
        
        foreach ($employees as $employeeData) {
            $employee = User::updateOrCreate(
                ['email' => $employeeData['email']],
                $employeeData
            );
            
            // Assign the employee role
            $employee->syncRoles([$employeeRole]);
        }
    }
}
