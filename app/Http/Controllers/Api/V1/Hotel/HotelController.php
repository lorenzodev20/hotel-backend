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
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Header;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response as AttributesResponse;
use Knuckles\Scribe\Attributes\ResponseFromFile;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group("Hotel", "Gestión de la información de un Hotel")]
#[Header("Content-Type", "application/json")]
class HotelController extends Controller
{
    use LogErrors, ApiResponseTrait;

    private $hotelProp = "hotel";

    public function __construct(
        private HotelRepository $repository
    ) {}
    /**
     * Obtener un listado de hoteles.
     *
     * Muestra una lista paginada de todos los hoteles disponibles.
     */
    #[QueryParam('page', 'int', 'El número de página para la paginación.', example: 1)]
    #[QueryParam('per_page', 'int', 'El número de elementos por página.', example: 15)]
    // #[AttributesResponse('storage/app/public/scribe/responses/hotel_index.json', Response::HTTP_OK)]
    #[AttributesResponse(['message' => 'Error interno del servidor.'], Response::HTTP_INTERNAL_SERVER_ERROR)]
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
     * Crear un nuevo hotel.
     *
     * Permite crear un nuevo registro de hotel con los datos proporcionados.
     */
    #[BodyParam('name', 'string', 'El nombre del hotel.', required: true, example: 'Hotel Test')]
    #[BodyParam('address', 'string', 'La dirección del hotel.', required: true, example: 'CALLE 23 58-25')]
    #[BodyParam('tax_id', 'string', 'Número de NIT del hotel.', required: true, example: '700562369-7')]
    #[BodyParam('quantity_rooms', 'int', 'La cantidad de habitaciones disponibles. Mínimo: 1.', required: true, example: 50)]
    #[BodyParam('city_id', 'int', 'Identificador numérico asociado al hotel.', required: true, example: 167)]
    //// #[ResponseFromFile('storage/app/public/scribe/responses/hotel_store.json', Response::HTTP_OK)]
    #[AttributesResponse(['message' => 'Los datos proporcionados no son válidos.', 'errors' => ['email' => ['El campo email ya ha sido tomado.']]], Response::HTTP_UNPROCESSABLE_ENTITY)]
    #[AttributesResponse(['message' => 'Error interno del servidor.'], Response::HTTP_INTERNAL_SERVER_ERROR)]
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
     * Mostrar un hotel específico.
     *
     * Obtiene los detalles de un hotel por su ID.
     */
    #[UrlParam('hotel', 'int', 'El ID del hotel a mostrar.', example: 1)]
    #[BodyParam('name', 'string', 'El nombre del hotel.', required: true, example: 'Hotel Test')]
    #[BodyParam('address', 'string', 'La dirección del hotel.', required: true, example: 'CALLE 23 58-25')]
    #[BodyParam('tax_id', 'string', 'Número de NIT del hotel.', required: true, example: '700562369-7')]
    #[BodyParam('quantity_rooms', 'int', 'La cantidad de habitaciones disponibles. Mínimo: 1.', required: true, example: 50)]
    #[BodyParam('city_id', 'int', 'Identificador numérico asociado al hotel.', required: true, example: 167)]
    //#[ResponseFromFile('storage/app/public/scribe/responses/hotel_show.json', Response::HTTP_OK)]
    #[AttributesResponse(['message' => 'Hotel no encontrado.'], Response::HTTP_NOT_FOUND)]
    #[AttributesResponse(['message' => 'Error interno del servidor.'], Response::HTTP_INTERNAL_SERVER_ERROR)]
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
     * Actualizar un hotel existente.
     *
     * Modifica la información de un hotel. Valida la cantidad de habitaciones para evitar overbooking.
     */
    #[UrlParam('hotel', 'int', 'El ID del hotel a actualizar.', example: 1)]
    //#[ResponseFromFile('storage/app/public/scribe/responses/hotel_update.json', Response::HTTP_OK)]
    #[AttributesResponse(['message' => 'No puede asignar una cantidad menor ya que las habitaciones están asignadas'], Response::HTTP_UNPROCESSABLE_ENTITY)]
    #[AttributesResponse(['message' => 'Hotel no encontrado.'], Response::HTTP_NOT_FOUND)]
    #[AttributesResponse(['message' => 'Error interno del servidor.'], Response::HTTP_INTERNAL_SERVER_ERROR)]
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
     * Eliminar un hotel.
     *
     * Elimina un hotel y sus disponibilidades asociadas.
     */
    #[UrlParam('hotel', 'int', 'El ID del hotel a eliminar.', example: 1)]
    #[AttributesResponse(['message' => 'Eliminado'], Response::HTTP_OK)]
    #[AttributesResponse(['message' => 'Hotel no encontrado.'], Response::HTTP_OK)]
    #[AttributesResponse(['message' => 'Error interno del servidor.'], Response::HTTP_INTERNAL_SERVER_ERROR)]
    public function destroy(Hotel $hotel): JsonResponse
    {
        try {
            $hotel->hotelAvailabilities()->delete();
            $hotel->delete();
            return $this->responseWithData("Eliminado", Response::HTTP_OK);
        } catch (\Throwable $th) {
            $this->printLog($th);
            return $this->responseErrorByException($th);
        }
    }
}
