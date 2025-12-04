<?php

declare(strict_types=1);

namespace App\Exceptions\Route;

use Exception;

class RouteNotFoundException extends Exception
{
    public function __construct(string $message = "Route not found", int $code = 404, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

