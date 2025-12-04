<?php

declare(strict_types=1);

namespace App\Http\Contracts;

use Throwable;

interface ExceptionHandlerInterface
{
    /**
     * Handle an uncaught exception and return an appropriate view.
     */
    public function handle(Throwable $exception): ViewInterface;

    /**
     * Report/log the exception.
     */
    public function report(Throwable $exception): void;
}

