<?php

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\services\ServiceModel;

class ListModelsAction
{
    public function __construct(
        private ServiceModel $modelService
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $models = $this->modelService->findAll();

            $items = array_map(function ($model) {
                if (is_object($model) && method_exists($model, 'toArray')) {
                    return $model->toArray();
                }
                return $model;
            }, $models);

            $response->getBody()->write(json_encode([
                'success' => true,
                'items' => $items
            ]));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Erreur lors du chargement des modèles: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
