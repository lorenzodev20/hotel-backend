<?php

namespace App\Http\Resources\Api\V1\Catalogs;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin App\Models\RoomRule;
 */
class RoomRuleResource extends JsonResource
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
            "room" => $this->roomType?->name,
            "accommodation" => $this->accommodationType?->name,
        ];
    }
}
