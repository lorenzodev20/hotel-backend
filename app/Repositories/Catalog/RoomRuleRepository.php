<?php

namespace App\Repositories\Catalog;

use App\Models\RoomRule;
use App\Repositories\Base\BaseRepository;

class RoomRuleRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(RoomRule $model)
    {
        parent::__construct($model, ['roomType', 'accommodationType']);
    }

    public function checkRule(int $roomType, int $accommodationType): bool
    {
        return $this->model->where('room_type_id', $roomType)
            ->where('accommodation_type_id', $accommodationType)
            ->exists();
    }
}
