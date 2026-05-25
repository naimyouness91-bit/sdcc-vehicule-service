<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'users.create',
            'users.update',
            'users.delete',
            'users.reset_password',
            'cars.manage',
            'reservations.manage',
            'reports.view',
            'reservations.own.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $superAdminRole = Role::findOrCreate('super_admin', 'web');
        $adminRole = Role::findOrCreate('admin', 'web');

        $superAdminRole->syncPermissions(Permission::all());
        $adminRole->syncPermissions([
            'users.create',
            'users.update',
            'users.reset_password',
            'cars.manage',
            'reservations.manage',
            'reservations.own.manage',
            'reports.view',
        ]);

        $superAdmin = User::updateOrCreate(
            ['email' => env('SUPER_ADMIN_EMAIL', 'superadmin@sdcc.ma')],
            [
                'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
                // Use a known default password for test environment and CI ('password')
                'password' => Hash::make(env('SUPER_ADMIN_PASSWORD', 'password')),
                'service' => env('SUPER_ADMIN_SERVICE', 'Direction Generale'),
            ]
        );

        $superAdmin->syncRoles([$superAdminRole]);

        // Call seeders (ensure employees are created before admin user in CarSeeder)
        $this->call(ServiceSeeder::class);
        $this->call(EmployeeSeeder::class);
        $this->call(CarSeeder::class);
        $this->call(PlanningZoneSeeder::class);
    }
}

