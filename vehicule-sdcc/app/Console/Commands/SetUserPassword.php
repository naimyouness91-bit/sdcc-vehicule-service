<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SetUserPassword extends Command
{
    protected $signature = 'debug:set-password {email} {password}';
    protected $description = 'Set password for a user by email (for testing)';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();
        if (! $user) {
            $this->error('User not found: ' . $email);
            return 1;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info('Password updated for ' . $email);
        return 0;
    }
}
