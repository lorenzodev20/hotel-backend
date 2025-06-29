<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    /** @use HasFactory<\Database\Factories\HotelFactory> */
    use HasFactory, SoftDeletes;
    protected $fillable = [
        "name",
        "address",
        "tax_id",
        "quantity_rooms"
    ];
    protected $casts = [
        "name" => "string",
        "address" => "string",
        "tax_id" => "string",
        "quantity_rooms" => "integer"
    ];
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function hotelAvailabilities(): HasMany
    {
        return $this->hasMany(HotelAvailability::class);
    }

    public function quantityRoomsAvailable(): int
    {
        $rooms = $this->hotelAvailabilities()?->get()?->toArray();
        $occupiedRooms = array_reduce($rooms, function ($initial, $item) {
            return $initial + $item['quantity'];
        }, 0);
        return $this->quantity_rooms - $occupiedRooms;
    }
}
