<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccommodationType extends Model
{
    /** @use HasFactory<\Database\Factories\AccommodationTypeFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    protected $casts = [
        'name' => 'string'
    ];

    public function hotelAvailability(): HasMany
    {
        return $this->hasMany(HotelAvailability::class, 'accommodation_type_id');
    }

    public function roomRules(): HasMany
    {
        return $this->hasMany(RoomRule::class);
    }
}
