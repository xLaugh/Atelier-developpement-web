<?php
declare(strict_types=1);

namespace App\actions;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthMeAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $authHeader = $request->getHeaderLine('Authorization');
            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                $response->getBody()->write(json_encode([
                    'error' => 'no_token',
                    'message' => 'Token manquant'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            // Mode dev sans JWT: renvoyer un user mock si token présent
            $response->getBody()->write(json_encode([
                'user' => [
                    'id' => 1,
                    'prenom' => 'Test',
                    'nom' => 'User',
                    'email' => 'test@example.com'
                ]
            ], JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'server_error',
                'message' => 'Erreur serveur'
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
    }
}
