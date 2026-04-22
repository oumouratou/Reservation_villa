<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reclamation extends Model
{
    protected $fillable = [
        'reservation_id',
        'user_id',
        'sujet',
        'description',
        'statut',
        'reponse_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
