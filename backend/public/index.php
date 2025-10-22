<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use App\middleware\CorsMiddleware;
use DI\ContainerBuilder;
use Dotenv\Dotenv;

// === ENV ===
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->safeLoad();
}

// === CONFIGURATION ===
$settings = require __DIR__ . '/../src/config/settings.php';

// === CONTAINER DI ===
$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);
$containerBuilder->addDefinitions(['settings' => $settings]);
$containerBuilder->addDefinitions(require __DIR__ . '/../src/config/services.php');

$container = $containerBuilder->build();

// === CRÉATION DE L'APP ===
AppFactory::setContainer($container);
$app = AppFactory::create();

// === AJOUT DU MIDDLEWARE CORS ===
$app->add(new CorsMiddleware());

// === Autres middlewares Slim ===
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// === ROUTES ===
require __DIR__ . '/../src/routes.php';

// === LANCEMENT ===
$app->run();
