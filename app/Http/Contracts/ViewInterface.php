<?php

declare(strict_types=1);

namespace App\Http\Contracts;

/**
 * Interface for HTTP response views.
 * 
 * Views are responsible for providing response content and metadata,
 * but NOT for sending headers or setting status codes (that's the HTTP layer's job).
 */
interface ViewInterface
{
    /**
     * Render the view content as a string.
     */
    public function render(): string;

    /**
     * Get the HTTP status code for this response.
     */
    public function getStatusCode(): int;

    /**
     * Get HTTP headers for this response.
     * 
     * @return array<string, string> Header name => value pairs
     */
    public function getHeaders(): array;
}
