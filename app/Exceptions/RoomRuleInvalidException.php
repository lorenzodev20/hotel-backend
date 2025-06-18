<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RoomRuleInvalidException extends Exception
{
    use ApiResponseTrait;

    public function __construct(
        string $message = 'Configuración de habitaciones no permitida',
        int $code = Response::HTTP_FORBIDDEN,
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
