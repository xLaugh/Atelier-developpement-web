<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app, $db) {
    $app->get('/api/outils', function (Request $request, Response $response) use ($db) {
        try {
            $pdo = $db->getConnection();
            $stmt = $pdo->query("
                SELECT m.id, m.name, m.brand, m.description, c.name AS category, COUNT(i.id) AS exemplaires
                FROM models m
                JOIN categories c ON c.id = m.category_id
                LEFT JOIN items i ON i.model_id = m.id
                GROUP BY m.id
            ");
            $outils = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response->getBody()->write(json_encode($outils));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });
};
