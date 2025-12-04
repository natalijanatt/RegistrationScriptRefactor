<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Contracts\ViewInterface;
use App\Http\View;

class DashboardController
{
    public function __construct(private readonly View $view)
    {
    }
    
    public function show(): ViewInterface
    {
        $content = $this->view->make('dashboard')->render();
        return $this->view->make('layout', [
            'title' => 'Dashboard',
            'content' => $content
        ]);
    }
}

