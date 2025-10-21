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

<<<<<<< HEAD
// === AJOUT DU MIDDLEWARE CORS ===
$app->add(new CorsMiddleware());
=======
// === Middleware CORS ===
$app->add(function ($request, $handler) {
    if ($request->getMethod() === 'OPTIONS') {
        $response = new \Slim\Psr7\Response(200);
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Access-Control-Max-Age', '86400')
            ->withHeader('Content-Length', '0');
    }

    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->withHeader('Vary', 'Origin');
});
>>>>>>> a7b62f9192b9f3d2faf0fd595221cf4c5e8aa7e6

// === Autres middlewares Slim ===
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

<<<<<<< HEAD
// === ROUTES ===
=======
// === Routes API ===
// Inclure toutes les classes Action
require __DIR__ . '/../src/actions/HealthAction.php';
require __DIR__ . '/../src/actions/ListCategoriesAction.php';
require __DIR__ . '/../src/actions/ListOutilsAction.php';
require __DIR__ . '/../src/actions/GetOutilAction.php';
require __DIR__ . '/../src/actions/AuthLoginAction.php';
require __DIR__ . '/../src/actions/AuthRegisterAction.php';
require __DIR__ . '/../src/actions/AuthMeAction.php';

>>>>>>> a7b62f9192b9f3d2faf0fd595221cf4c5e8aa7e6
require __DIR__ . '/../src/routes.php';

// === LANCEMENT ===
$app->run();
