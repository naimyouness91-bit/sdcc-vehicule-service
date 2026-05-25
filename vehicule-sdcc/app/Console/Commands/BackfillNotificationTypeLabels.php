<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillNotificationTypeLabels extends Command
{
    protected $signature = 'notifications:backfill-type-labels {--dry-run : Do not persist changes, only report}';

    protected $description = 'Backfill existing database notifications with human-friendly type_label and badge_color when missing.';

    public function handle(): int
    {
        $this->info('Scanning notifications table...');

        $mappings = [
            'RequestSubmittedNotification' => 'Nouvelle demande',
            'VehicleChangedNotification' => 'Modification véhicule',
            'RequestStatusUpdatedNotification' => 'Statut demande',
            'VehicleReservationNotification' => 'Réservation',
        ];

        $defaultColors = [
            'Nouvelle demande' => '#4CAF50',
            'Modification véhicule' => '#FFA726',
            'Statut demande' => '#FFA726',
            'Réservation' => '#2E7D32',
        ];

        $notifications = DB::table('notifications')->get();
        $count = $notifications->count();
        $this->info("Found {$count} notifications to inspect.");

        $updated = 0;
        foreach ($notifications as $n) {
            $data = json_decode($n->data ?? '{}', true) ?: [];

            if (array_key_exists('type_label', $data)) {
                continue;
            }

            $base = $n->type ? class_basename($n->type) : null;
            $label = $data['type_label'] ?? ($mappings[$base] ?? ($data['type'] ?? $base ?? 'Autre'));
            $data['type_label'] = $label;

            if (!array_key_exists('badge_color', $data)) {
                $data['badge_color'] = $defaultColors[$label] ?? null;
            }

            if ($this->option('dry-run')) {
                $this->line("[DRY] {$n->id} => {$label}");
            } else {
                DB::table('notifications')->where('id', $n->id)->update(['data' => json_encode($data)]);
                $this->info("Updated {$n->id} => {$label}");
            }
            $updated++;
        }

        $this->info("Done. Updated: {$updated}");

        return 0;
    }
}
