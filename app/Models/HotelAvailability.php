<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class HotelAvailability extends Model
{
    /** @use HasFactory<\Database\Factories\HotelAvailabilityFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "quantity"
    ];

    protected $casts = [
        "quantity" => "integer"
    ];

    public function roomType(): HasOne
    {
        return $this->hasOne(RoomType::class, 'room_type_id');
    }

    public function accommodationType(): HasOne
    {
        return $this->hasOne(AccommodationType::class, 'accommodation_type_id');
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }
}
