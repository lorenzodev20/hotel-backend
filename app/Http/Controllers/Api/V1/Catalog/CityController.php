<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Models\City;
use App\Traits\LogErrors;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Api\V1\Catalogs\CityRequest;
use App\Http\Resources\Api\V1\Catalogs\CityResource;

class CityController extends Controller
{
    use LogErrors, ApiResponseTrait;

    public function index(CityRequest $request): JsonResponse
    {
        try {
            return $this->responseWithoutData(
                CityResource::collection(City::byState($request->getStateId())->get()),
                Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            $this->printLog($th);
            return $this->responseErrorByException($th);
        }
    }
}
