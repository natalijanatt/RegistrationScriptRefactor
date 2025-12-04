<?php

declare(strict_types=1);

namespace App\Http;

use App\Http\Contracts\ViewInterface;
use RuntimeException;

class View implements ViewInterface
{
    private string $viewsPath;
    private string $template = '';
    private array $data = [];
    private int $statusCode = 200;

    public function __construct(string $viewsPath = __DIR__ . '/../../views')
    {
        $this->viewsPath = $viewsPath;
    }

    public function make(string $template, array $data = [], int $statusCode = 200): self
    {
        $view = new self($this->viewsPath);
        $view->template = $template;
        $view->data = $data;
        $view->statusCode = $statusCode;
        return $view;
    }

    public function render(): string
    {
        if ($this->template === '') {
            return '';
        }

        $viewFile = $this->viewsPath . '/' . $this->template . '.php';

        if (!file_exists($viewFile)) {
            throw new RuntimeException("View file not found: {$viewFile}");
        }

        ob_start();

        (function (string $__file, array $data): void {
            include $__file;
        })($viewFile, $this->data);

        return ob_get_clean();
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return [
            'Content-Type' => 'text/html; charset=UTF-8',
        ];
    }
}
