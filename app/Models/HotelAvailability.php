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

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function accommodationType(): BelongsTo
    {
        return $this->belongsTo(AccommodationType::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function getRoomTypeName(): ?string
    {
        return $this->roomType?->name;
    }

    public function getAccommodationTypeName(): ?string
    {
        return $this->AccommodationType?->name;
    }
}
