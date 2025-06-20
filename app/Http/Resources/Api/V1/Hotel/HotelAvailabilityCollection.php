<?php

namespace App\Http\Resources\Api\V1\Hotel;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class HotelAvailabilityCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }

    public $collects = 'App\Http\Resources\Api\V1\Hotel\HotelAvailabilityResource';
}
