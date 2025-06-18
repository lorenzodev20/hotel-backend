<?php

namespace App\Http\Resources\Api\V1\Hotel;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @mixin App\Models\HotelAvailability
 */
class HotelAvailabilityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "quantity" => $this->quantity,
            "room_type" => $this->getRoomTypeName(),
            "accommodation_type" => $this->getAccommodationTypeName(),
            "hotel" => $this->hotel?->name,
        ];
    }
}
