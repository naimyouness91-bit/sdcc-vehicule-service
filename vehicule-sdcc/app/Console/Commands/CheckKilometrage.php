<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\KilometrageService;

class CheckKilometrage extends Command
{
    protected $signature = 'kilometrage:check';
    protected $description = 'Check vehicle kilometrage and notify admins when thresholds are reached';

    protected KilometrageService $service;

    public function __construct(KilometrageService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function handle()
    {
        $this->info('Lancement de la vérification de kilométrage...');
        $summary = $this->service->checkAndNotify();
        $this->info('Vérification terminée. Résumé: ' . json_encode($summary));
        return 0;
    }
}
