<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReservationRejected extends Notification
{
    use Queueable;

    public function __construct(public Reservation $reservation)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'reservation_rejected',
            'reservation_id' => $this->reservation->id,
            'message' => 'Votre reservation a ete rejetee.',
        ];
    }
}
