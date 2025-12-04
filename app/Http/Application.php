<?php

declare(strict_types=1);

namespace App\Http;

use App\Http\Contracts\ExceptionHandlerInterface;
use App\Http\Contracts\ResponseEmitterInterface;
use App\Http\Contracts\ViewInterface;
use App\Http\Request\RequestFactory;
use Throwable;

/**
 * The Application kernel - entry point for HTTP request handling.
 * 
 * Orchestrates the request/response cycle with proper error handling:
 * - Routes incoming requests to appropriate handlers
 * - Catches and handles any exceptions that occur
 * - Emits the final response to the client
 * 
 * This class follows the clean architecture by depending only on interfaces
 * and delegating concerns to specialized components.
 */
class Application
{
    public function __construct(
        private readonly Router $router,
        private readonly RequestFactory $requestFactory,
        private readonly ExceptionHandlerInterface $exceptionHandler,
        private readonly ResponseEmitterInterface $responseEmitter
    ) {}

    /**
     * Run the application.
     * 
     * This is the main entry point that handles the full request/response lifecycle.
     */
    public function run(): void
    {
        $view = $this->handle();
        $this->responseEmitter->emit($view);
    }

    /**
     * Handle the incoming request and return a view.
     * 
     * Separated from run() to allow testing the handling logic
     * without emitting the response.
     */
    public function handle(): ViewInterface
    {
        try {
            $request = $this->requestFactory->fromGlobals();
            return $this->router->resolve($request);
        } catch (Throwable $e) {
            return $this->exceptionHandler->handle($e);
        }
    }

    /**
     * Register routes with the application's router.
     * 
     * @param callable(Router): void $callback
     */
    public function routes(callable $callback): self
    {
        $callback($this->router);
        return $this;
    }
}

