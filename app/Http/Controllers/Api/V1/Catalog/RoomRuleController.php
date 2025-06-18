<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Traits\LogErrors;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Catalog\RoomRuleRepository;
use App\Http\Resources\Api\V1\Catalogs\RoomRuleResource;

class RoomRuleController extends Controller
{
    use LogErrors, ApiResponseTrait;

    public function __construct(
        private RoomRuleRepository $repository
    ) {}

    public function index(): JsonResponse
    {
        try {
            return $this->responseWithoutData(
                RoomRuleResource::collection($this->repository->all()),
                Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            $this->printLog($th);
            return $this->responseErrorByException($th);
        }
    }
}
