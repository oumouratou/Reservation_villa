<?php

namespace App\Notifications;

use App\Models\Reclamation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReclamationResponseReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reclamation;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reclamation $reclamation)
    {
        $this->reclamation = $reclamation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Réponse à votre réclamation concernant la réservation #' . $this->reclamation->reservation_id)
                    ->greeting('Bonjour ' . $notifiable->name . ',')
                    ->line('Un agent a répondu à votre réclamation concernant la réservation pour la villa "' . $this->reclamation->reservation->villa->name . '".')
                    ->line('Sujet de la réclamation : ' . $this->reclamation->sujet)
                    ->line('Réponse de l\'agent :')
                    ->line($this->reclamation->reponse_agent)
                    ->action('Voir mes réclamations', url('/client/reclamations'))
                    ->line('Nous espérons que cette réponse vous sera utile.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
