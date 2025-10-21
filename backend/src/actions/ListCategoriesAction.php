<?php
declare(strict_types=1);

namespace App\actions;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListCategoriesAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $pdo = $GLOBALS['db']->getConnection();
            $stmt = $pdo->query("SELECT id, name, description FROM categories ORDER BY name");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $items = array_map(function ($row) {
                return [
                    'id' => (int)$row['id'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    '_links' => [
                        'self' => ['href' => '/api/categories/' . $row['id']],
                        'outils' => ['href' => '/api/outils?category_id=' . $row['id']],
                    ],
                ];
            }, $rows);

            $payload = [
                'count' => count($items),
                'items' => $items,
                '_links' => ['self' => ['href' => '/api/categories']],
            ];

            $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
    }
}
