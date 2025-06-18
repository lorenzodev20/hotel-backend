<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait LogErrors
{
    public function printLog($exception): void
    {
        Log::error("Fail - Message: {$exception->getMessage()}, File: {$exception->getFile()}, Line: {$exception->getLine()}");
    }
}
