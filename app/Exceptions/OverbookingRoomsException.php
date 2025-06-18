<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OverbookingRoomsException extends Exception
{
    use ApiResponseTrait;

    public function __construct(
        string $message = 'Capacidad del hotel excedida, por favor verifique la disponibilidad del hotel',
        int $code = Response::HTTP_BAD_REQUEST,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function render($request): JsonResponse
    {
        return $this->sendErrorMessage(
            $this->message,
            $this->code
        );
    }
}
