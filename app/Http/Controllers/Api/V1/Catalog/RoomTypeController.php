<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Traits\LogErrors;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Catalogs\RoomTypeResource;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Catalog\RoomTypeRepository;
use Illuminate\Http\JsonResponse;

class RoomTypeController extends Controller
{
    use LogErrors, ApiResponseTrait;

    public function __construct(
        private RoomTypeRepository $repository
    ) {}

    public function index(): JsonResponse
    {
        try {
            return $this->responseWithoutData(
                RoomTypeResource::collection($this->repository->all()),
                Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            $this->printLog($th);
            return $this->responseErrorByException($th);
        }
    }
}
