<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillaPhoto extends Model
{
    protected $fillable = ['villa_id', 'url', 'cloudinary_id', 'caption', 'is_cover', 'sort_order'];
    protected $casts    = ['is_cover' => 'boolean', 'sort_order' => 'integer'];

    public function villa() { return $this->belongsTo(Villa::class); }
}