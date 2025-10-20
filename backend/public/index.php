<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$settings = require __DIR__ . '/../src/config/Settings.php';
require __DIR__ . '/../src/db/Database.php';

$db = new Database($settings['db']);

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

require __DIR__ . '/../src/routes/outils.php';

$app->run();
