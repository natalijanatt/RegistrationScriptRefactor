<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Contracts\ViewInterface;
use App\Http\Security\CsrfTokenManager;
use App\Http\View;

class RegistrationPageController
{
    public function __construct(
        private readonly View $view,
        private readonly CsrfTokenManager $csrfManager
    ) {}
    
    public function show(): ViewInterface
    {
        $content = $this->view->make('registration', [
            'csrfToken' => $this->csrfManager->getToken()
        ])->render();
        
        return $this->view->make('layout', [
            'title' => 'Register - Create Your Account',
            'content' => $content
        ]);
    }
}
