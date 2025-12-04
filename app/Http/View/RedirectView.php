<?php

declare(strict_types=1);

namespace App\Http\View;

use App\Http\Contracts\ViewInterface;

/**
 * HTTP redirect response view.
 * 
 * Returns a 302 redirect to the specified location.
 */
class RedirectView implements ViewInterface
{
    public function __construct(
        private readonly string $location,
        private readonly int $statusCode = 302
    ) {}

    public function render(): string
    {
        return '';
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return [
            'Location' => $this->location,
        ];
    }
}
