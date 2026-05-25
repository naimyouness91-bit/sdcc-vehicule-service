<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        
        // 🔥 Disable foreign key checks temporarily
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        /**
         * 1. Delete demandes (child table first)
         */
        DB::table('demandes')->delete();

        /**
         * 2. Get super admin IDs BEFORE deleting users
         */
        $superAdminIds = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', 'super_admin')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->pluck('users.id')
            ->toArray();

        /**
         * 3. Delete all users except super admin
         */
        if (!empty($superAdminIds)) {
            DB::table('users')
                ->whereNotIn('id', $superAdminIds)
                ->delete();
        }

        /**
         * 4. Clean pivot table safely
         */
        DB::table('model_has_roles')->delete();

        /**
         * 5. Re-attach super admin roles (optional safety step)
         */
        foreach ($superAdminIds as $id) {
            $roleId = DB::table('roles')
                ->where('name', 'super_admin')
                ->value('id');

            if ($roleId) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $roleId,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $id,
                ]);
            }
        }

        // 🔥 Re-enable foreign key checks
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    /**
     * Reverse migration (not needed for cleanup)
     */
    public function down(): void
    {
        // No rollback for data cleanup migration
    }
};