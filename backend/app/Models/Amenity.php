<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Amenity extends Model
{
    use HasFactory;
    protected $fillable = ['owner_id', 'name', 'price', 'icon', 'category'];
    protected $casts = ['price' => 'decimal:2'];

    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }
    public function villas() { return $this->belongsToMany(Villa::class, 'villa_amenity'); }
}