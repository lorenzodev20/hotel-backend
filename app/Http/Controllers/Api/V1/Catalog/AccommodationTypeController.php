<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Traits\LogErrors;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Catalogs\AccommodationTypeResource;
use App\Repositories\Catalog\AccommodationTypeRepository;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AccommodationTypeController extends Controller
{
    use LogErrors, ApiResponseTrait;

    public function __construct(
        private AccommodationTypeRepository $repository
    ) {}

    public function index(): JsonResponse
    {
        try {
            return $this->responseWithoutData(
                AccommodationTypeResource::collection($this->repository->all()),
                Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            $this->printLog($th);
            return $this->responseErrorByException($th);
        }
    }
}
