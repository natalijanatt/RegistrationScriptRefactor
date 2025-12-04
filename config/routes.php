<?php

declare(strict_types=1);

use App\Http\Controller\DashboardController;
use App\Http\Controller\RegisterController;
use App\Http\Controller\RegistrationPageController;
use App\Http\Middleware\AuthenticationMiddleware;
use App\Http\Middleware\CsrfMiddleware;
use App\Http\Middleware\FraudCheckMiddleware;
use App\Http\Middleware\RegistrationValidationMiddleware;
use App\Http\Router;
use App\Http\View\RedirectView;
use App\Http\Contracts\ViewInterface;

return static function (Router $router): void {
    // Public routes
    $router->get('/', static function (): ViewInterface {
        return new RedirectView('/registration');
    });

    $router->get('/registration', [RegistrationPageController::class, 'show']);

    // Registration with validation, CSRF, and fraud check middleware
    // Middleware executes in order: CSRF → Validation → FraudCheck → Controller
    $router->post('/register', [RegisterController::class, 'handle'], [
        CsrfMiddleware::class,
        RegistrationValidationMiddleware::class,
        FraudCheckMiddleware::class,
    ]);

    // Authenticated routes
    $router->get('/dashboard', [DashboardController::class, 'show'], [AuthenticationMiddleware::class]);
};
