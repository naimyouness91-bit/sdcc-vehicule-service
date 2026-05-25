<?php

namespace App\Notifications;

use App\Notifications\Traits\RoleAwareNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleReservationNotification extends Notification implements ShouldQueue
{
    use Queueable, RoleAwareNotification;

    /**
     * All roles can receive reservation confirmations
     */
    protected array $allowedRoles = [];

    public function __construct(
        private readonly string $destination,
        private readonly ?int $carId = null,
        private readonly ?int $demandeId = null
    ) {
        $this->queue = 'notifications';
        $this->delay = 0;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        // Determine URL based on user role for proper isolation
        $url = $notifiable->hasAnyRole(['admin', 'super_admin']) 
            ? route('admin.data-management') 
            : route('mes-demandes.index');

        return [
            'title' => 'Réservation enregistrée',
            'message' => 'Votre réservation vers ' . $this->destination . ' a été enregistrée avec succès.',
            'url' => $url,
            'icon' => 'fa-calendar-check',
            'type' => 'reservation_confirmed',
            'type_label' => 'Réservation',
            'badge_color' => '#2E7D32',
            'meta' => [
                'car_id' => $this->carId,
                'destination' => $this->destination,
                'demande_id' => $this->demandeId,
            ],
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Confirmation de réservation véhicule')
            ->greeting('Bonjour ' . ($notifiable->name ?? ''))
            ->line('Votre réservation pour le déplacement vers ' . $this->destination . ' a bien été enregistrée.')
            ->line($this->carId ? '**Véhicule sélectionné (ID):** ' . $this->carId : '**Note:** Aucun véhicule spécifique n\'a été choisi.')
            ->line('Votre demande est maintenant en attente d\'examen par l\'administration.')
            ->action('Voir mes réservations', route('mes-demandes.index'))
            ->line('Vous recevrez une mise à jour lors de la décision (approuvée/rejetée).')
            ->salutation('Cordialement,\nSystème SDCC');
    }
}
