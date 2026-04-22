<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reservation;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
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
        $status = $this->reservation->status === 'confirmed' ? 'confirmée' : 'refusée';
        $subject = "Mise à jour de votre réservation pour la villa " . $this->reservation->villa->name;

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Le statut de votre réservation pour la villa "' . $this->reservation->villa->name . '" a été mis à jour.')
            ->line('Nouveau statut : ' . $status)
            ->line('Période : du ' . $this->reservation->start_date->format('d/m/Y') . ' au ' . $this->reservation->end_date->format('d/m/Y') . '.')
            ->action('Voir mes réservations', url('/client/reservations'))
            ->line('Merci d\'utiliser notre plateforme.');
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
