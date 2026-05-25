<?php

namespace App\Notifications;

use App\Notifications\Traits\RoleAwareNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable, RoleAwareNotification;

    /**
     * All roles can receive system updates
     */
    protected array $allowedRoles = [];

    public function __construct(
        private readonly string $title,
        private readonly string $message,
        private readonly string $url
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
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'icon' => 'fa-bell',
            'type' => 'system_update',
            'meta' => [
                'type' => 'system_update',
            ],
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->greeting('Bonjour ' . ($notifiable->name ?? ''))
            ->line($this->message)
            ->action('Ouvrir l\'application', $this->url)
            ->line('Vous pouvez également retrouver cette information dans vos notifications in-app.')
            ->salutation('Cordialement,\nSystème SDCC');
    }
}
