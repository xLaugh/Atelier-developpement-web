<?php
declare(strict_types=1);

namespace App\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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

            $token = substr($authHeader, 7);
            
            try {
                $settings = require __DIR__ . '/../../config/Settings.php';
                $jwtConfig = $settings['jwt'];
                $decoded = JWT::decode($token, new Key($jwtConfig['secret'], $jwtConfig['algorithm']));
                
                $response->getBody()->write(json_encode([
                    'user' => [
                        'id' => $decoded->data->id,
                        'prenom' => $decoded->data->prenom,
                        'nom' => $decoded->data->nom,
                        'email' => $decoded->data->email
                    ]
                ], JSON_UNESCAPED_UNICODE));
                
            } catch (Exception $e) {
                $response->getBody()->write(json_encode([
                    'error' => 'invalid_token',
                    'message' => 'Token invalide ou expiré'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');

        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'server_error',
                'message' => 'Erreur serveur'
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
    }
}
