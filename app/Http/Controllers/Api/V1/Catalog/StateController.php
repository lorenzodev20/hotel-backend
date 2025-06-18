<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Models\State;
use App\Traits\LogErrors;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Api\V1\Catalogs\StateRequest;
use App\Http\Resources\Api\V1\Catalogs\StateResource;

class StateController extends Controller
{
    use LogErrors, ApiResponseTrait;

    public function index(StateRequest $request): JsonResponse
    {
        try {
            return $this->responseWithoutData(
                StateResource::collection(State::byCountry($request->getCountryId())->get()),
                Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            $this->printLog($th);
            return $this->responseErrorByException($th);
        }
    }
}
