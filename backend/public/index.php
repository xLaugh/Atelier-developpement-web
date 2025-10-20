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
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// === Routes API ===
(require __DIR__ . '/../src/routes/outils.php')($app, $db);

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
    $path = $request->getUri()->getPath();
    if (str_starts_with($path, '/api/')) {
        return $next($request, $response);
    }
    return $response;
});

// === Run ===
$app->run();
