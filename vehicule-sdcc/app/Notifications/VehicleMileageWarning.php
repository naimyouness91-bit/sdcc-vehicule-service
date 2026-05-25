<?php

namespace App\Notifications;

use App\Models\Car;
use App\Notifications\Traits\RoleAwareNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleMileageWarning extends Notification implements ShouldQueue
{
    use Queueable, RoleAwareNotification;

    /**
     * Only admins should receive mileage warnings
     */
    protected array $allowedRoles = ['admin', 'super_admin'];

    public Car $car;
    public int $threshold;

    public function __construct(Car $car, int $threshold = 5000)
    {
        $this->car = $car;
        $this->threshold = $threshold;
        $this->queue = 'notifications';
        $this->delay = 0;
    }

    public function via($notifiable): array
    {
        // Check role permissions first
        if (!$this->shouldNotify($notifiable)) {
            return [];
        }
        
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'               => 'mileage_warning',
            'car_id'             => $this->car->id,
            'car_name'           => $this->car->name,
            'kilometrage_actuel' => $this->car->kilometrage_actuel,
            'kilometrage_max'    => $this->car->kilometrage_max,
            'remaining'          => $this->car->remainingKilometers(),
            'message'            => "La voiture {$this->car->name} approche du kilométrage maximal (moins de {$this->threshold} km).",
            'title'              => 'Alerte kilométrage',
            'url'                => route('admin.data.kilometrage'),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $remaining = $this->car->remainingKilometers();

        return (new MailMessage)
            ->subject('⚠️ Alerte kilométrage : ' . $this->car->name)
            ->greeting('Bonjour ' . ($notifiable->name ?? ''))
            ->line("Le véhicule **{$this->car->name}** approche de son kilométrage maximal autorisé.")
            ->line("- Kilométrage actuel : **{$this->car->kilometrage_actuel} km**")
            ->line("- Kilométrage maximum : **{$this->car->kilometrage_max} km**")
            ->line("- Kilomètres restants : **{$remaining} km**")
            ->action('Voir le kilométrage', route('admin.data.kilometrage'))
            ->line('Veuillez planifier la maintenance si nécessaire.');
    }
}
