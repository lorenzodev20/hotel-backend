<?php

declare(strict_types=1);

namespace App\Repositories\Hotel;

use App\Exceptions\ResourceNotFound;
use App\Models\City;
use App\Models\Hotel;
use App\Repositories\Base\BaseRepository;
use App\Http\Requests\Api\V1\Hotel\StoreHotelRequest;
use App\Http\Requests\Api\V1\Hotel\UpdateHotelRequest;

class HotelRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(Hotel $model)
    {
        parent::__construct($model, ['city', 'hotelAvailabilities']);
    }

    public function create(StoreHotelRequest $request)
    {
        $model = new Hotel();
        $model->name = $request->input('name');
        $model->address = $request->input('address');
        $model->tax_id = $request->input('tax_id');
        $model->quantity_rooms = $request->input('quantity_rooms');
        $model->city()->associate(City::find($request->input('city_id')));
        return $this->save($model);
    }

    public function update(UpdateHotelRequest $request, int $id)
    {
        $model = $this->get($id);
        if ($model) {
            $model->name = $request->input('name');
            $model->address = $request->input('address');
            $model->tax_id = $request->input('tax_id');
            $model->quantity_rooms = $request->input('quantity_rooms');
            $model->city()->associate(City::find($request->input('city_id')));
            return $this->save($model);
        }
    }

    public function availableRooms(int $hotel): int
    {
        $model = $this->get($hotel);
        if (!$model) {
            throw new ResourceNotFound();
        }

        return $model->quantityRoomsAvailable();
    }

    public function totalRooms(int $hotel): int
    {
        return $this->get($hotel)?->quantity_rooms ?? 0;
    }
}
