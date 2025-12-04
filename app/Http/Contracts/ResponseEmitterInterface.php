<?php

declare(strict_types=1);

namespace App\Http\Contracts;

interface ResponseEmitterInterface
{
    /**
     * Emit the response to the client.
     */
    public function emit(ViewInterface $view): void;
}

