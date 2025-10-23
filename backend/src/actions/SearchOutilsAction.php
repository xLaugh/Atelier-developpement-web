<?php
declare(strict_types=1);

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\ports\api\ServiceOutilInterface;

class SearchOutilsAction
{
    public function __construct(
        private ServiceOutilInterface $serviceOutil
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $search = $queryParams['q'] ?? '';
            $page = (int)($queryParams['page'] ?? 1);
            $limit = (int)($queryParams['limit'] ?? 48);
            
            if (empty($search)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Paramètre de recherche requis'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $outils = $this->serviceOutil->searchOutils($search, $page, $limit);
            $total = $this->serviceOutil->countSearchOutils($search);

            $items = array_map(function($outil) {
                return $outil->toArray();
            }, $outils);

            $payload = [
                'success' => true,
                'items' => $items,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                ]
            ];

            $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Erreur lors de la recherche: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
