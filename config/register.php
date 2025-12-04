<?php

declare(strict_types=1);

use App\Application\RegisterUser\RegisterUser;
use App\Application\RegisterUser\RegisterUserValidator;
use App\Application\Validation\Rules\EmailFormatRule;
use App\Application\Validation\Rules\EmailRequiredRule;
use App\Application\Validation\Rules\PasswordLengthRule;
use App\Application\Validation\Rules\PasswordRequiredRule;
use App\Application\Validation\Rules\PasswordsMatchRule;
use App\Container;
use App\Domain\Config\ConfigInterface;
use App\Domain\Logging\Logger;
use App\Domain\Notification\EmailSender;
use App\Domain\Persistence\TransactionManager;
use App\Domain\Security\FraudCheckService;
use App\Domain\Security\PasswordHasher;
use App\Domain\User\UserRepository;
use App\Http\Application;
use App\Http\Contracts\ExceptionHandlerInterface;
use App\Http\Contracts\ResponseEmitterInterface;
use App\Http\Controller\DashboardController;
use App\Http\Controller\RegisterController;
use App\Http\Controller\RegistrationPageController;
use App\Http\ExceptionHandler;
use App\Http\Middleware\AuthenticationMiddleware;
use App\Http\Middleware\CsrfMiddleware;
use App\Http\Middleware\FraudCheckMiddleware;
use App\Http\Middleware\RegistrationValidationMiddleware;
use App\Http\Request\RequestFactory;
use App\Http\Response\ResponseEmitter;
use App\Http\Router;
use App\Http\Security\CsrfTokenManager;
use App\Http\Session\NativeSession;
use App\Http\Session\SessionInterface;
use App\Http\View;
use App\Infrastructure\Config\EnvConfig;
use App\Infrastructure\Database\DatabaseConnection;
use App\Infrastructure\Database\MysqliTransactionManager;
use App\Infrastructure\Logging\ErrorLogLogger;
use App\Infrastructure\Notification\SmtpEmailSender;
use App\Infrastructure\Persistence\MysqliUserRepository;
use App\Infrastructure\Security\BcryptPasswordHasher;
use App\Infrastructure\Security\SimulatedMaxMindService;
use App\QueryBuilder\Infrastructure\Builder\DefaultQueryBuilderFactory;
use App\QueryBuilder\Infrastructure\MySqli\MySqliConnection;
use Psr\Container\ContainerInterface;

// Domain interfaces

// Application services

// HTTP layer

// Infrastructure

$container = new Container();

/**
 * Configuration
 */
$container->set(ConfigInterface::class, static function () {
    return new EnvConfig($_ENV);
}, true);

/**
 * Session
 */
$container->set(SessionInterface::class, static function () {
    return new NativeSession();
}, true);

/**
 * Security - CSRF
 */
$container->set(CsrfTokenManager::class, static function (Container $c) {
    return new CsrfTokenManager(
        $c->get(SessionInterface::class)
    );
}, true);

$container->set(CsrfMiddleware::class, static function (Container $c) {
    return new CsrfMiddleware(
        $c->get(CsrfTokenManager::class)
    );
}, true);

/**
 * Security - Fraud Detection (MaxMind simulation)
 */
$container->set(FraudCheckService::class, static function () {
    return new SimulatedMaxMindService();
}, true);

$container->set(FraudCheckMiddleware::class, static function (Container $c) {
    return new FraudCheckMiddleware(
        $c->get(FraudCheckService::class),
        $c->get(Logger::class)
    );
}, true);

/**
 * Database connection
 */
$container->set(DatabaseConnection::class, static function (Container $c) {
    return DatabaseConnection::fromConfig(
        $c->get(ConfigInterface::class)
    );
}, true);

$container->set(\mysqli::class, static function (Container $c) {
    return $c->get(DatabaseConnection::class)->getConnection();
}, true);

/**
 * Query builder
 */
$container->set(MySqliConnection::class, static function (Container $c) {
    return new MySqliConnection($c->get(\mysqli::class));
}, true);

$container->set(DefaultQueryBuilderFactory::class, static function (Container $c) {
    return new DefaultQueryBuilderFactory(
        $c->get(MySqliConnection::class)
    );
}, true);

/**
 * Repositories
 */
$container->set(UserRepository::class, static function (Container $c) {
    return new MysqliUserRepository(
        $c->get(DefaultQueryBuilderFactory::class)
    );
}, true);

/**
 * Infrastructure services
 */
$container->set(PasswordHasher::class, static function () {
    return new BcryptPasswordHasher();
}, true);

$container->set(EmailSender::class, static function (Container $c) {
    return new SmtpEmailSender(
        $c->get(ConfigInterface::class)
    );
}, true);

$container->set(Logger::class, static function () {
    return new ErrorLogLogger();
}, true);

$container->set(TransactionManager::class, static function (Container $c) {
    return new MysqliTransactionManager(
        $c->get(\mysqli::class)
    );
}, true);

/**
 * Validators
 */
$container->set(RegisterUserValidator::class, static function () {
    return new RegisterUserValidator(
        new EmailRequiredRule(),
        new EmailFormatRule(),
        new PasswordRequiredRule(),
        new PasswordLengthRule(),
        new PasswordsMatchRule()
    );
}, true);

/**
 * Use Cases
 */
$container->set(RegisterUser::class, static function (Container $c) {
    return new RegisterUser(
        $c->get(UserRepository::class),
        $c->get(PasswordHasher::class),
        $c->get(EmailSender::class),
        $c->get(TransactionManager::class),
        $c->get(Logger::class),
    );
}, true);

/**
 * HTTP Layer
 */
$container->set(View::class, View::class);
$container->set(RequestFactory::class, RequestFactory::class, true);

$container->set(ResponseEmitterInterface::class, static function () {
    return new ResponseEmitter();
}, true);

$container->set(ExceptionHandlerInterface::class, static function (Container $c) {
    return new ExceptionHandler(
        $c->get(Logger::class)
    );
}, true);

/**
 * Middleware
 */
$container->set(AuthenticationMiddleware::class, static function (Container $c) {
    return new AuthenticationMiddleware(
        $c->get(UserRepository::class),
        $c->get(SessionInterface::class)
    );
}, true);

$container->set(RegistrationValidationMiddleware::class, static function (Container $c) {
    return new RegistrationValidationMiddleware(
        $c->get(RegisterUserValidator::class)
    );
}, true);

$container->set(Router::class, static function (ContainerInterface $c) {
    return new Router($c);
}, true);

$container->set(Application::class, static function (Container $c) {
    return new Application(
        $c->get(Router::class),
        $c->get(RequestFactory::class),
        $c->get(ExceptionHandlerInterface::class),
        $c->get(ResponseEmitterInterface::class)
    );
}, true);

/**
 * Controllers
 */
$container->set(RegistrationPageController::class, static function (Container $c) {
    return new RegistrationPageController(
        $c->get(View::class),
        $c->get(CsrfTokenManager::class)
    );
}, true);

$container->set(RegisterController::class, static function (Container $c) {
    return new RegisterController(
        $c->get(RegisterUser::class),
        $c->get(Logger::class),
        $c->get(SessionInterface::class)
    );
}, true);

$container->set(DashboardController::class, static function (Container $c) {
    return new DashboardController(
        $c->get(View::class)
    );
}, true);

return $container;
