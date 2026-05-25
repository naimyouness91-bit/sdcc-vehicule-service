<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListAdminUsers extends Command
{
    protected $signature = 'debug:list-admins';
    protected $description = 'List users with admin or super_admin roles';

    public function handle()
    {
        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'super_admin']);
        })->get();

        if ($users->isEmpty()) {
            $this->info('No admin or super_admin users found.');
            return 0;
        }

        foreach ($users as $u) {
            $this->line($u->id . ' <' . $u->email . '> - roles: ' . $u->roles->pluck('name')->join(', '));
        }

        return 0;
    }
}
