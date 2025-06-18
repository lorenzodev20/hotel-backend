<?php

namespace App\Repositories\Catalog;

use App\Models\RoomType;
use App\Repositories\Base\BaseRepository;

class RoomTypeRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(RoomType $model)
    {
        parent::__construct($model, ['hotelAvailability']);
    }
}
