<?php

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\ports\api\ServiceOutilInterface;

class CreateOutilAction
{
    public function __construct(
        private ServiceOutilInterface $outilService
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            
            $requiredFields = ['name', 'description', 'category_id', 'model_id'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => "Le champ $field est requis"
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }

            $outil = new Outil(
                id: 0,
                categoryId: (int)$data['category_id'],
                name: $data['name'],
                brand: '',
                imageUrl: '',
                pricePerDay: null,
                description: $data['description'],
                createdAt: new \DateTimeImmutable()
            );

            $createdOutil = $this->outilService->create($outil);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Outil créé avec succès',
                'outil' => $createdOutil->toArray()
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'outil: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
