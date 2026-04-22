<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    protected $fillable = ['villa_id', 'date', 'is_available', 'custom_price', 'note'];
    protected $casts    = ['date' => 'date', 'is_available' => 'boolean', 'custom_price' => 'decimal:2'];

    public function villa() { return $this->belongsTo(Villa::class); }
}