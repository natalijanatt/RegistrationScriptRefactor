<?php

declare(strict_types=1);

namespace App\Http\Response;

use App\Http\Contracts\ResponseEmitterInterface;
use App\Http\Contracts\ViewInterface;


class ResponseEmitter implements ResponseEmitterInterface
{
    public function emit(ViewInterface $view): void
    {
        $this->emitStatusCode($view->getStatusCode());
        $this->emitHeaders($view->getHeaders());
        $this->emitSecurityHeaders();
        $this->emitBody($view->render());
    }
    private function emitSecurityHeaders(): void
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
    private function emitStatusCode(int $statusCode): void
    {
        http_response_code($statusCode);
    }

    /**
     * @param array<string, string> $headers
     */
    private function emitHeaders(array $headers): void
    {
        foreach ($headers as $name => $value) {
            header("{$name}: {$value}");
        }
    }

    private function emitBody(string $body): void
    {
        echo $body;
    }
}

