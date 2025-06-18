<?php

namespace App\Http\Controllers\Api\V1\Hotel;

use App\Models\Hotel;
use App\Traits\LogErrors;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\Hotel\HotelRepository;
use App\Exceptions\OverbookingRoomsException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Api\V1\Hotel\HotelResource;
use App\Http\Resources\Api\V1\Hotel\HotelCollection;
use App\Http\Requests\Api\V1\Hotel\StoreHotelRequest;
use App\Http\Requests\Api\V1\Hotel\UpdateHotelRequest;
use App\Http\Requests\Api\V1\Paginator\SamplePaginatorRequest;

class HotelController extends Controller
{
    use LogErrors, ApiResponseTrait;

    private $hotelProp = "hotel";

    public function __construct(
        private HotelRepository $repository
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index(SamplePaginatorRequest $request): JsonResponse|HotelCollection
    {
        try {
            $query = $this->repository->samplePaginator(
                page: $request->getPage(),
                perPage: $request->getPerPage()
            );

            return new HotelCollection($query);
        } catch (\Throwable $th) {
            $this->printLog($th);
            return $this->responseErrorByException($th);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHotelRequest $request): JsonResponse
    {
        try {
            return $this->responseWithoutData(
                [$this->hotelProp => new HotelResource($this->repository->create($request))],
                Response::HTTP_CREATED
            );
        } catch (\Throwable $th) {
            $this->printLog($th);
            return $this->responseErrorByException($th);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Hotel $hotel): JsonResponse
    {
        try {
            return $this->responseWithoutData(
                [$this->hotelProp => new HotelResource($hotel)],
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
    public function update(UpdateHotelRequest $request, Hotel $hotel)
    {
        try {
            $available   = $this->repository->availableRooms(hotel: $hotel->id);

            if (
                ($available === 0) && ($request->getQuantity() < $hotel->quantity_rooms) ||
                ($request->getQuantity() < ($hotel->quantity_rooms - $available))
            ) {
                throw new OverbookingRoomsException('No puede asignar una cantidad menor ya que las habitaciones están asignadas');
            }

            return $this->responseWithoutData(
                [$this->hotelProp => new HotelResource($this->repository->update($request, $hotel->id))],
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
    public function destroy(Hotel $hotel): JsonResponse
    {
        try {
            $hotel->delete();
            return $this->responseWithData("Eliminado", Response::HTTP_OK);
        } catch (\Throwable $th) {
            $this->printLog($th);
            return $this->responseErrorByException($th);
        }
    }
}
