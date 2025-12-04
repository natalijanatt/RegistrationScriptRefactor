<?php

declare(strict_types=1);

namespace App\Http;

use App\Exceptions\Container\ContainerException;
use App\Exceptions\Container\NotFoundException;
use App\Exceptions\Route\RouteNotFoundException;
use App\Http\Contracts\MiddlewareInterface;
use App\Http\Contracts\ViewInterface;
use App\Http\Request\Request;
use Psr\Container\ContainerInterface;


class Router
{
    /**
     * @var array<string, array<string, array{action: callable|array, middlewares: string[]}>>
     *
     */
    private array $routes = [];

    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function register(
        string $requestMethod,
        string $path,
        callable|array $action,
        array $middlewares = []
    ): self {
        $requestMethod = strtolower($requestMethod);

        $this->routes[$requestMethod][$path] = [
            'action'      => $action,
            'middlewares' => $middlewares,
        ];

        return $this;
    }

    public function get(
        string $path,
        callable|array $action,
        array $middlewares = []
    ): self {
        return $this->register('get', $path, $action, $middlewares);
    }

    public function post(
        string $path,
        callable|array $action,
        array $middlewares = []
    ): self {
        return $this->register('post', $path, $action, $middlewares);
    }

    /**
     * @throws RouteNotFoundException
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function resolve(Request $request): ViewInterface
    {

        $route = $this->getRoute($request->uri());

        $routeConfig = $this->getValidRouteConfig(
            strtolower($request->method()),
            $route
        );

        $action      = $routeConfig['action'];
        $middlewares = $routeConfig['middlewares'] ?? [];

        // Final handler = controller/callback
        $controllerHandler = $this->createControllerHandler($action);

        // Wrap with middlewares (pipeline)
        $pipeline = $this->buildMiddlewarePipeline($middlewares, $controllerHandler);

        // Execute pipeline
        return $pipeline($request);
    }

    private function getRoute(string $requestUri): string
    {
        return explode('?', $requestUri)[0];
    }

    /**
     * @return array{action: callable|array, middlewares: string[]}
     * @throws RouteNotFoundException
     */
    private function getValidRouteConfig(string $requestMethod, string $route): array
    {
        $routeConfig = $this->routes[$requestMethod][$route] ?? null;

        if (!$routeConfig) {
            throw new RouteNotFoundException();
        }

        return $routeConfig;
    }

    /**
     * @param callable|array $action
     * @return callable(Request): ViewInterface
     *
     */
    private function createControllerHandler(callable|array $action): callable
    {
        return function (Request $request) use ($action): ViewInterface {
            // Simple callable (closure, function, invokable object)
            if (is_callable($action) && !is_array($action)) {
                return \call_user_func($action, $request);
            }

            // [ControllerClass::class, 'method']
            if (is_array($action) && count($action) === 2) {
                [$class, $method] = $action;

                if (!class_exists($class)) {
                    throw new RouteNotFoundException(sprintf('Controller "%s" not found.', $class));
                }

                $controller = $this->container->get($class);

                if (!\method_exists($controller, $method)) {
                    throw new RouteNotFoundException(sprintf(
                        'Method "%s" not found on controller "%s".',
                        $method,
                        $class
                    ));
                }

                return \call_user_func_array([$controller, $method], [$request]);
            }

            throw new RouteNotFoundException('Invalid route action configuration.');
        };
    }

    /**
     * @param string[] $middlewares  List of middleware class names
     * @param callable $last         Final handler (controller)
     * @return callable(Request): ViewInterface
     */
    private function buildMiddlewarePipeline(array $middlewares, callable $last): callable
    {
        $pipeline = $last;

        // Wrap from last to first
        foreach (\array_reverse($middlewares) as $middlewareClass) {
            $pipeline = function (Request $request) use ($middlewareClass, $pipeline): ViewInterface {
                $middleware = $this->container->get($middlewareClass);

                if (!$middleware instanceof MiddlewareInterface) {
                    throw new \RuntimeException(sprintf(
                        'Middleware "%s" must implement %s.',
                        $middlewareClass,
                        MiddlewareInterface::class
                    ));
                }

                return $middleware->handle($request, $pipeline);
            };
        }

        return $pipeline;
    }
}
