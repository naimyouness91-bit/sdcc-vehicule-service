<?php

namespace App\Notifications;

use App\Models\Car;
use App\Notifications\Traits\RoleAwareNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleMileageExceeded extends Notification implements ShouldQueue
{
    use Queueable, RoleAwareNotification;

    /**
     * Only admins should receive mileage exceeded alerts
     */
    protected array $allowedRoles = ['admin', 'super_admin'];

    public Car $car;

    public function __construct(Car $car)
    {
        $this->car = $car;
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
            'type'               => 'mileage_exceeded',
            'car_id'             => $this->car->id,
            'car_name'           => $this->car->name,
            'kilometrage_actuel' => $this->car->kilometrage_actuel,
            'kilometrage_max'    => $this->car->kilometrage_max,
            'remaining'          => $this->car->remainingKilometers(),
            'message'            => "La voiture {$this->car->name} a dépassé le kilométrage maximal.",
            'title'              => 'Kilométrage dépassé',
            'url'                => route('admin.data.kilometrage'),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🚨 Urgent : Kilométrage dépassé — ' . $this->car->name)
            ->greeting('Bonjour ' . ($notifiable->name ?? ''))
            ->line("Le véhicule **{$this->car->name}** a **dépassé** son kilométrage maximal autorisé.")
            ->line("- Kilométrage actuel : **{$this->car->kilometrage_actuel} km**")
            ->line("- Kilométrage maximum : **{$this->car->kilometrage_max} km**")
            ->action('Voir le kilométrage', route('admin.data.kilometrage'))
            ->line('⚠️ Une action urgente est recommandée. Veuillez planifier la maintenance immédiatement.');
    }
}
