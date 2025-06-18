<?php

namespace App\Repositories\Hotel;

use App\Models\HotelAvailability;
use App\Repositories\Base\BaseRepository;
use App\Http\Requests\Api\V1\Hotel\StoreAvailabilityRequest;
use App\Http\Requests\Api\V1\Hotel\UpdateAvailabilityRequest;

class HotelAvailabilityRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(HotelAvailability $model)
    {
        parent::__construct($model, ['roomType', 'accommodationType', 'hotel']);
    }

    public function create(StoreAvailabilityRequest $request)
    {
        $model = new HotelAvailability();
        $model->quantity = $request->quantity;
        $model->room_type_id = $request->room_type_id;
        $model->accommodation_type_id = $request->accommodation_type_id;
        $model->hotel_id = $request->hotel_id;
        return $this->save($model);
    }

    public function update(UpdateAvailabilityRequest $request, int $id)
    {
        $model = $this->get($id);
        if ($model) {
            $model->quantity = $request->quantity;
            $model->room_type_id = $request->room_type_id;
            $model->accommodation_type_id = $request->accommodation_type_id;
            $model->hotel_id = $request->hotel_id;
            return $this->save($model);
        }
    }

    public function duplicatedConfiguration(int $roomType, int $accommodationType, int $hotel, ?int $configId = null): bool
    {
        if ($configId) {
            return $this->model->where('room_type_id', $roomType)
                ->where('accommodation_type_id', $accommodationType)
                ->where('hotel_id', $hotel)
                ->where('id','!=',$configId)
                ->exists();
        }
        return $this->model->where('room_type_id', $roomType)
            ->where('accommodation_type_id', $accommodationType)
            ->where('hotel_id', $hotel)
            ->exists();
    }
}
