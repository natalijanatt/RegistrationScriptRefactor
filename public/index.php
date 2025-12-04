<?php

declare(strict_types=1);

use App\Container;
use App\Http\Application;

require_once __DIR__ . '/../bootstrap.php';

/** @var Container $container */
$container = require __DIR__ . '/../config/register.php';

/** @var Application $app */
$app = $container->get(Application::class);

// Register routes and run
$registerRoutes = require __DIR__ . '/../config/routes.php';

$app->routes($registerRoutes)->run();
