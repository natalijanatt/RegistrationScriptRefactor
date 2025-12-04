<?php

declare(strict_types=1);

namespace App\Http\Request;

use App\Domain\User\User;

class Request
{
    /**
     * @param array<string, mixed> $attributes Custom attributes (validated DTOs, etc.)
     */
    public function __construct(
        private readonly string $method,
        private readonly string $uri,
        private readonly array  $queryParams = [],
        private readonly array  $bodyParams = [],
        private readonly array  $headers = [],
        private readonly array  $cookies = [],
        private readonly array  $files = [],
        private readonly string $rawBody = '',
        private readonly string $clientIp = '0.0.0.0',
        private readonly ?User  $user = null,
        private readonly array  $attributes = [],
    ) {
    }

    public function method(): string
    {
        return $this->method;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function query(): array
    {
        return $this->queryParams;
    }

    public function body(): array
    {
        return $this->bodyParams;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function cookies(): array
    {
        return $this->cookies;
    }

    public function files(): array
    {
        return $this->files;
    }

    public function rawBody(): string
    {
        return $this->rawBody;
    }

    public function clientIp(): string
    {
        return $this->clientIp;
    }

    public function user(): ?User
    {
        return $this->user;
    }

    /**
     * Get a custom attribute value.
     */
    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * Get all custom attributes.
     * 
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Returns a new Request with the given attribute added.
     */
    public function withAttribute(string $name, mixed $value): static
    {
        $attributes = $this->attributes;
        $attributes[$name] = $value;

        return new static(
            $this->method,
            $this->uri,
            $this->queryParams,
            $this->bodyParams,
            $this->headers,
            $this->cookies,
            $this->files,
            $this->rawBody,
            $this->clientIp,
            $this->user,
            $attributes,
        );
    }

    /**
     * Returns a new Request with the given user attached.
     */
    public function withUser(User $user): static
    {
        return new static(
            $this->method,
            $this->uri,
            $this->queryParams,
            $this->bodyParams,
            $this->headers,
            $this->cookies,
            $this->files,
            $this->rawBody,
            $this->clientIp,
            $user,
            $this->attributes,
        );
    }
}