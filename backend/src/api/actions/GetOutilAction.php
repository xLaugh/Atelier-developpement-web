<?php
declare(strict_types=1);

namespace App\api\actions;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetOutilAction
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            if ($id <= 0) {
                $response->getBody()->write(json_encode([
                    'error' => 'invalid_id',
                    'message' => 'Identifiant invalide'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $pdo = $GLOBALS['db']->getConnection();
            $stmt = $pdo->prepare(
                "SELECT m.id, m.name, m.brand, m.image_url, m.price_per_day, m.description, c.name AS category, COUNT(i.id) AS exemplaires
                 FROM models m
                 JOIN categories c ON c.id = m.category_id
                 LEFT JOIN items i ON i.model_id = m.id
                 WHERE m.id = :id
                 GROUP BY m.id"
            );
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                $response->getBody()->write(json_encode([
                    'error' => 'not_found',
                    'message' => 'Outil introuvable'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $item = [
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
                    'collection' => ['href' => '/api/outils'],
                ],
            ];

            $response->getBody()->write(json_encode($item, JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
