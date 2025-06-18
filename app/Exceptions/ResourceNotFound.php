<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResourceNotFound extends Exception
{
    use ApiResponseTrait;

    public function __construct(
        string $message = 'Recurso no encontrado',
        int $code = Response::HTTP_NOT_FOUND,
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
