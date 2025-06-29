<?php

namespace App\Models;

use App\Models\HotelAvailability;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomType extends Model
{
    /** @use HasFactory<\Database\Factories\RoomTypeFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    protected $casts = [
        'name' => 'string'
    ];

    public function hotelAvailability(): HasMany
    {
        return $this->hasMany(HotelAvailability::class, 'room_type_id');
    }

    public function roomRules(): HasMany
    {
        return $this->hasMany(RoomRule::class);
    }
}
