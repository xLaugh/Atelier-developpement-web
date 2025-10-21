<?php
declare(strict_types=1);

use Slim\App;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

<<<<<<< HEAD
use App\actions\HealthAction;
use App\actions\ListCategoriesAction;
use App\actions\ListOutilsAction;
use App\actions\GetOutilAction;
use App\actions\AuthLoginAction;
use App\actions\AuthRegisterAction;
use App\actions\AuthMeAction;

=======
>>>>>>> a7b62f9192b9f3d2faf0fd595221cf4c5e8aa7e6
$app->get('/api/health', HealthAction::class)->setName('health');

$app->get('/api/categories', ListCategoriesAction::class)->setName('list_categories');

$app->get('/api/outils', ListOutilsAction::class)->setName('list_outils');

$app->get('/api/outils/{id}', GetOutilAction::class)->setName('get_outil');

$app->post('/api/auth/login', AuthLoginAction::class)->setName('auth_login');

$app->post('/api/auth/register', AuthRegisterAction::class)->setName('auth_register');

$app->get('/api/auth/me', AuthMeAction::class)->setName('auth_me');
