<?php
declare(strict_types=1);

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\ports\api\ServiceUserInterface;
use App\domain\exceptions\UserNotFoundException;

class AuthLoginAction
{
    public function __construct(
        private ServiceUserInterface $serviceUser
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            if (empty($email) || empty($password)) {
                $response->getBody()->write(json_encode([
                    'error' => 'missing_fields',
                    'message' => 'Email et mot de passe requis'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $user = $this->serviceUser->authenticate($email, $password);

            if (!$user) {
                $response->getBody()->write(json_encode([
                    'error' => 'invalid_credentials',
                    'message' => 'Email ou mot de passe incorrect'
                ], JSON_UNESCAPED_UNICODE));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json; charset=utf-8');
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                // JWT désactivé: on renvoie un jeton de développement pour le frontend
                'token' => 'dev',
                'user' => $user->toArray()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        } catch (UserNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'user_not_found',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json; charset=utf-8');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'server_error',
                'message' => 'Erreur serveur'
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
    }
}
