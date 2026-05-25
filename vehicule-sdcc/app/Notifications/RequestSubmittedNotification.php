<?php

namespace App\Notifications;

use App\Models\Demande;
use App\Notifications\Traits\RoleAwareNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable, RoleAwareNotification;


    /**
     * Configure the queue for this notification.
     * Uses the 'notifications' queue by default.
     */
    public function __construct(
        private readonly string $employeeName,
        private readonly string $destination,
        private readonly ?int $carId = null,
        private readonly ?int $demandeId = null,
        private readonly bool $hasConflict = false,
        private readonly ?string $conflictPeriod = null,
    ) {
        // Only admins and super_admins should receive new request notifications.
        // We use forRoles() from the RoleAwareNotification trait instead of
        // re-declaring $allowedRoles (which would conflict with the trait property).
        $this->forRoles(['admin', 'super_admin']);
        $this->queue = 'notifications';
        $this->delay = 0;
    }

    public function via(object $notifiable): array
    {
        // Check role permissions first
        if (!$this->shouldNotify($notifiable)) {
            return [];
        }
        
        // Send to both database and email
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        $message = $this->hasConflict
            ? $this->employeeName . ' a soumis une demande vers ' . $this->destination . ' — conflit de réservation détecté' . ($this->conflictPeriod ? ' (' . $this->conflictPeriod . ').' : '.')
            : $this->employeeName . ' a soumis une demande vers ' . $this->destination . '.';

        return [
            'title' => $this->hasConflict ? 'Conflit de réservation détecté' : 'Nouvelle demande de réservation',
            'message' => $message,
            'url' => route('admin.data.requests'),
            'icon' => $this->hasConflict ? 'fa-exclamation-triangle' : 'fa-file-alt',
            'type' => $this->hasConflict ? 'reservation_conflict' : 'request_submitted',
            'type_label' => $this->hasConflict ? 'Conflit' : 'Nouvelle demande',
            'badge_color' => $this->hasConflict ? '#e53935' : '#4CAF50',
            'meta' => [
                'employee_name' => $this->employeeName,
                'destination' => $this->destination,
                'car_id' => $this->carId,
                'demande_id' => $this->demandeId,
                'has_conflict' => $this->hasConflict,
                'conflict_period' => $this->conflictPeriod,
            ],
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $carInfo = $this->carId 
            ? 'Véhicule souhaité (ID): ' . $this->carId 
            : 'Aucun véhicule spécifique n\'a été sélectionné.';

        $subject = $this->hasConflict
            ? 'Conflit de réservation — action requise'
            : 'Nouvelle demande de réservation véhicule';

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Bonjour ' . ($notifiable->name ?? 'Administrateur'))
            ->line($this->employeeName . ' a soumis une nouvelle demande de réservation.');

        if ($this->hasConflict) {
            $mail->line('**Conflit de réservation détecté** — le véhicule est déjà réservé ' . ($this->conflictPeriod ?? 'sur cette période') . '.');
        }

        return $mail
            ->line('**Destination:** ' . $this->destination)
            ->line($carInfo)
            ->action('Gérer les demandes', route('admin.data.requests'))
            ->line('Cette notification est également disponible dans votre tableau de bord.')
            ->salutation('Cordialement,\nSystème SDCC');
    }
}
