<?php

declare(strict_types=1);

namespace App\Http\View;

use App\Http\Contracts\ViewInterface;
use JsonException;

/**
 * JSON response view.
 * 
 * Provides JSON-encoded data with appropriate content type header.
 */
class JsonView implements ViewInterface
{
    public function __construct(
        private readonly array $data,
        private readonly int $statusCode = 200
    ) {}

    /**
     * @throws JsonException
     */
    public function render(): string
    {
        return json_encode($this->data, JSON_THROW_ON_ERROR);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }
}


