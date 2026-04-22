<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    protected $fillable = [
        'reservation_id', 'raised_by', 'against',
        'type', 'description', 'status',
        'admin_notes', 'resolution', 'resolved_at', 'resolved_by',
    ];
    protected $casts = ['resolved_at' => 'datetime'];

    public function reservation() { return $this->belongsTo(Reservation::class); }
    public function raisedBy()    { return $this->belongsTo(User::class, 'raised_by'); }
    public function against()     { return $this->belongsTo(User::class, 'against'); }
    public function resolver()    { return $this->belongsTo(User::class, 'resolved_by'); }
}