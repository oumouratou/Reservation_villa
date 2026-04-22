<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// ==============================
// Review
// ==============================
class Review extends Model
{
    use HasFactory;
    protected $fillable = ['villa_id','reservation_id','user_id','rating','cleanliness','accuracy','communication','location','value','comment','owner_response','is_approved'];
    protected $casts = ['rating'=>'decimal:1','cleanliness'=>'decimal:1','accuracy'=>'decimal:1','communication'=>'decimal:1','location'=>'decimal:1','value'=>'decimal:1','is_approved'=>'boolean'];

    public function villa()       { return $this->belongsTo(Villa::class); }
    public function user()        { return $this->belongsTo(User::class); }
    public function reservation() { return $this->belongsTo(Reservation::class); }
}