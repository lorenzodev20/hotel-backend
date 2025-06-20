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
use App\Http\Requests\Api\V1\Hotel\HotelAvailabilityByHotelRequest;
use App\Http\Requests\Api\V1\Hotel\StoreAvailabilityRequest;
use App\Http\Requests\Api\V1\Hotel\UpdateAvailabilityRequest;
use App\Http\Resources\Api\V1\Hotel\HotelAvailabilityCollection;
use App\Http\Resources\Api\V1\Hotel\HotelAvailabilityResource;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Header;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response as AttributesResponse;

#[Group("Disponibilidad del Hotel", "Gestión de la información de la disponibilidad del hotel")]
#[Header("Content-Type", "application/json")]
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
     * Obtener un listado de la disponibilidad por Hotel
     *
     * Muestra una lista paginada de todos los hoteles disponibles.
     */
    #[QueryParam('hotelId', 'int', 'El ID del hotel sobre la que muestra la disponibilidad.', example: 1)]
    #[QueryParam('page', 'int', 'El número de página para la paginación.', example: 1)]
    #[QueryParam('per_page', 'int', 'El número de elementos por página.', example: 15)]
    // #[AttributesResponse('storage/app/public/scribe/responses/hotel_index.json', Response::HTTP_OK)]
    #[AttributesResponse(['message' => 'Error interno del servidor.'], Response::HTTP_INTERNAL_SERVER_ERROR)]
    public function index(HotelAvailabilityByHotelRequest $request)
    {
        try {
            $query = $this->repository->getByHotelWithPagination(
                hotel: $request->getHotelId(),
                page: $request->getPage(),
                perPage: $request->getPerPage()
            );

            return new HotelAvailabilityCollection($query);
        } catch (\Throwable $th) {
            $this->printLog($th);
            return $this->responseErrorByException($th);
        }
    }

    /**
     * Crear un registro de disponibilidad por Hotel
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
