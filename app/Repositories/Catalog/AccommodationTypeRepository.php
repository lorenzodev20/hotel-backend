<?php

namespace App\Repositories\Catalog;

use App\Models\AccommodationType;
use App\Repositories\Base\BaseRepository;

class AccommodationTypeRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(AccommodationType $model)
    {
        parent::__construct($model, ['hotelAvailability']);
    }
}
