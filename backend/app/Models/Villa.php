<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Villa extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'title',
        'slug',
        'description',
        'address',
        'city',
        'country',
        'latitude',
        'longitude',
        'price_per_night',
        'price',
        'weekend_price',
        'cleaning_fee',
        'security_deposit',
        'max_guests',
        'bedrooms',
        'bathrooms',
        'surface',
        'rooms_count',
        'status',        // pending, approved, rejected, suspended
        'listing_type',  // vente, location
        'has_pool',
        'has_garden',
        'has_garage',
        'has_air_conditioning',
        'has_wifi',
        'exceptional_equipments',
        'exceptional_equipments_total',
        'rules',
        'cancellation_policy', // flexible, moderate, strict
        'min_nights',
        'max_nights',
        'rating',
        'reviews_count',
    ];

    protected $casts = [
        'price_per_night'   => 'decimal:2',
        'price'             => 'decimal:2',
        'weekend_price'     => 'decimal:2',
        'cleaning_fee'      => 'decimal:2',
        'security_deposit'  => 'decimal:2',
        'latitude'          => 'decimal:8',
        'longitude'         => 'decimal:8',
        'rating'            => 'decimal:2',
        'max_guests'        => 'integer',
        'bedrooms'          => 'integer',
        'bathrooms'         => 'integer',
        'surface'           => 'decimal:2',
        'rooms_count'       => 'integer',
        'min_nights'        => 'integer',
        'max_nights'        => 'integer',
        'has_pool'          => 'boolean',
        'has_garden'        => 'boolean',
        'has_garage'        => 'boolean',
        'has_air_conditioning' => 'boolean',
        'has_wifi'          => 'boolean',
        'exceptional_equipments' => 'array',
        'exceptional_equipments_total' => 'decimal:2',
    ];

    const STATUS_PENDING   = 'pending';
    const STATUS_APPROVED  = 'approved';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_SUSPENDED = 'suspended';

    // Scopes
    public function scopeApproved($query) { return $query->where('status', self::STATUS_APPROVED); }
    public function scopePending($query)  { return $query->where('status', self::STATUS_PENDING); }

    // Relations
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function photos()
    {
        return $this->hasMany(VillaPhoto::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'villa_amenity');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'villa_tag');
    }

    // Helpers
    public function getPriceForDate(\Carbon\Carbon $date): float
    {
        return $date->isWeekend() && $this->weekend_price
            ? (float)$this->weekend_price
            : (float)$this->price_per_night;
    }
}