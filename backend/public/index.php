<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

// === Config & DB ===
$settings = require __DIR__ . '/../src/config/Settings.php';
require __DIR__ . '/../src/db/Database.php';
$db = new Database($settings['db']);

// === App Slim ===
$app = AppFactory::create();

$app->add(function ($request, $handler) {
    if ($request->getMethod() === 'OPTIONS') {
        $response = new \Slim\Psr7\Response(200);
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Access-Control-Max-Age', '86400');
    }

    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->withHeader('Vary', 'Origin');
});

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// === Routes API ===
(require __DIR__ . '/../src/routes/outils.php')($app, $db);
(require __DIR__ . '/../src/routes/auth.php')($app, $db);

// === Route pour servir le frontend ===
$app->get('/', function ($request, $response) {
    $frontendPath = __DIR__ . '/../../frontend/index.html';
    if (file_exists($frontendPath)) {
        return $response->withHeader('Content-Type', 'text/html')
                       ->write(file_get_contents($frontendPath));
    }
    return $response->withStatus(404)->write('Frontend not found');
});

// === Servir les fichiers statiques (CSS, JS) ===
$app->get('/{path:.*}', function ($request, $response, $args) {
    $path = $args['path'];
    $filePath = __DIR__ . '/../../frontend/' . $path;
    
    if (file_exists($filePath) && is_file($filePath)) {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $contentType = match($extension) {
            'css' => 'text/css',
            'js' => 'application/javascript',
            'html' => 'text/html',
            'json' => 'application/json',
            default => 'text/plain'
        };
        
        return $response->withHeader('Content-Type', $contentType)
                       ->write(file_get_contents($filePath));
    }
    
    return $response->withStatus(404)->write('File not found');
})->add(function ($request, $response, $next) {
    // Skip cette route si c'est une route API
    if (str_starts_with($path, '/api/')) {
        return $next($request, $response);
    }
    return $response;
});

$app->run();
