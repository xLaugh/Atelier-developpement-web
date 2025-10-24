<?php
declare(strict_types=1);

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\ports\api\ServiceOutilInterface;

class UpdateOutilAction
{
    public function __construct(
        private ServiceOutilInterface $outilService
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $id = $request->getAttribute('id');
            $name = $data['name'] ?? '';
            $description = $data['description'] ?? '';
            $category_id = $data['category_id'] ?? '';
            $model_id = $data['model_id'] ?? '';

            if (empty($name) || empty($description) || empty($category_id) || empty($model_id)) {
                $response->getBody()->write(json_encode([
                    'error' => 'missing_fields',
                    'message' => 'Tous les champs sont requis'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $outil = $this->outilService->update($id, $name, $description, (int)$category_id, (int)$model_id);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Outil mis à jour avec succès',
                'outil' => $outil->toArray()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'server_error',
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
    }
}
