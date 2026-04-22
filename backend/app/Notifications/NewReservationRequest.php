<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReservationRequest extends Notification
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
            'type' => 'new_reservation_request',
            'reservation_id' => $this->reservation->id,
            'villa_id' => $this->reservation->villa_id,
            'message' => 'Nouvelle demande de reservation.',
        ];
    }
}
