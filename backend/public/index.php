<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use App\middleware\CorsMiddleware;
use App\db\Database; 

// === CONFIG & DATABASE ===
$settings = require __DIR__ . '/../src/config/Settings.php';

$db = new Database($settings['db']);
$GLOBALS['db'] = $db;

// === CRÉATION DE L’APP ===
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
