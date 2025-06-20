<?php

namespace App\Http\Resources\Api\V1\Hotel;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin App\Models\Hotel
 */
class HotelResource extends JsonResource
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
            "name" => $this->name,
            "address" => $this->address,
            "tax_id" => $this->tax_id,
            "total_rooms" => $this->quantity_rooms,
            "available_rooms" => $this->quantityRoomsAvailable(),
            "full_address" => $this->address.','.$this->city?->withStateAndCountry(),
            "city_id" => $this->city_id,
            "state_id" => $this->city?->state?->id
        ];
    }
}
