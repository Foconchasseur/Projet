<?php
declare(strict_types=1);

use App\Application\Database;
use App\Application\ErrorCodes;
use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\Middleware\SessionMiddleware;
use App\Application\ResponseEmitter\ResponseEmitter;
use App\Application\Settings\SettingsInterface;
use DebugBar\Bridge\Twig\TimeableTwigExtensionProfiler;
use DebugBar\Bridge\TwigProfileCollector;
use DebugBar\StandardDebugBar;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Extension\DebugExtension;
use Twig\Profiler\Profile;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

if (false) { // Should be set to true in production
	$containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
}

// Set up settings
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

// Set up repositories
$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);

$containerBuilder->addDefinitions([
    'host' => '192.168.44.254'
])

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
try {
    Database::getInstance($container);
} catch (Exception $e) {
    $container->get(LoggerInterface::class)->error("Error initializing Database singleton: ".$e->getMessage());
}

session_start();

$app = AppFactory::create();

$container->set('view', function() {
    $twig = Twig::create(__DIR__ . '/../src/Application/Views/');

    $environment = $twig->getEnvironment();
    $environment->addGlobal("session", $_SESSION);
    $environment->addGlobal("ErrorCodes", ErrorCodes::class);

    return $twig;
});

/** @var Twig $twig */
$twig = $container->get('view');

$environment = $twig->getEnvironment();
$environment->addGlobal("session", $_SESSION);

if ($container->get(SettingsInterface::class)->get('debug_bar')) {
    $debugbar = new StandardDebugBar();
    $debugbar_renderer = $debugbar->getJavascriptRenderer();
    $debugbar_renderer->dumpJsAssets(__DIR__ . '/debugbar.js');
    $debugbar_renderer->dumpCssAssets(__DIR__ . '/debugbar.css');
    $debugbar_renderer->setIncludeVendors(false);
    $environment->addGlobal("debugbar_head", $debugbar_renderer->renderHead());
    $environment->addGlobal("debugbar_body", $debugbar_renderer->render());
}

// Create Twig
$app->add(TwigMiddleware::createFromContainer($app));

$callableResolver = $app->getCallableResolver();

// Register middleware
$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

// Register routes
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

/** @var SettingsInterface $settings */
$settings = $container->get(SettingsInterface::class);

Database::getInstance($container);

$displayErrorDetails = $settings->get('displayErrorDetails');
$logError = $settings->get('logError');
$logErrorDetails = $settings->get('logErrorDetails');

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Create Error Handler
$responseFactory = $app->getResponseFactory();
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

// Create Shutdown Handler
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Body Parsing Middleware
$app->addBodyParsingMiddleware();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logError, $logErrorDetails);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Flash Middleware
$app->add(new SessionMiddleware());

// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
