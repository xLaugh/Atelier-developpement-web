<?php
declare(strict_types=1);

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\ports\api\ServiceCategoryInterface;

class UpdateCategoryAction
{
    public function __construct(
        private ServiceCategoryInterface $categoryService
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $id = $request->getAttribute('id');
            $name = $data['name'] ?? '';

            if (empty($name)) {
                $response->getBody()->write(json_encode([
                    'error' => 'missing_fields',
                    'message' => 'Le nom est requis'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $category = $this->categoryService->update($id, $name);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Catégorie mise à jour avec succès',
                'category' => $category->toArray()
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
