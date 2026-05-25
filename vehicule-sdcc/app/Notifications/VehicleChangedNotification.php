<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $action,
        private readonly string $vehicleName
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Mise a jour vehicule',
            'message' => 'Le vehicule "' . $this->vehicleName . '" a ete ' . $this->action . '.',
            'url' => route('cars.index'),
            'icon' => 'fa-car-side',
            'type' => 'vehicle_changed',
            'type_label' => 'Modification véhicule',
            'badge_color' => '#FFA726',
            'meta' => [
                'action' => $this->action,
            ],
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Mise a jour vehicule')
            ->greeting('Bonjour ' . ($notifiable->name ?? ''))
            ->line('Le vehicule "' . $this->vehicleName . '" a ete ' . $this->action . '.')
            ->action('Voir les vehicules', route('cars.index'))
            ->line('Cette notification est egalement disponible dans votre application.');
    }
}
