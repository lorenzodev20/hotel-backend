<?php

namespace App\Http\Controllers\Api\V1\Hotel;

use App\Traits\LogErrors;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Models\HotelAvailability;
use App\Http\Controllers\Controller;
use App\Repositories\Hotel\HotelRepository;
use App\Exceptions\RoomRuleInvalidException;
use App\Exceptions\OverbookingRoomsException;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Catalog\RoomRuleRepository;
use App\Repositories\Hotel\HotelAvailabilityRepository;
use App\Exceptions\RoomConfigurationDuplicatedException;
use App\Http\Requests\Api\V1\Hotel\StoreAvailabilityRequest;
use App\Http\Requests\Api\V1\Hotel\UpdateAvailabilityRequest;
use App\Http\Resources\Api\V1\Hotel\HotelAvailabilityResource;

class HotelAvailabilityController extends Controller
{
    use LogErrors, ApiResponseTrait;
    private $availabilityProp = 'availability';

    public function __construct(
        private HotelAvailabilityRepository $repository,
        private HotelRepository $hotelRepository,
        private RoomRuleRepository $roomRuleRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAvailabilityRequest $request)
    {
        try {
            // habitaciones menor o igual a las habitaciones disponibles del hotel
            $available  = $this->hotelRepository->availableRooms(hotel: $request->getHotelId());

            if ($request->getQuantity() > $available) {
                throw new OverbookingRoomsException();
            }

            if (!$this->roomRuleRepository->checkRule(
                roomType: $request->getRoomTypeId(),
                accommodationType: $request->getAccommodationTypeId(),
            )) {
                throw new RoomRuleInvalidException();
            }

            if ($this->repository->duplicatedConfiguration(
                roomType: $request->getRoomTypeId(),
                accommodationType: $request->getAccommodationTypeId(),
                hotel: $request->getHotelId()
            )) {
                throw new RoomConfigurationDuplicatedException();
            }

            return $this->responseWithoutData(
                [
                    $this->availabilityProp => new HotelAvailabilityResource(
                        $this->repository->create(request: $request)
                    )
                ],
                Response::HTTP_CREATED
            );
        } catch (\Throwable $th) {
            $this->printLog($th);
            return $this->responseErrorByException($th, $th->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(HotelAvailability $availability)
    {
        try {
            return $this->responseWithoutData(
                [$this->availabilityProp => new HotelAvailabilityResource($availability)],
                Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            $this->printLog($th);
            return $this->responseErrorByException($th);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAvailabilityRequest $request, HotelAvailability $availability)
    {
        try {
            $available  = $this->hotelRepository->availableRooms(hotel: $request->getHotelId());
            $currentQuantity = $availability->quantity;
            if (($request->getQuantity() > $currentQuantity) && ($request->getQuantity() > ($available + $currentQuantity))) {
                throw new OverbookingRoomsException();
            }

            if (!$this->roomRuleRepository->checkRule(
                roomType: $request->getRoomTypeId(),
                accommodationType: $request->getAccommodationTypeId(),
            )) {
                throw new RoomRuleInvalidException();
            }

            if ($this->repository->duplicatedConfiguration(
                roomType: $request->getRoomTypeId(),
                accommodationType: $request->getAccommodationTypeId(),
                hotel: $request->getHotelId(),
                configId: $availability->id
            )) {
                throw new RoomConfigurationDuplicatedException();
            }

            return $this->responseWithoutData(
                [$this->availabilityProp => new HotelAvailabilityResource(
                    $this->repository->update(request: $request, id: $availability->id)
                )],
                Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            $this->printLog($th);
            return $this->responseErrorByException($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HotelAvailability $availability)
    {
        try {
            $availability->delete();
            return $this->responseWithData("Eliminado", Response::HTTP_OK);
        } catch (\Throwable $th) {
            $this->printLog($th);
            return $this->responseErrorByException($th);
        }
    }
}
