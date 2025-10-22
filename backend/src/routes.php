<?php
declare(strict_types=1);

use App\actions\HealthAction;
use App\actions\ListCategoriesAction;
use App\actions\ListOutilsAction;
use App\actions\GetOutilAction;
use App\actions\AuthLoginAction;
use App\actions\AuthRegisterAction;
use App\actions\AuthMeAction;
use App\actions\CreateReservationAction;
use App\actions\CreatePeriodReservationAction;
use App\actions\CreateCategoryAction;
use App\actions\CreateModelAction;
use App\actions\CreateOutilAction;
use App\actions\ListModelsAction;

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->get('/api/health', HealthAction::class)->setName('health');
$app->get('/api/categories', ListCategoriesAction::class)->setName('list_categories');
$app->get('/api/outils', ListOutilsAction::class)->setName('list_outils');
$app->get('/api/outils/{id}', GetOutilAction::class)->setName('get_outil');
$app->post('/api/auth/login', AuthLoginAction::class)->setName('auth_login');
$app->post('/api/auth/register', AuthRegisterAction::class)->setName('auth_register');
$app->get('/api/auth/me', AuthMeAction::class)->setName('auth_me');
$app->post('/api/reservations', CreateReservationAction::class)->setName('create_reservation');
$app->post('/api/reservations/period', CreatePeriodReservationAction::class)->setName('create_period_reservation');

$app->get('/api/models', ListModelsAction::class)->setName('list_models');
$app->post('/api/admin/categories', CreateCategoryAction::class)->setName('create_category');
$app->post('/api/admin/models', CreateModelAction::class)->setName('create_model');
$app->post('/api/admin/outils', CreateOutilAction::class)->setName('create_outil');
