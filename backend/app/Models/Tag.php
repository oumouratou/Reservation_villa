<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name', 'slug'];
    public function villas() { return $this->belongsToMany(Villa::class, 'villa_tag'); }
}