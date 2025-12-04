<?php

declare(strict_types=1);

namespace App\Http;

use App\Http\Contracts\ExceptionHandlerInterface;
use App\Http\Contracts\ResponseEmitterInterface;
use App\Http\Contracts\ViewInterface;
use App\Http\Request\RequestFactory;
use Throwable;

class Application
{
    public function __construct(
        private readonly Router $router,
        private readonly RequestFactory $requestFactory,
        private readonly ExceptionHandlerInterface $exceptionHandler,
        private readonly ResponseEmitterInterface $responseEmitter
    ) {}

    public function run(): void
    {
        $view = $this->handle();
        $this->responseEmitter->emit($view);
    }

    public function handle(): ViewInterface
    {
        try {
            $request = $this->requestFactory->fromGlobals();
            return $this->router->resolve($request);
        } catch (Throwable $e) {
            return $this->exceptionHandler->handle($e);
        }
    }

    public function routes(callable $callback): self
    {
        $callback($this->router);
        return $this;
    }
}

