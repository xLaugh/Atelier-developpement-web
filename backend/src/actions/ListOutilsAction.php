<?php
declare(strict_types=1);

namespace App\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListOutilsAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $pdo = $GLOBALS['db']->getConnection();

            $queryParams = $request->getQueryParams();
            $categoryId = isset($queryParams['category_id']) ? (int)$queryParams['category_id'] : null;

            if ($categoryId !== null && $categoryId <= 0) {
                $response->getBody()->write(json_encode([
                    'error' => 'invalid_category_id',
                    'message' => 'Paramètre category_id invalide'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if ($categoryId) {
                $stmt = $pdo->prepare(
                    "SELECT m.id, m.name, m.brand, m.image_url, m.price_per_day, m.description, c.name AS category, COUNT(i.id) AS exemplaires
                     FROM models m
                     JOIN categories c ON c.id = m.category_id
                     LEFT JOIN items i ON i.model_id = m.id
                     WHERE c.id = :cid
                     GROUP BY m.id"
                );
                $stmt->execute(['cid' => $categoryId]);
            } else {
                $stmt = $pdo->query(
                    "SELECT m.id, m.name, m.brand, m.image_url, m.price_per_day, m.description, c.name AS category, COUNT(i.id) AS exemplaires
                     FROM models m
                     JOIN categories c ON c.id = m.category_id
                     LEFT JOIN items i ON i.model_id = m.id
                     GROUP BY m.id"
                );
            }

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $items = array_map(function ($row) {
                return [
                    'id' => (int)$row['id'],
                    'name' => $row['name'],
                    'brand' => $row['brand'],
                    'image_url' => $row['image_url'],
                    'price_per_day' => $row['price_per_day'] ? (float)$row['price_per_day'] : null,
                    'description' => $row['description'],
                    'category' => $row['category'],
                    'exemplaires' => (int)$row['exemplaires'],
                    '_links' => [
                        'self' => ['href' => '/api/outils/' . $row['id']],
                    ],
                ];
            }, $rows);

            $payload = [
                'count' => count($items),
                'items' => $items,
                '_links' => ['self' => ['href' => '/api/outils']],
            ];

            $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
    }
}
