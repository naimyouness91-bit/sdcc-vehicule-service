<?php

namespace App\Console\Commands;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\ServeCommand as LaravelServeCommand;
use Symfony\Component\Console\Input\InputOption;

class ServeCommand extends LaravelServeCommand
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Start Vite dev server in the background
        $this->startViteServer();

        // Then start Laravel server (calls parent)
        return parent::handle();
    }

    /**
     * Start the Vite development server
     */
    protected function startViteServer()
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            // Windows: start npm run dev in background
            $this->info('[START] Starting Vite dev server...');
            $this->line('');
            
            // Use START command to launch in background without blocking
            pclose(popen('start /B npm run dev', 'r'));
            sleep(2); // Give Vite time to start
            
            $this->info('[OK] Vite dev server: http://localhost:5173');
            $this->line('');
        } else {
            // Unix-like systems
            $this->info('[START] Starting Vite dev server...');
            passthru('npm run dev > /dev/null 2>&1 &');
            sleep(2);
            $this->info('[OK] Vite dev server: http://localhost:5173');
            $this->line('');
        }
    }
}
