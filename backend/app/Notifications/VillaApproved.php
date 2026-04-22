<?php

namespace App\Notifications;

use App\Models\Villa;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VillaApproved extends Notification
{
    use Queueable;

    public function __construct(public Villa $villa)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'villa_approved',
            'villa_id' => $this->villa->id,
            'message' => 'Votre villa a ete approuvee.',
        ];
    }
}
