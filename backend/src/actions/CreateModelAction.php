<?php

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\services\ServiceModel;

class CreateModelAction
{
    public function __construct(
        private ServiceModel $modelService
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            
            if (!isset($data['name']) || empty($data['name'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Le nom du modèle est requis'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (!isset($data['category_id']) || empty($data['category_id'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'La catégorie est requise'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $model = new Model();
            $model->setCategoryId((int)$data['category_id']);
            $model->setName($data['name']);
            $model->setImageUrl($data['image_url'] ?? null);

            $createdModel = $this->modelService->create($model);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Modèle créé avec succès',
                'model' => $createdModel->toArray()
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Erreur lors de la création du modèle: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
