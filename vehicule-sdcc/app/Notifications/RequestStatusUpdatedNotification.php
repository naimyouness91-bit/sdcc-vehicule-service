<?php

namespace App\Notifications;

use App\Notifications\Traits\RoleAwareNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable, RoleAwareNotification;

    /**
     * Employees receive status updates on their own requests
     * No role filtering for this notification (employees always get it)
     */
    protected array $allowedRoles = [];

    public function __construct(
        private readonly string $status,
        private readonly string $destination,
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
        $statusLabel = match($this->status) {
            'approuvee' => '✓ Approuvée',
            'rejetee' => '✗ Rejetée',
            'pending' => '⏳ En attente',
            default => ucfirst($this->status),
        };

        // Employees can only see their own requests
        return [
            'title' => 'Mise à jour de demande',
            'message' => 'Votre demande vers ' . $this->destination . ' a été ' . $statusLabel . '.',
            'url' => route('mes-demandes.index'),
            'icon' => $this->status === 'approuvee' ? 'fa-check-circle' : 'fa-times-circle',
            'type' => 'request_status_updated',
            'type_label' => 'Statut demande',
            'badge_color' => $this->status === 'approuvee' ? '#4CAF50' : ($this->status === 'rejetee' ? '#e53935' : '#FFA726'),
            'meta' => [
                'status' => $this->status,
                'destination' => $this->destination,
                'demande_id' => $this->demandeId,
            ],
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = match($this->status) {
            'approuvee' => '**APPROUVÉE** ✓',
            'rejetee' => '**REJETÉE** ✗',
            'pending' => '**EN ATTENTE** ⏳',
            default => strtoupper($this->status),
        };

        $intro = match($this->status) {
            'approuvee' => 'Bonne nouvelle ! Votre demande a été approuvée.',
            'rejetee' => 'Nous regrettons de vous informer que votre demande a été rejetée.',
            default => 'Votre demande a été mise à jour.',
        };

        $message = (new MailMessage)
            ->subject('Statut de votre demande - ' . $statusLabel)
            ->greeting('Bonjour ' . ($notifiable->name ?? ''))
            ->line($intro)
            ->line('**Destination:** ' . $this->destination)
            ->line('**Statut:** ' . $statusLabel)
            ->action('Voir ma demande', route('mes-demandes.index'));

        if ($this->status === 'rejetee') {
            $message->line('Si vous avez des questions, veuillez contacter l\'administration.');
        } else if ($this->status === 'approuvee') {
            $message->line('Vous pouvez consulter les détails complets dans votre espace personnel.');
        }

        return $message->salutation('Cordialement,\nSystème SDCC');
    }
}
