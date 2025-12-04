<?php

declare(strict_types=1);

namespace App\Http;

use App\Domain\Logging\Logger;
use App\Exceptions\Route\RouteNotFoundException;
use App\Http\Contracts\ExceptionHandlerInterface;
use App\Http\Contracts\ViewInterface;
use App\Http\View\NotFoundView;
use App\Http\View\ServerErrorView;
use Throwable;

/**
 * Handles uncaught exceptions by converting them to appropriate HTTP responses.
 * 
 * Maps known exception types to specific error views and logs all exceptions.
 */
class ExceptionHandler implements ExceptionHandlerInterface
{
    public function __construct(
        private readonly Logger $logger
    ) {}

    public function handle(Throwable $exception): ViewInterface
    {
        $this->report($exception);

        return $this->render($exception);
    }

    public function report(Throwable $exception): void
    {
        $this->logger->error(
            sprintf(
                '%s in %s:%d',
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            ),
            ['exception' => $exception]
        );
    }

    /**
     * Convert the exception to an appropriate view response.
     */
    private function render(Throwable $exception): ViewInterface
    {
        return match (true) {
            $exception instanceof RouteNotFoundException => new NotFoundView(
                'The page you are looking for does not exist.'
            ),
            default => new ServerErrorView(
                'An unexpected error occurred. Please try again later.'
            ),
        };
    }
}

