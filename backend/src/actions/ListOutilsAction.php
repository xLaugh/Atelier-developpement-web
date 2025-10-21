<?php
declare(strict_types=1);

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\ports\api\ServiceOutilInterface;

class ListOutilsAction
{
    public function __construct(
        private ServiceOutilInterface $serviceOutil
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $categoryId = isset($queryParams['category_id']) ? (int)$queryParams['category_id'] : null;

            if ($categoryId !== null && $categoryId <= 0) {
                $response->getBody()->write(json_encode([
                    'error' => 'invalid_category_id',
                    'message' => 'Paramètre category_id invalide'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            if ($categoryId) {
                $outils = $this->serviceOutil->listerOutilsParCategorie($categoryId);
            } else {
                $outils = $this->serviceOutil->listerOutils();
            }

            $items = array_map(function ($outil) {
                return $outil->toArray();
            }, $outils);

            $payload = [
                'count' => count($items),
                'items' => $items,
                '_links' => ['self' => ['href' => '/api/outils']],
            ];

            $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'server_error',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
    }
}
