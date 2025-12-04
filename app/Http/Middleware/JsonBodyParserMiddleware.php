<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Contracts\MiddlewareInterface;
use App\Http\Contracts\ViewInterface;
use App\Http\Request\Request;

class JsonBodyParserMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): ViewInterface
    {
        if (($request->headers()['content-type'] ?? '') === 'application/json') {
            $parsed = json_decode($request->rawBody(), true);
            $request = new Request(
                method: $request->method(),
                uri: $request->uri(),
                queryParams: $request->query(),
                bodyParams: $parsed ?? [],
                headers: $request->headers(),
                cookies: $request->cookies(),
                files: $request->files(),
                rawBody: $request->rawBody(),
                clientIp: $request->clientIp(),
                user: $request->user(),
                attributes: $request->getAttributes(),
            );
        }

        return $next($request);
    }
}