<?php
declare(strict_types=1);

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\infrastructure\repositories\PDOLogRepository;

class GetUserLogsAction
{
    public function __construct(
        private PDOLogRepository $logRepository
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            // Pour l'instant, on utilise l'utilisateur ID 1 (à remplacer par l'authentification)
            $userId = 1;
            
            $logs = $this->logRepository->getLogsByUserId($userId);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'logs' => $logs
            ], JSON_UNESCAPED_UNICODE));
            
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'server_error',
                'message' => 'Erreur lors du chargement des logs: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
    }
}
