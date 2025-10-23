<?php

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\services\ServiceUser;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class GetUserReservationsAction
{
    private $serviceUser;

    public function __construct(ServiceUser $serviceUser)
    {
        $this->serviceUser = $serviceUser;
    }

    public function handle(Request $request, Response $response): Response
    {
        try {
            // Récupérer le token JWT depuis l'en-tête Authorization
            $authHeader = $request->getHeaderLine('Authorization');
            if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                $response->getBody()->write(json_encode([
                    'error' => 'token_missing',
                    'message' => 'Token d\'authentification manquant'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $token = $matches[1];
            
            // Décoder le token JWT
            $settings = require __DIR__ . '/../../config/settings.php';
            $decoded = JWT::decode($token, new Key($settings['jwt']['secret'], $settings['jwt']['algorithm']));
            
            $userId = $decoded->user_id;
            
            // Récupérer les réservations de l'utilisateur
            $reservations = $this->serviceUser->getUserReservations($userId);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'reservations' => $reservations
            ], JSON_UNESCAPED_UNICODE));
            
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
            
        } catch (\Firebase\JWT\ExpiredException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'token_expired',
                'message' => 'Token expiré'
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json; charset=utf-8');
            
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'token_invalid',
                'message' => 'Token invalide'
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json; charset=utf-8');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'server_error',
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
    }
}
