<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Demande;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;

class ShowTestDemandes extends Command
{
    protected $signature = 'debug:show-test-demandes {email=autotest@sdcc.ma}';
    protected $description = 'Affiche les demandes pour l\'utilisateur de test';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        if (! $user) {
            $this->error("User not found: {$email}");
            return 1;
        }

        $demandes = Demande::where('user_id', $user->id)->latest('created_at')->get();

        $this->info('DB: found ' . $demandes->count() . ' demandes for ' . $email);

        // Apply the same mapping as DashboardController::employeeDashboard to ensure compatibility
        $mapped = $demandes->map(function ($demande) {
            $status = strtolower((string) $demande->status);
            $statusLabel = $status === Demande::STATUS_APPROVED ? 'Approuvé' : ($status === Demande::STATUS_PENDING ? 'En attente' : 'Rejeté');
            $vehicleName = $demande->car ? trim($demande->car->name . ' ' . ($demande->car->matricule ?? '')) : '—';

            return [
                'id' => $demande->id,
                'destination' => $demande->destination,
                'status' => $demande->status,
                'status_label' => $statusLabel,
                'created_at' => $demande->created_at?->toDateTimeString(),
                'start_date' => $demande->start_date?->toDateString(),
                'end_date' => $demande->end_date?->toDateString(),
                'date_usage' => $demande->start_date ? \Carbon\Carbon::parse($demande->start_date)->format('d/m/Y') : null,
                'start_time' => $demande->start_time ?? null,
                'end_time' => $demande->end_time ?? null,
                'reason' => $demande->reason ?? null,
                'car' => $demande->car ? ['id' => $demande->car->id, 'name' => $demande->car->name, 'matricule' => $demande->car->matricule ?? null] : null,
                'vehicle' => $vehicleName,
                'employee' => $demande->user?->name ?? null,
                'service' => $demande->user?->service ?? null,
            ];
        });

        $this->line('Mapped demandes:');
        foreach ($mapped as $m) {
            $this->line(json_encode($m));
        }

        return 0;
    }
}
