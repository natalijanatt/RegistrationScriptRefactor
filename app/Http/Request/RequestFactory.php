<?php
declare(strict_types=1);

namespace App\Http\Request;

class RequestFactory
{
    public function fromGlobals(): Request
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');

        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $normalized = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$normalized] = $value;
            }
        }

        $rawBody = file_get_contents('php://input');

        return new Request(
            method: strtolower($method),
            uri: $uri,
            queryParams: $_GET,
            bodyParams: $_POST,
            headers: $headers,
            cookies: $_COOKIE,
            files: $_FILES,
            rawBody: $rawBody,
            clientIp: $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
        );
    }
}