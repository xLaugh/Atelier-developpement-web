<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app, $db) {
    $app->get('/api/health', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode(['status' => 'ok'], JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
    });

    $app->get('/api/categories', function (Request $request, Response $response) use ($db) {
        try {
            $pdo = $db->getConnection();
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
    });

    $app->get('/api/outils', function (Request $request, Response $response) use ($db) {
        try {
            $pdo = $db->getConnection();

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
                    "SELECT m.id, m.name, m.brand, m.image_url, m.description, c.name AS category, COUNT(i.id) AS exemplaires
                     FROM models m
                     JOIN categories c ON c.id = m.category_id
                     LEFT JOIN items i ON i.model_id = m.id
                     WHERE c.id = :cid
                     GROUP BY m.id"
                );
                $stmt->execute(['cid' => $categoryId]);
            } else {
                $stmt = $pdo->query(
                    "SELECT m.id, m.name, m.brand, m.image_url, m.description, c.name AS category, COUNT(i.id) AS exemplaires
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
    });

    $app->get('/api/outils/{id}', function (Request $request, Response $response, array $args) use ($db) {
        try {
            $id = (int)($args['id'] ?? 0);
            if ($id <= 0) {
                $response->getBody()->write(json_encode([
                    'error' => 'invalid_id',
                    'message' => 'Identifiant invalide'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $pdo = $db->getConnection();
            $stmt = $pdo->prepare(
                "SELECT m.id, m.name, m.brand, m.image_url, m.description, c.name AS category, COUNT(i.id) AS exemplaires
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
    });
};
