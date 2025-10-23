<?php

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\ports\api\ServiceCategoryInterface;

class CreateCategoryAction
{
    public function __construct(
        private ServiceCategoryInterface $categoryService
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            
            if (!isset($data['name']) || empty($data['name'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Le nom de la catégorie est requis'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $createdCategory = $this->categoryService->creerCategorie($data['name']);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Catégorie créée avec succès',
                'category' => $createdCategory->toArray()
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Erreur lors de la création de la catégorie: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
