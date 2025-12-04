<?php

declare(strict_types = 1);

namespace App;

use App\Exceptions\Container\ContainerException;
use App\Exceptions\Container\NotFoundException;
use ReflectionClass;
use ReflectionException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * @var array<string, array{concrete: callable|string, singleton: bool}>
     */
    private array $entries = [];

    /**
     * @var array<string, object>
     */
    private array $instances = [];

    /**
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function get(string $id)
    {
        // If we already have a singleton instance, return it
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        // If the entry is registered in the container
        if ($this->has($id)) {
            $entry = $this->entries[$id];
            $concrete = $entry['concrete'];
            $isSingleton = $entry['singleton'];

            // Resolve the concrete
            if (is_callable($concrete)) {
                $object = $concrete($this);
            } else {
                // $concrete is assumed to be a class-string
                $object = $this->resolve($concrete);
            }

            // If it's a singleton, store the instance
            if ($isSingleton) {
                $this->instances[$id] = $object;
            }

            return $object;
        }

        // Fallback: autowire/resolve the class directly (not a singleton)
        return $this->resolve($id);
    }

    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    /**
     * Register an entry in the container.
     *
     * @param string          $id
     * @param callable|string $concrete  Callable that receives the container or a class-string
     * @param bool            $singleton If true, the resolved instance will be reused (singleton)
     */
    public function set(string $id, callable|string $concrete, bool $singleton = false): void
    {
        $this->entries[$id] = [
            'concrete'  => $concrete,
            'singleton' => $singleton,
        ];
    }

    /**
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function resolve(string $id)
    {
        // 1. Inspect the class that we are trying to get from the container
        try {
            $reflectionClass = new ReflectionClass($id);
        } catch (ReflectionException $e) {
            throw new NotFoundException($e->getMessage(), $e->getCode(), $e);
        }

        if (! $reflectionClass->isInstantiable()) {
            throw new ContainerException('Class "' . $id . '" is not instantiable');
        }

        // 2. Inspect the constructor of the class
        $constructor = $reflectionClass->getConstructor();

        if (! $constructor) {
            return new $id;
        }

        // 3. Inspect the constructor parameters (dependencies)
        $parameters = $constructor->getParameters();

        if (! $parameters) {
            return new $id;
        }

        // 4. If the constructor parameter is a class then try to resolve that class using the container
        $dependencies = array_map(
            function (\ReflectionParameter $param) use ($id) {
                $name = $param->getName();
                $type = $param->getType();

                if (! $type) {
                    throw new ContainerException(
                        'Failed to resolve class "' . $id . '" because param "' . $name . '" is missing a type hint'
                    );
                }

                if ($type instanceof \ReflectionUnionType) {
                    throw new ContainerException(
                        'Failed to resolve class "' . $id . '" because of union type for param "' . $name . '"'
                    );
                }

                if ($type instanceof \ReflectionNamedType) {
                    // Class (non-builtin) → resolve via container
                    if (! $type->isBuiltin()) {
                        return $this->get($type->getName());
                    }

                    // Builtin type (string, int, etc.)
                    // If there is a default value, use it
                    if ($param->isDefaultValueAvailable()) {
                        return $param->getDefaultValue();
                    }

                    // Optionally: if nullable, you could do:
                    // if ($type->allowsNull()) {
                    //     return null;
                    // }

                    throw new ContainerException(
                        'Failed to resolve class "' . $id . '" because invalid param "' . $name . '" (builtin without default)'
                    );
                }

                throw new ContainerException(
                    'Failed to resolve class "' . $id . '" because invalid param "' . $name . '"'
                );
            },
            $parameters

    );

        return $reflectionClass->newInstanceArgs($dependencies);
    }
}
