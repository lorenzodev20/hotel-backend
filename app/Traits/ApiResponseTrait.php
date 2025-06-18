<?php

declare(strict_types=1);

namespace App\Traits;

use Exception;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{

    private $attributeSuccess = 'success';
    private $attributeError = 'error';

    private $attributeCode = 'code';

    private $attributeMessage = 'message';

    private $data = 'data';
    private $type = 'type';

    public function responseWithData(array|string $data, int $code): JsonResponse
    {
        return response()->json([
            $this->data => $data,
        ], $code);
    }

    public function responseWithoutData($data, int $code): JsonResponse
    {
        return response()->json(
            $data,
            $code
        );
    }

    public function sendErrorMessage($textMessage = 'No message', $httpCode = Response::HTTP_BAD_REQUEST)
    {
        return response()->json([$this->attributeMessage => $textMessage], $httpCode);
    }

    public function responseErrorByException(Exception|Throwable $exception, int $code = Response::HTTP_FORBIDDEN): JsonResponse
    {
        $message = 'En este momento no puede procesarse su solicitud, intente de nuevo más tarde.';
        if (!empty($exception->getMessage())) {
            $message = $exception->getMessage();
        }
        $code = $code === 0 || is_null($code) ? Response::HTTP_FORBIDDEN : $code;

        Log::error("Fail - Message: {$exception->getMessage()}, File: {$exception->getFile()}, Line: {$exception->getLine()}");

        return response()->json([
            $this->attributeMessage => $message
        ], $code === 0 ? Response::HTTP_FORBIDDEN : $code);
    }
}
