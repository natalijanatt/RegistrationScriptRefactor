<?php

declare(strict_types=1);

namespace App\Http\Contracts;

use App\Http\Request\Request;

interface MiddlewareInterface
{
    /**
     * @param Request $request
     * @param callable $next Receives Request and must return ViewInterface
     */
    public function handle(Request $request, callable $next): ViewInterface;
}
